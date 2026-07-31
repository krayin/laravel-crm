<?php

namespace Webkul\GoogleContact\Jobs\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\GoogleContact\Models\ContactExportBatch;
use Webkul\GoogleContact\Models\ContactExportBatchItem;
use Webkul\GoogleContact\Repositories\ContactExportBatchItemRepository;
use Webkul\GoogleContact\Repositories\ContactExportBatchRepository;
use Webkul\GoogleContact\Repositories\GoogleContactAccountRepository;
use Webkul\GoogleContact\Services\GoogleContactsService;

class PrepareExportBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $batchId,
        protected array $personIds,
    ) {}

    public function handle(
        ContactExportBatchRepository $batchRepository,
        ContactExportBatchItemRepository $batchItemRepository,
        GoogleContactAccountRepository $googleContactAccountRepository,
        GoogleContactsService $googleContactsService,
        PersonRepository $personRepository,
    ): void {
        $batch = $batchRepository->find($this->batchId);

        $account = $googleContactAccountRepository->findOneByField('user_id', $batch->user_id);

        if (! $account) {
            $batchRepository->update([
                'state' => ContactExportBatch::STATE_FAILED,
                'completed_at' => now(),
            ], $batch->id);

            return;
        }

        $batchRepository->update([
            'state' => ContactExportBatch::STATE_FETCHING_EXISTING,
            'started_at' => now(),
        ], $batch->id);

        $existingEmails = $googleContactsService->listExistingEmails($account);

        $persons = $personRepository->findWhereIn('id', $this->personIds);

        $now = now();
        $rows = [];
        $duplicateCount = 0;

        foreach ($persons as $person) {
            $firstEmail = $person->emails[0] ?? null;
            $email = is_array($firstEmail) ? ($firstEmail['value'] ?? null) : $firstEmail;
            $email = $email ? strtolower($email) : null;

            $isDuplicate = $email && in_array($email, $existingEmails);

            if ($isDuplicate) {
                $duplicateCount++;
            }

            $rows[] = [
                'batch_id' => $batch->id,
                'person_id' => $person->id,
                'status' => $isDuplicate ? ContactExportBatchItem::STATUS_DUPLICATE : ContactExportBatchItem::STATUS_PENDING,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows) {
            $batchItemRepository->getModel()->insert($rows);
        }

        $batchRepository->update([
            'total_count' => count($persons),
            'duplicate_count' => $duplicateCount,
        ], $batch->id);

        $pendingItemIds = $batchItemRepository->findWhere([
            ['batch_id', '=', $batch->id],
            ['status', '=', ContactExportBatchItem::STATUS_PENDING],
        ])->pluck('id')->all();

        if (! $pendingItemIds) {
            CompleteExportBatch::dispatch($batch->id);

            return;
        }

        $batchRepository->update([
            'state' => ContactExportBatch::STATE_PROCESSING,
        ], $batch->id);

        $chunkJobs = array_map(
            fn (array $itemIds) => new ExportChunk($batch->id, $itemIds),
            array_chunk($pendingItemIds, GoogleContactsService::BATCH_LIMIT)
        );

        Bus::batch($chunkJobs)
            ->then(fn () => CompleteExportBatch::dispatch($batch->id))
            ->catch(fn () => CompleteExportBatch::dispatch($batch->id))
            ->dispatch();
    }
}
