<?php

namespace Webkul\DataTransfer\Helpers\Importers\Leads;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Contracts\Validations\Decimal;
use Webkul\DataTransfer\Contracts\ImportBatch as ImportBatchContract;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Helpers\Importers\AbstractImporter;
use Webkul\DataTransfer\Repositories\ImportBatchRepository;
use Webkul\Lead\Repositories\LeadRepository;

class Importer extends AbstractImporter
{
    /**
     * Error code for non existing id.
     */
    const ERROR_ID_NOT_FOUND_FOR_DELETE = 'id_not_found_to_delete';

    /**
     * Permanent entity columns.
     */
    protected array $validColumnNames = [
        'id',
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
        self::ERROR_ID_NOT_FOUND_FOR_DELETE => 'data_transfer::app.importers.leads.validation.errors.id-not-found',
    ];

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $permanentAttributes = ['id'];

    /**
     * Permanent entity column.
     */
    protected string $masterAttributeCode = 'id';

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
            if (! $this->isIdExist($rowData['id'])) {
                $this->skipRow($rowNumber, self::ERROR_ID_NOT_FOUND_FOR_DELETE, 'id');

                return false;
            }

            return true;
        }

        /**
         * Validate leads attributes.
         */
        $validator = Validator::make($rowData, [
            ...$this->getValidationRules('leads|persons', $rowData),
            'products'              => 'array',
            'products.*.product_id' => 'sometimes|required|exists:products,id',
            'products.*.name'       => 'required_with:products.*.product_id',
            'products.*.price'      => 'required_with:products.*.product_id',
            'products.*.quantity'   => 'required_with:products.*.product_id',
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
     * Get validation rules.
     */
    public function getValidationRules(string $entityTypes, array $rowData): array
    {
        $rules = [];

        foreach (explode('|', $entityTypes) as $entityType) {
            $attributes = $this->attributeRepository->scopeQuery(fn ($query) => $query->whereIn('code', array_keys($rowData))->where('entity_type', $entityType))->get();

            foreach ($attributes as $attribute) {
                if ($entityType == 'persons') {
                    $attribute->code = 'person.'.$attribute->code;
                }

                $validations = [];

                if ($attribute->type == 'boolean') {
                    continue;
                } elseif ($attribute->type == 'address') {
                    if (! $attribute->is_required) {
                        continue;
                    }

                    $validations = [
                        $attribute->code.'.address'  => 'required',
                        $attribute->code.'.country'  => 'required',
                        $attribute->code.'.state'    => 'required',
                        $attribute->code.'.city'     => 'required',
                        $attribute->code.'.postcode' => 'required',
                    ];
                } elseif ($attribute->type == 'email') {
                    $validations = [
                        $attribute->code              => [$attribute->is_required ? 'required' : 'nullable'],
                        $attribute->code.'.*.value'   => [$attribute->is_required ? 'required' : 'nullable', 'email'],
                        $attribute->code.'.*.label'   => $attribute->is_required ? 'required' : 'nullable',
                    ];
                } elseif ($attribute->type == 'phone') {
                    $validations = [
                        $attribute->code              => [$attribute->is_required ? 'required' : 'nullable'],
                        $attribute->code.'.*.value'   => [$attribute->is_required ? 'required' : 'nullable'],
                        $attribute->code.'.*.label'   => $attribute->is_required ? 'required' : 'nullable',
                    ];
                } else {
                    $validations[$attribute->code] = [$attribute->is_required ? 'required' : 'nullable'];

                    if ($attribute->type == 'text' && $attribute->validation) {
                        array_push($validations[$attribute->code],
                            $attribute->validation == 'decimal'
                            ? new Decimal
                            : $attribute->validation
                        );
                    }

                    if ($attribute->type == 'price') {
                        array_push($validations[$attribute->code], new Decimal);
                    }
                }

                if ($attribute->is_unique) {
                    array_push($validations[in_array($attribute->type, ['email', 'phone'])
                        ? $attribute->code.'.*.value'
                        : $attribute->code
                    ], function ($field, $value, $fail) use ($attribute) {
                        if (! $this->attributeValueRepository->isValueUnique(
                            null,
                            $attribute->entity_type,
                            $attribute,
                            request($field)
                        )
                        ) {
                            $fail(trans('data_transfer::app.validation.errors.already-exists', ['attribute' => $attribute->name]));
                        }
                    });
                }

                $rules = [
                    ...$rules,
                    ...$validations,
                ];
            }
        }

        return $rules;
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
         * Load leads storage with batch ids.
         */
        $this->leadsStorage->load(Arr::pluck($batch->data, 'id'));

        $idsToDelete = [];

        foreach ($batch->data as $rowData) {
            if (! $this->isIdExist($rowData['id'])) {
                continue;
            }

            $idsToDelete[] = $this->leadsStorage->get($rowData['id']);
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
        $this->leadsStorage->load(Arr::pluck($batch->data, 'id'));

        $leads = [];

        /**
         * Prepare leads for import.
         */
        foreach ($batch->data as $rowData) {
            if (
                ! empty($rowData['id'])
                && $this->isIdExist($rowData['id'])
            ) {
                $leads['update'][$rowData['id']] = $rowData;
            } else {
                $leads['insert'][] = [
                    ...Arr::except($rowData, ['id']),
                    'created_at' => $rowData['created_at'] ?? now(),
                    'updated_at' => $rowData['updated_at'] ?? now(),
                ];
            }
        }

        if (! empty($leads['update'])) {
            $this->updatedItemsCount += count($leads['update']);

            $this->leadRepository->upsert(
                $leads['update'],
                $this->masterAttributeCode
            );
        }

        if (! empty($leads['insert'])) {
            $this->createdItemsCount += count($leads['insert']);

            $this->leadRepository->insert($leads['insert']);
        }

        return true;
    }

    /**
     * Check if id exists.
     */
    public function isIdExist(string $id): bool
    {
        return $this->leadsStorage->has($id);
    }

    /**
     * Prepare row data to save into the database.
     */
    protected function prepareRowForDb(array $rowData): array
    {
        return parent::prepareRowForDb($rowData);
    }
}
