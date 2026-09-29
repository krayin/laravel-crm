<?php

namespace Webkul\Admin\Http\Controllers\Contact\Persons;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Webkul\Admin\DataGrids\Contact\PersonDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\GoogleContact\Jobs\Export\PrepareExportBatch;
use Webkul\GoogleContact\Models\ContactExportBatch;
use Webkul\GoogleContact\Repositories\ContactExportBatchRepository;
use Webkul\GoogleContact\Repositories\GoogleContactAccountRepository;

class GoogleContactExportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected GoogleContactAccountRepository $googleContactAccountRepository,
        protected ContactExportBatchRepository $contactExportBatchRepository,
    ) {}

    /**
     * Kick off an async export of every person matching the current datagrid
     * filters/sort (mirrors the CSV/XLS export, not a row-selection action)
     * to the current user's connected Google account.
     */
    public function store(Request $request, PersonDataGrid $personDataGrid): JsonResponse
    {
        $request->validate([
            'filters' => ['sometimes', 'array'],
            'sort' => ['sometimes', 'array'],
        ]);

        $userId = auth()->guard('user')->id();

        if (! $this->googleContactAccountRepository->findOneByField('user_id', $userId)) {
            return response()->json([
                'message' => trans('admin::app.contacts.persons.index.datagrid.google-export-not-connected'),
            ], 422);
        }

        $personIds = $personDataGrid->getFilteredQueryBuilder()->pluck('persons.id')->all();

        if (empty($personIds)) {
            return response()->json([
                'message' => trans('admin::app.export.no-records'),
            ], 400);
        }

        $batch = $this->contactExportBatchRepository->create([
            'user_id' => $userId,
            'state' => ContactExportBatch::STATE_PENDING,
            'total_count' => count($personIds),
        ]);

        PrepareExportBatch::dispatch($batch->id, $personIds);

        return response()->json([
            'message' => trans('admin::app.contacts.persons.index.datagrid.google-export-started'),
            'batch_id' => $batch->id,
        ]);
    }

    /**
     * Report progress/result counts for a single export batch, polled by the frontend.
     */
    public function stats(int $batch): JsonResponse
    {
        $exportBatch = $this->contactExportBatchRepository->find($batch);

        if (! $exportBatch || $exportBatch->user_id !== auth()->guard('user')->id()) {
            abort(404);
        }

        return response()->json([
            'state' => $exportBatch->state,
            'finished' => $exportBatch->isFinished(),
            'total_count' => $exportBatch->total_count,
            'exported_count' => $exportBatch->exported_count,
            'duplicate_count' => $exportBatch->duplicate_count,
            'failed_count' => $exportBatch->failed_count,
        ]);
    }
}
