<?php

namespace Webkul\DataTransfer\Helpers\Importers\Leads;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\DataTransfer\Contracts\ImportBatch as ImportBatchContract;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Helpers\Importers\AbstractImporter;
use Webkul\DataTransfer\Repositories\ImportBatchRepository;
use Webkul\Lead\Repositories\LeadRepository;

class Importer extends AbstractImporter
{
    /**
     * Error code for non existing title.
     */
    const ERROR_TITLE_NOT_FOUND_FOR_DELETE = 'title_not_found_to_delete';

    /**
     * Error code for duplicated title.
     */
    const ERROR_DUPLICATE_TITLE = 'duplicated_title';

    /**
     * Permanent entity columns.
     */
    protected array $validColumnNames = [
        'title',
        'description',
        'lead_value',
        'status',
        'lost_reason',
        'closed_at',
        'user_id',
        'person_id',
        'lead_source_id',
        'lead_type_id',
        'lead_pipeline_id',
        'lead_pipeline_stage_id',
        'expected_close_date',
    ];

    /**
     * Error message templates.
     */
    protected array $messages = [
        self::ERROR_TITLE_NOT_FOUND_FOR_DELETE => 'data_transfer::app.importers.leads.validation.errors.title-not-found',
        self::ERROR_DUPLICATE_TITLE            => 'data_transfer::app.importers.leads.validation.errors.duplicate-title',
    ];

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $permanentAttributes = ['title'];

    /**
     * Permanent entity column.
     */
    protected string $masterAttributeCode = 'unique_id';

    /**
     * Titles storage.
     */
    protected array $titles = [];

    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(
        protected ImportBatchRepository $importBatchRepository,
        protected LeadRepository $leadRepository,
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        protected Storage $leadsStorage
    ) {
        parent::__construct(
            $importBatchRepository,
            $attributeRepository,
            $attributeValueRepository,
        );
    }

    /**
     * Initialize leads error templates.
     */
    protected function initErrorMessages(): void
    {
        foreach ($this->messages as $errorCode => $message) {
            $this->errorHelper->addErrorMessage($errorCode, trans($message));
        }

        parent::initErrorMessages();
    }

    /**
     * Validate data.
     */
    public function validateData(): void
    {
        $this->leadsStorage->init();

        parent::validateData();
    }

    /**
     * Validates row.
     */
    public function validateRow(array $rowData, int $rowNumber): bool
    {
        /**
         * If row is already validated than no need for further validation.
         */
        if (isset($this->validatedRows[$rowNumber])) {
            return ! $this->errorHelper->isRowInvalid($rowNumber);
        }

        $this->validatedRows[$rowNumber] = true;

        /**
         * If import action is delete than no need for further validation.
         */
        if ($this->import->action == Import::ACTION_DELETE) {
            if (! $this->isTitleExist($rowData['title'])) {
                $this->skipRow($rowNumber, self::ERROR_TITLE_NOT_FOUND_FOR_DELETE);

                return false;
            }

            return true;
        }

        /**
         * Validate leads attributes.
         */
        $validator = Validator::make($rowData, [
            ...$this->getValidationRules('leads', $rowData),
            ...[
                'user_id'                => 'required|exists:users,id|numeric',
                'person_id'              => 'required|exists:persons,id|numeric',
                'lead_source_id'         => 'required|exists:lead_sources,id|numeric',
                'lead_type_id'           => 'required|exists:lead_types,id|numeric',
                'lead_pipeline_id'       => 'required|exists:lead_pipelines,id|numeric',
                'lead_pipeline_stage_id' => 'nullable|exists:lead_pipeline_stages,id|numeric',
                'expected_close_date'    => 'nullable|date',
            ],
        ]);

        if ($validator->fails()) {
            $failedAttributes = $validator->failed();

            foreach ($validator->errors()->getMessages() as $attributeCode => $message) {
                $errorCode = array_key_first($failedAttributes[$attributeCode] ?? []);

                $this->skipRow($rowNumber, $errorCode, $attributeCode, current($message));
            }
        }

        return ! $this->errorHelper->isRowInvalid($rowNumber);
    }

    /**
     * Start the import process.
     */
    public function importBatch(ImportBatchContract $batch): bool
    {
        Event::dispatch('data_transfer.imports.batch.import.before', $batch);

        if ($batch->import->action == Import::ACTION_DELETE) {
            $this->deleteLeads($batch);
        } else {
            $this->saveLeads($batch);
        }

        /**
         * Update import batch summary.
         */
        $batch = $this->importBatchRepository->update([
            'state' => Import::STATE_PROCESSED,

            'summary'      => [
                'created' => $this->getCreatedItemsCount(),
                'updated' => $this->getUpdatedItemsCount(),
                'deleted' => $this->getDeletedItemsCount(),
            ],
        ], $batch->id);

        Event::dispatch('data_transfer.imports.batch.import.after', $batch);

        return true;
    }

    /**
     * Delete leads from current batch.
     */
    protected function deleteLeads(ImportBatchContract $batch): bool
    {
        /**
         * Load leads storage with batch titles.
         */
        $this->leadsStorage->load(Arr::pluck($batch->data, 'title'));

        $idsToDelete = [];

        foreach ($batch->data as $rowData) {
            if (! $this->isTitleExist($rowData['title'])) {
                continue;
            }

            $idsToDelete[] = $this->leadsStorage->get($rowData['title']);
        }

        $idsToDelete = array_unique($idsToDelete);

        $this->deletedItemsCount = count($idsToDelete);

        $this->leadRepository->deleteWhere([['id', 'IN', $idsToDelete]]);

        return true;
    }

    /**
     * Save leads from current batch.
     */
    protected function saveLeads(ImportBatchContract $batch): bool
    {
        /**
         * Load lead storage with batch unique ids.
         */
        $this->leadsStorage->load(Arr::pluck($batch->data, 'title'));

        $taxRates = [];

        /**
         * Prepare leads for import.
         */
        foreach ($batch->data as $rowData) {
            $rowData['unique_id'] = "{$rowData['user_id']}|{$rowData['person_id']}|{$rowData['lead_source_id']}|{$rowData['lead_type_id']}|{$rowData['lead_pipeline_id']}";

            if ($this->isTitleExist($rowData['title'])) {
                $taxRates['update'][$rowData['title']] = $rowData;
            } else {
                $taxRates['insert'][$rowData['title']] = [
                    ...$rowData,
                    'created_at' => $rowData['created_at'] ?? now(),
                    'updated_at' => $rowData['updated_at'] ?? now(),
                ];
            }
        }

        if (! empty($taxRates['update'])) {
            $this->updatedItemsCount += count($taxRates['update']);

            $this->leadRepository->upsert(
                $taxRates['update'],
                $this->masterAttributeCode
            );
        }

        if (! empty($taxRates['insert'])) {
            $this->createdItemsCount += count($taxRates['insert']);

            $this->leadRepository->insert($taxRates['insert']);
        }

        return true;
    }

    /**
     * Check if title exists.
     */
    public function isTitleExist(string $title): bool
    {
        return $this->leadsStorage->has($title);
    }

    /**
     * Prepare row data to save into the database.
     */
    protected function prepareRowForDb(array $rowData): array
    {
        return parent::prepareRowForDb($rowData);
    }
}
