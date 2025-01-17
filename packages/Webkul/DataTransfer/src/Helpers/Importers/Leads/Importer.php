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
use Webkul\Lead\Repositories\ProductRepository as LeadProductRepository;

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
        'product',
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
    protected $permanentAttributes = ['title'];

    /**
     * Permanent entity column.
     */
    protected string $masterAttributeCode = 'id';

    /**
     * Is linking required
     */
    protected bool $linkingRequired = true;

    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(
        protected ImportBatchRepository $importBatchRepository,
        protected LeadRepository $leadRepository,
        protected LeadProductRepository $leadProductRepository,
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        protected Storage $leadsStorage,
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
                $this->skipRow($rowNumber, self::ERROR_ID_NOT_FOUND_FOR_DELETE, 'id');

                return false;
            }

            return true;
        }

        if (! empty($rowData['product'])) {
            $product = $this->parseProducts($rowData['product']);

            $validator = Validator::make($product, [
                'id'       => 'required|exists:products,id',
                'price'    => 'required',
                'quantity' => 'required',
            ]);

            if ($validator->fails()) {
                $failedAttributes = $validator->failed();

                foreach ($validator->errors()->getMessages() as $attributeCode => $message) {
                    $errorCode = array_key_first($failedAttributes[$attributeCode] ?? []);

                    $this->skipRow($rowNumber, $errorCode, $attributeCode, current($message));
                }
            }
        }

        /**
         * Validate leads attributes.
         */
        $validator = Validator::make($rowData, [
            ...$this->getValidationRules('leads|persons', $rowData),
            'id'                     => 'numeric',
            'status'                 => 'sometimes|required|in:0,1',
            'user_id'                => 'required|exists:users,id',
            'person_id'              => 'required|exists:persons,id',
            'lead_source_id'         => 'required|exists:lead_sources,id',
            'lead_type_id'           => 'required|exists:lead_types,id',
            'lead_pipeline_id'       => 'required|exists:lead_pipelines,id',
            'lead_pipeline_stage_id' => 'required|exists:lead_pipeline_stages,id',
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
     * Prepare row data for lead product.
     */
    protected function parseProducts(?string $products): array
    {
        $productData = [];

        $productArray = explode(',', $products);

        foreach ($productArray as $product) {
            if (empty($product)) {
                continue;
            }

            [$key, $value] = explode('=', $product);

            $productData[$key] = $value;
        }

        if (
            isset($productData['price'])
            && isset($productData['quantity'])
        ) {
            $productData['amount'] = $productData['price'] * $productData['quantity'];
        }

        return $productData;
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
     * Start the products linking process
     */
    public function linkBatch(ImportBatchContract $batch): bool
    {
        Event::dispatch('data_transfer.imports.batch.linking.before', $batch);

        /**
         * Load leads storage with batch ids.
         */
        $this->leadsStorage->load(Arr::pluck($batch->data, 'title'));

        $products = [];

        foreach ($batch->data as $rowData) {
            /**
             * Prepare products.
             */
            $this->prepareProducts($rowData, $products);
        }

        $this->saveProducts($products);

        /**
         * Update import batch summary
         */
        $this->importBatchRepository->update([
            'state' => Import::STATE_LINKED,
        ], $batch->id);

        Event::dispatch('data_transfer.imports.batch.linking.after', $batch);

        return true;
    }

    /**
     * Prepare products.
     */
    public function prepareProducts($rowData, &$product): void
    {
        if (! empty($rowData['product'])) {
            $product[$rowData['title']] = $this->parseProducts($rowData['product']);
        }
    }

    /**
     * Save products.
     */
    public function saveProducts(array $products): void
    {
        $leadProducts = [];

        foreach ($products as $title => $product) {
            $lead = $this->leadsStorage->get($title);

            $leadProducts['insert'][] = [
                'lead_id'    => $lead['id'],
                'product_id' => $product['id'],
                'price'      => $product['price'],
                'quantity'   => $product['quantity'],
                'amount'     => $product['amount'],
            ];
        }

        foreach ($leadProducts['insert'] as $key => $leadProduct) {
            $this->leadProductRepository->deleteWhere([
                'lead_id'    => $leadProduct['lead_id'],
                'product_id' => $leadProduct['product_id'],
            ]);
        }

        $this->leadProductRepository->upsert($leadProducts['insert'], ['lead_id', 'product_id']);
    }

    /**
     * Delete leads from current batch.
     */
    protected function deleteLeads(ImportBatchContract $batch): bool
    {
        /**
         * Load leads storage with batch ids.
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
         * Load lead storage with batch unique title.
         */
        $this->leadsStorage->load(Arr::pluck($batch->data, 'title'));

        $leads = [];

        /**
         * Prepare leads for import.
         */
        foreach ($batch->data as $rowData) {
            if (isset($rowData['id'])) {
                $leads['update'][$rowData['id']] = Arr::except($rowData, ['product']);
            } else {
                $leads['insert'][$rowData['title']] = [
                    ...Arr::except($rowData, ['id', 'product']),
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

            /**
             * Update the sku storage with newly created products
             */
            $newLeads = $this->leadRepository->findWhereIn(
                'title',
                array_keys($leads['insert']),
                [
                    'id',
                    'title',
                ]
            );

            foreach ($newLeads as $lead) {
                $this->leadsStorage->set($lead->title, [
                    'id'    => $lead->id,
                    'title' => $lead->title,
                ]);
            }
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
