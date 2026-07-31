<?php

namespace Webkul\GoogleContact\Jobs\Export;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\GoogleContact\Models\ContactExportBatchItem;
use Webkul\GoogleContact\Repositories\ContactExportBatchItemRepository;
use Webkul\GoogleContact\Repositories\ContactExportBatchRepository;
use Webkul\GoogleContact\Repositories\GoogleContactAccountRepository;
use Webkul\GoogleContact\Services\GoogleContactsService;

class ExportChunk implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $exportBatchId,
        protected array $itemIds,
    ) {}

    public function handle(
        ContactExportBatchRepository $batchRepository,
        ContactExportBatchItemRepository $batchItemRepository,
        GoogleContactAccountRepository $googleContactAccountRepository,
        GoogleContactsService $googleContactsService,
        PersonRepository $personRepository,
    ): void {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $batch = $batchRepository->find($this->exportBatchId);

        $account = $googleContactAccountRepository->findOneByField('user_id', $batch->user_id);

        $items = $batchItemRepository->findWhereIn('id', $this->itemIds);

        if (! $account) {
            $this->markItemsFailed($batchItemRepository, $items, 'Google account is no longer connected.');

            $batchRepository->getModel()->whereKey($batch->id)->increment('failed_count', $items->count());

            return;
        }

        $personsById = $personRepository
            ->findWhereIn('id', $items->pluck('person_id')->all())
            ->keyBy('id');

        $orderedPersons = $items->map(fn ($item) => $personsById->get($item->person_id))->filter()->values();

        try {
            $results = $googleContactsService->batchCreateContacts($account, $orderedPersons->all());
        } catch (\Exception $exception) {
            $this->markItemsFailed($batchItemRepository, $items, $exception->getMessage());

            $batchRepository->getModel()->whereKey($batch->id)->increment('failed_count', $items->count());

            return;
        }

        $exportedCount = 0;
        $failedCount = 0;

        foreach ($orderedPersons->values() as $index => $person) {
            $item = $items->firstWhere('person_id', $person->id);

            $result = $results[$index] ?? ['success' => false, 'resource_name' => null, 'error' => 'No response from Google.'];

            if ($result['success']) {
                $exportedCount++;

                $batchItemRepository->update([
                    'status' => ContactExportBatchItem::STATUS_EXPORTED,
                    'google_resource_name' => $result['resource_name'],
                ], $item->id);
            } else {
                $failedCount++;

                $batchItemRepository->update([
                    'status' => ContactExportBatchItem::STATUS_FAILED,
                    'error_message' => $result['error'],
                ], $item->id);
            }
        }

        $batchRepository->getModel()->whereKey($batch->id)->increment('exported_count', $exportedCount);
        $batchRepository->getModel()->whereKey($batch->id)->increment('failed_count', $failedCount);
    }

    /**
     * Mark every given item as failed with the same error message, e.g. when
     * the whole chunk couldn't be sent to Google at all.
     */
    protected function markItemsFailed(ContactExportBatchItemRepository $batchItemRepository, $items, string $message): void
    {
        foreach ($items as $item) {
            $batchItemRepository->update([
                'status' => ContactExportBatchItem::STATUS_FAILED,
                'error_message' => $message,
            ], $item->id);
        }
    }
}
