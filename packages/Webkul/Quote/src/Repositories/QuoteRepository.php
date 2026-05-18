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

        foreach ($data['items'] as $itemData) {
            $this->quoteItemRepository->create(array_merge($itemData, [
                'quote_id' => $quote->id,
            ]));
        }

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

        return $quote;
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
