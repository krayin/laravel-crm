<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting\DataTransfer;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Repositories\ImportRepository;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\ImportResource;

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
    public function index(): JsonResource
    {
        $imports = $this->allResources($this->importRepository);

        return ImportResource::collection($imports);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResource
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

        return new JsonResource([
            'data'    => [
                'id' => $import->id,
            ],
            'message' => trans('rest-api::app.settings.data-transfer.imports.create-success'),
        ]);
    }

    /**
     * Show the specified Resource.
     */
    public function show(int $id): ImportResource
    {
        $import = $this->importRepository->findOrFail($id);

        return new ImportResource($import);
    }

    /**
     * Update a resource in storage.
     */
    public function update(int $id): JsonResource
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

        return new JsonResource([
            'data'    => [
                'id' => $import->id,
            ],
            'message' => trans('rest-api::app.settings.data-transfer.imports.update-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResource
    {
        $import = $this->importRepository->findOrFail($id);

        try {
            Storage::disk('public')->delete($import->file_path);

            Storage::disk('public')->delete($import->error_file_path ?? '');

            $this->importRepository->delete($id);

            return new JsonResource([
                'message' => trans('rest-api::app.settings.data-transfer.imports.delete-success'),
            ]);
        } catch (\Exception $e) {
        }

        return new JsonResource([
            'message' => trans('rest-api::app.settings.data-transfer.imports.delete-failed'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function validateImport(int $id): \Illuminate\Http\JsonResponse
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
                'message' => trans('rest-api::app.settings.data-transfer.imports.nothing-to-import'),
            ], 400);
        }

        $this->importHelper->setImport($import);

        if (! $this->importHelper->isValid()) {
            return new JsonResponse([
                'message' => trans('rest-api::app.settings.data-transfer.imports.not-valid'),
            ], 400);
        }

        if (
            $import->process_in_queue
            && config('queue.default') == 'sync'
        ) {
            return new JsonResponse([
                'message' => trans('rest-api::app.settings.data-transfer.imports.setup-queue-error'),
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
    public function downloadSample(string $type): StreamedResponse
    {
        $importer = config('importers.'.$type);

        return Storage::download($importer['sample_path']);
    }

    /**
     * Download import error report
     */
    public function downloadErrorReport(int $id): StreamedResponse
    {
        $import = $this->importRepository->findOrFail($id);

        return Storage::disk('public')->download($import->file_path);
    }
}
