<?php

namespace Webkul\Admin\Http\Controllers\Settings\DataTransfer;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\DataTransfer\ImportDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Repositories\ImportRepository;

class ImportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ImportRepository $importRepository,
        protected Import $importHelper
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(ImportDataGrid::class)->process();
        }

        return view('admin::settings.data-transfer.imports.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::settings.data-transfer.imports.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        $importers = array_keys(config('importers'));

        $this->validate(request(), [
            'type'                => 'required|in:'.implode(',', $importers),
            'action'              => 'required:in:append,delete',
            'validation_strategy' => 'required:in:stop-on-errors,skip-errors',
            'allowed_errors'      => 'required|integer|min:0',
            'field_separator'     => 'required',
            'file'                => 'required|mimes:csv,xls,xlsx,txt',
        ]);

        Event::dispatch('data_transfer.imports.create.before');

        $data = request()->only([
            'type',
            'action',
            'process_in_queue',
            'validation_strategy',
            'validation_strategy',
            'allowed_errors',
            'field_separator',
        ]);

        if (! isset($data['process_in_queue'])) {
            $data['process_in_queue'] = false;
        } else {
            $data['process_in_queue'] = true;
        }

        $import = $this->importRepository->create(
            array_merge(
                [
                    'file_path' => request()->file('file')->storeAs(
                        'imports',
                        time().'-'.request()->file('file')->getClientOriginalName(),
                        'public'
                    ),
                ],
                $data
            )
        );

        Event::dispatch('data_transfer.imports.create.after', $import);

        session()->flash('success', trans('admin::app.settings.data-transfer.imports.create-success'));

        return redirect()->route('admin.settings.data_transfer.imports.import', $import->id);
    }

    /**
     * Show the form for editing a new resource.
     */
    public function edit(int $id): View
    {
        $import = $this->importRepository->findOrFail($id);

        return view('admin::settings.data-transfer.imports.edit', compact('import'));
    }

    /**
     * Update a resource in storage.
     */
    public function update(int $id): RedirectResponse
    {
        $importers = array_keys(config('importers'));

        $import = $this->importRepository->findOrFail($id);

        $this->validate(request(), [
            'type'                => 'required|in:'.implode(',', $importers),
            'action'              => 'required:in:append,delete',
            'validation_strategy' => 'required:in:stop-on-errors,skip-errors',
            'allowed_errors'      => 'required|integer|min:0',
            'field_separator'     => 'required',
            'file'                => 'mimes:csv,xls,xlsx,txt',
        ]);

        Event::dispatch('data_transfer.imports.update.before');

        $data = array_merge(
            request()->only([
                'type',
                'action',
                'process_in_queue',
                'validation_strategy',
                'validation_strategy',
                'allowed_errors',
                'field_separator',
            ]),
            [
                'state'                => 'pending',
                'processed_rows_count' => 0,
                'invalid_rows_count'   => 0,
                'errors_count'         => 0,
                'errors'               => null,
                'error_file_path'      => null,
                'started_at'           => null,
                'completed_at'         => null,
                'summary'              => null,
            ]
        );

        Storage::disk('public')->delete($import->error_file_path ?? '');

        if (request()->file('file') && request()->file('file')->isValid()) {
            Storage::disk('public')->delete($import->file_path);

            $data['file_path'] = request()->file('file')->storeAs(
                'imports',
                time().'-'.request()->file('file')->getClientOriginalName(),
                'public'
            );
        }

        if (! isset($data['process_in_queue'])) {
            $data['process_in_queue'] = false;
        }

        $import = $this->importRepository->update($data, $import->id);

        Event::dispatch('data_transfer.imports.update.after', $import);

        session()->flash('success', trans('admin::app.settings.data-transfer.imports.update-success'));

        return redirect()->route('admin.settings.data_transfer.imports.import', $import->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $import = $this->importRepository->findOrFail($id);

        try {
            Storage::disk('public')->delete($import->file_path);

            Storage::disk('public')->delete($import->error_file_path ?? '');

            $this->importRepository->delete($id);

            return response()->json([
                'message' => trans('admin::app.settings.data-transfer.imports.delete-success'),
            ]);
        } catch (\Exception $e) {
        }

        return response()->json([
            'message' => trans('admin::app.settings.data-transfer.imports.delete-failed'),
        ], 500);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function import(int $id): View
    {
        $import = $this->importRepository->findOrFail($id);

        $isValid = $this->importHelper
            ->setImport($import)
            ->isValid();

        if ($import->state == Import::STATE_LINKING) {
            if ($this->importHelper->isIndexingRequired()) {
                $state = Import::STATE_INDEXING;
            } else {
                $state = Import::STATE_COMPLETED;
            }
        } elseif ($import->state == Import::STATE_INDEXING) {
            $state = Import::STATE_COMPLETED;
        } else {
            $state = Import::STATE_COMPLETED;
        }

        $stats = $this->importHelper->stats($state);

        $import->unsetRelations();

        return view('admin::settings.data-transfer.imports.import', compact('import', 'isValid', 'stats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function validateImport(int $id): JsonResponse
    {
        $import = $this->importRepository->findOrFail($id);

        $isValid = $this->importHelper
            ->setImport($import)
            ->validate();

        return new JsonResponse([
            'is_valid' => $isValid,
            'import'   => $this->importHelper->getImport()->unsetRelations(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function start(int $id): JsonResponse
    {
        $import = $this->importRepository->findOrFail($id);

        if (! $import->processed_rows_count) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.data-transfer.imports.nothing-to-import'),
            ], 400);
        }

        $this->importHelper->setImport($import);

        if (! $this->importHelper->isValid()) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.data-transfer.imports.not-valid'),
            ], 400);
        }

        if (
            $import->process_in_queue
            && config('queue.default') == 'sync'
        ) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.data-transfer.imports.setup-queue-error'),
            ], 400);
        }

        /**
         * Set the import state to processing
         */
        if ($import->state == Import::STATE_VALIDATED) {
            $this->importHelper->started();
        }

        /**
         * Get the first pending batch to import
         */
        $importBatch = $import->batches->where('state', Import::STATE_PENDING)->first();

        if ($importBatch) {
            /**
             * Start the import process
             */
            try {
                if ($import->process_in_queue) {
                    $this->importHelper->start();
                } else {
                    $this->importHelper->start($importBatch);
                }
            } catch (\Exception $e) {
                return new JsonResponse([
                    'message' => $e->getMessage(),
                ], 400);
            }
        } else {
            if ($this->importHelper->isLinkingRequired()) {
                $this->importHelper->linking();
            } elseif ($this->importHelper->isIndexingRequired()) {
                $this->importHelper->indexing();
            } else {
                $this->importHelper->completed();
            }
        }

        return new JsonResponse([
            'stats'  => $this->importHelper->stats(Import::STATE_PROCESSED),
            'import' => $this->importHelper->getImport()->unsetRelations(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function link(int $id): JsonResponse
    {
        $import = $this->importRepository->findOrFail($id);

        if (! $import->processed_rows_count) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.data-transfer.imports.nothing-to-import'),
            ], 400);
        }

        $this->importHelper->setImport($import);

        if (! $this->importHelper->isValid()) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.data-transfer.imports.not-valid'),
            ], 400);
        }

        /**
         * Set the import state to linking
         */
        if ($import->state == Import::STATE_PROCESSED) {
            $this->importHelper->linking();
        }

        /**
         * Get the first processing batch to link
         */
        $importBatch = $import->batches->where('state', Import::STATE_PROCESSED)->first();

        /**
         * Set the import state to linking/completed
         */
        if ($importBatch) {
            /**
             * Start the resource linking process
             */
            try {
                $this->importHelper->link($importBatch);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'message' => $e->getMessage(),
                ], 400);
            }
        } else {
            if ($this->importHelper->isIndexingRequired()) {
                $this->importHelper->indexing();
            } else {
                $this->importHelper->completed();
            }
        }

        return new JsonResponse([
            'stats'  => $this->importHelper->stats(Import::STATE_LINKED),
            'import' => $this->importHelper->getImport()->unsetRelations(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function indexData(int $id): JsonResponse
    {
        $import = $this->importRepository->findOrFail($id);

        if (! $import->processed_rows_count) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.data-transfer.imports.nothing-to-import'),
            ], 400);
        }

        $this->importHelper->setImport($import);

        if (! $this->importHelper->isValid()) {
            return new JsonResponse([
                'message' => trans('admin::app.settings.data-transfer.imports.not-valid'),
            ], 400);
        }

        /**
         * Set the import state to linking
         */
        if ($import->state == Import::STATE_LINKED) {
            $this->importHelper->indexing();
        }

        /**
         * Get the first processing batch to link
         */
        $importBatch = $import->batches->where('state', Import::STATE_LINKED)->first();

        /**
         * Set the import state to linking/completed
         */
        if ($importBatch) {
            /**
             * Start the resource linking process
             */
            try {
                $this->importHelper->index($importBatch);
            } catch (\Exception $e) {
                return new JsonResponse([
                    'message' => $e->getMessage(),
                ], 400);
            }
        } else {
            /**
             * Set the import state to completed
             */
            $this->importHelper->completed();
        }

        return new JsonResponse([
            'stats'  => $this->importHelper->stats(Import::STATE_INDEXED),
            'import' => $this->importHelper->getImport()->unsetRelations(),
        ]);
    }

    /**
     * Returns import stats
     */
    public function stats(int $id, string $state = Import::STATE_PROCESSED): JsonResponse
    {
        $import = $this->importRepository->findOrFail($id);

        $stats = $this->importHelper
            ->setImport($import)
            ->stats($state);

        return new JsonResponse([
            'stats'  => $stats,
            'import' => $this->importHelper->getImport()->unsetRelations(),
        ]);
    }

    /**
     * Download import error report
     */
    public function downloadSample(string $type)
    {
        $importer = config('importers.'.$type);

        return Storage::download($importer['sample_path']);
    }

    /**
     * Download import error report
     */
    public function download(int $id)
    {
        $import = $this->importRepository->findOrFail($id);

        return Storage::disk('public')->download($import->file_path);
    }

    /**
     * Download import error report
     */
    public function downloadErrorReport(int $id)
    {
        $import = $this->importRepository->findOrFail($id);

        return Storage::disk('public')->download($import->file_path);
    }
}
