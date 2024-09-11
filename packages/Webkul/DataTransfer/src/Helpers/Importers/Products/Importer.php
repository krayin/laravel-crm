<?php

namespace Webkul\DataTransfer\Helpers\Importers\Products;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\DataTransfer\Contracts\ImportBatch as ImportBatchContract;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Helpers\Importers\AbstractImporter;
use Webkul\DataTransfer\Repositories\ImportBatchRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class Importer extends AbstractImporter
{
    /**
     * Error code for non existing SKU.
     */
    const ERROR_SKU_NOT_FOUND_FOR_DELETE = 'sku_not_found_to_delete';

    /**
     * Error message templates.
     */
    protected array $messages = [
        self::ERROR_SKU_NOT_FOUND_FOR_DELETE  => 'data_transfer::app.importers.products.validation.errors.sku-not-found',
    ];

    /**
     * Permanent entity columns.
     */
    protected array $permanentAttributes = ['sku'];

    /**
     * Permanent entity column.
     */
    protected string $masterAttributeCode = 'sku';

    /**
     * Cached attributes.
     */
    protected mixed $attributes = [];

    /**
     * Valid csv columns.
     */
    protected array $validColumnNames = [
        'sku',
        'name',
        'description',
        'quantity',
        'price',
    ];

    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(
        protected ImportBatchRepository $importBatchRepository,
        protected AttributeRepository $attributeRepository,
        protected AttributeOptionRepository $attributeOptionRepository,
        protected ProductRepository $productRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected AttributeValueRepository $attributeValueRepository,
        protected SKUStorage $skuStorage
    ) {
        parent::__construct(
            $importBatchRepository,
            $attributeRepository,
            $attributeValueRepository
        );

        $this->initAttributes();
    }

    /**
     * Load all attributes and families to use later.
     */
    protected function initAttributes(): void
    {
        $this->attributes = $this->attributeRepository->all();

        foreach ($this->attributes as $attribute) {
            $this->validColumnNames[] = $attribute->code;
        }
    }

    /**
     * Initialize Product error templates.
     */
    protected function initErrorMessages(): void
    {
        foreach ($this->messages as $errorCode => $message) {
            $this->errorHelper->addErrorMessage($errorCode, trans($message));
        }

        parent::initErrorMessages();
    }

    /**
     * Save validated batches.
     */
    protected function saveValidatedBatches(): self
    {
        $source = $this->getSource();

        $source->rewind();

        $this->skuStorage->init();

        while ($source->valid()) {
            try {
                $rowData = $source->current();
            } catch (\InvalidArgumentException $e) {
                $source->next();

                continue;
            }

            $this->validateRow($rowData, $source->getCurrentRowNumber());

            $source->next();
        }

        parent::saveValidatedBatches();

        return $this;
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
            if (! $this->isSKUExist($rowData['sku'])) {
                $this->skipRow($rowNumber, self::ERROR_SKU_NOT_FOUND_FOR_DELETE, 'sku');

                return false;
            }

            return true;
        }

        /**
         * Validate product attributes
         */
        $validator = Validator::make($rowData, $this->getValidationRules('products', $rowData));

        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $attributeCode => $message) {
                $failedAttributes = $validator->failed();

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
            $this->deleteProducts($batch);
        } else {
            $this->saveProductsData($batch);
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
     * Delete products from current batch.
     */
    protected function deleteProducts(ImportBatchContract $batch): bool
    {
        /**
         * Load SKU storage with batch skus.
         */
        $this->skuStorage->load(Arr::pluck($batch->data, 'sku'));

        $idsToDelete = [];

        foreach ($batch->data as $rowData) {
            if (! $this->isSKUExist($rowData['sku'])) {
                continue;
            }

            $product = $this->skuStorage->get($rowData['sku']);

            $idsToDelete[] = $product['id'];
        }

        $idsToDelete = array_unique($idsToDelete);

        $this->deletedItemsCount = count($idsToDelete);

        $this->productRepository->deleteWhere([['id', 'IN', $idsToDelete]]);

        return true;
    }

    /**
     * Save products from current batch.
     */
    protected function saveProductsData(ImportBatchContract $batch): bool
    {
        /**
         * Load SKU storage with batch skus.
         */
        $this->skuStorage->load(Arr::pluck($batch->data, 'sku'));

        $products = [];

        /**
         * Prepare products for import.
         */
        foreach ($batch->data as $rowData) {
            $this->prepareProducts($rowData, $products);
        }

        $this->saveProducts($products);

        return true;
    }

    /**
     * Prepare products from current batch.
     */
    public function prepareProducts(array $rowData, array &$products): void
    {
        if ($this->isSKUExist($rowData['sku'])) {
            $products['update'][$rowData['sku']] = $rowData;
        } else {
            $products['insert'][$rowData['sku']] = [
                ...$rowData,
                'created_at' => $rowData['created_at'] ?? now(),
                'updated_at' => $rowData['updated_at'] ?? now(),
            ];
        }
    }

    /**
     * Save products from current batch.
     */
    public function saveProducts(array $products): void
    {
        if (! empty($products['update'])) {
            $this->updatedItemsCount += count($products['update']);

            $this->productRepository->upsert(
                $products['update'],
                $this->masterAttributeCode
            );
        }

        if (! empty($products['insert'])) {
            $this->createdItemsCount += count($products['insert']);

            $this->productRepository->insert($products['insert']);
        }
    }

    /**
     * Save channels from current batch.
     */
    public function saveChannels(array $channels): void
    {
        $productChannels = [];

        foreach ($channels as $sku => $channelIds) {
            $product = $this->skuStorage->get($sku);

            foreach (array_unique($channelIds) as $channelId) {
                $productChannels[] = [
                    'product_id' => $product['id'],
                    'channel_id' => $channelId,
                ];
            }
        }

        DB::table('product_channels')->upsert(
            $productChannels,
            [
                'product_id',
                'channel_id',
            ],
        );
    }

    /**
     * Save links.
     */
    public function loadUnloadedSKUs(array $skus): void
    {
        $notLoadedSkus = [];

        foreach ($skus as $sku) {
            if ($this->skuStorage->has($sku)) {
                continue;
            }

            $notLoadedSkus[] = $sku;
        }

        /**
         * Load not loaded SKUs to the sku storage.
         */
        if (! empty($notLoadedSkus)) {
            $this->skuStorage->load($notLoadedSkus);
        }
    }

    /**
     * Check if SKU exists.
     */
    public function isSKUExist(string $sku): bool
    {
        return $this->skuStorage->has($sku);
    }

    /**
     * Prepare row data to save into the database.
     */
    protected function prepareRowForDb(array $rowData): array
    {
        return parent::prepareRowForDb($rowData);
    }
}
