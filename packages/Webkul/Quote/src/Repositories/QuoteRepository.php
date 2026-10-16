<?php

namespace Webkul\Quote\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\Lead\Repositories\ProductRepository;
use Webkul\Quote\Contracts\Quote;

class QuoteRepository extends Repository
{
    /**
     * Searchable fields.
     */
    protected $fieldSearchable = [
        'subject',
        'description',
        'person_id',
        'person.name',
        'user_id',
        'user.name',
    ];

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        protected QuoteItemRepository $quoteItemRepository,
        protected ProductRepository $productRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Quote::class;
    }

    /**
     * Create.
     *
     * @return Quote
     */
    public function create(array $data)
    {
        $quote = parent::create($data);

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $quote->id,
        ]));

        /**
         * A quote can be started without its line items — the quick add form collects only the
         * subject, owner and totals, and the products are added on the quote's own page afterwards.
         * `update()` already allows for this; creating one without them threw on a missing key.
         */
        foreach ($data['items'] ?? [] as $itemData) {
            $this->quoteItemRepository->create(array_merge($itemData, [
                'quote_id' => $quote->id,
            ]));
        }

        $this->syncLeadProducts($quote, $data['lead_id'] ?? null);

        return $quote;
    }

    /**
     * Update.
     *
     * @param  int  $id
     * @param  array  $attribute
     * @return Quote
     */
    public function update(array $data, $id, $attributes = [])
    {
        $quote = $this->find($id);

        $originalLeadId = $quote->lead_id;

        parent::update($data, $id);

        /**
         * If attributes are provided then only save the provided attributes and return.
         */
        if (! empty($attributes)) {
            $conditions = ['entity_type' => $data['entity_type']];

            if (isset($data['quick_add'])) {
                $conditions['quick_add'] = 1;
            }

            $attributes = $this->attributeRepository->where($conditions)
                ->whereIn('code', $attributes)
                ->get();

            $this->attributeValueRepository->save(array_merge($data, [
                'entity_id' => $quote->id,
            ]), $attributes);

            return $quote;
        }

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $quote->id,
        ]));

        $previousItemIds = $quote->items->pluck('id');

        if (isset($data['items'])) {
            foreach ($data['items'] as $itemId => $itemData) {
                if (Str::contains($itemId, 'item_')) {
                    $this->quoteItemRepository->create(array_merge($itemData, [
                        'quote_id' => $id,
                    ]));

                    if (! empty($data['lead_id'])) {
                        $this->productRepository->updateOrCreate(
                            [
                                'lead_id' => $data['lead_id'],
                                'product_id' => $itemData['product_id'],
                            ],
                            [
                                'lead_id' => $data['lead_id'],
                                'amount' => $itemData['price'] * $itemData['quantity'],
                                'quantity' => $itemData['quantity'],
                                'price' => $itemData['price'],
                            ]
                        );
                    }
                } else {
                    if (is_numeric($index = $previousItemIds->search($itemId))) {
                        $previousItemIds->forget($index);
                    }

                    $this->quoteItemRepository->update($itemData, $itemId);
                }
            }
        }

        foreach ($previousItemIds as $itemId) {
            if (! empty($originalLeadId)) {
                $deletedItem = $quote->items->firstWhere('id', $itemId);

                if ($deletedItem?->product_id) {
                    $this->productRepository->deleteWhere([
                        'lead_id' => $originalLeadId,
                        'product_id' => $deletedItem->product_id,
                    ]);
                }
            }

            $this->quoteItemRepository->delete($itemId);
        }

        $this->syncLeadProducts($quote, $data['lead_id'] ?? $originalLeadId);

        return $quote;
    }

    /**
     * Sync products between a quote and its lead.
     *
     * @param  Quote  $quote
     * @param  int|null  $leadId
     */
    protected function syncLeadProducts($quote, $leadId): void
    {
        if (empty($leadId)) {
            return;
        }

        $quote = $quote->refresh();

        $quoteProductIds = $quote->items->pluck('product_id')->filter();

        $leadProducts = $this->productRepository->findWhere(['lead_id' => $leadId]);

        foreach ($quote->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            if ($leadProducts->contains('product_id', $item->product_id)) {
                continue;
            }

            $this->productRepository->create([
                'lead_id' => $leadId,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'amount' => $item->price * $item->quantity,
            ]);
        }

        foreach ($leadProducts as $leadProduct) {
            if (! $leadProduct->product_id) {
                continue;
            }

            if ($quoteProductIds->contains($leadProduct->product_id)) {
                continue;
            }

            $quantity = $leadProduct->quantity ?: 1;
            $price = $leadProduct->price ?: 0;

            $this->quoteItemRepository->create([
                'quote_id' => $quote->id,
                'product_id' => $leadProduct->product_id,
                'name' => $leadProduct->product?->name ?? '',
                'sku' => $leadProduct->product?->sku ?? '',
                'quantity' => $quantity,
                'price' => $price,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'total' => $price * $quantity,
            ]);
        }
    }

    /**
     * Retrieves customers count based on date.
     *
     * @return number
     */
    public function getQuotesCount($startDate, $endDate)
    {
        return $this
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->count();
    }
}
