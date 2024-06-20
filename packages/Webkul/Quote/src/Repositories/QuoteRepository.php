<?php

namespace Webkul\Quote\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Core\Eloquent\Repository;
use Webkul\Attribute\Repositories\AttributeValueRepository;

class QuoteRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeValueRepository $attributeValueRepository,
        protected QuoteItemRepository $quoteItemRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Quote\Contracts\Quote';
    }

    /**
     * @param array $data
     * @return \Webkul\Quote\Contracts\Quote
     */
    public function create(array $data)
    {
        $quote = parent::create($data);

        $this->attributeValueRepository->save($data, $quote->id);

        foreach ($data['items'] as $itemData) {
            $this->quoteItemRepository->create(array_merge($itemData, [
                'quote_id' => $quote->id,
            ]));
        }

        return $quote;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \Webkul\Quote\Contracts\Quote
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $quote = $this->find($id);

        parent::update($data, $id, $attribute);

        $this->attributeValueRepository->save($data, $id);

        if (! isset($data['_method'])) {
            return $quote;
        }

        $previousItemIds = $quote->items->pluck('id');

        if (isset($data['items'])) {
            foreach ($data['items'] as $itemId => $itemData) {
                if (Str::contains($itemId, 'item_')) {
                    $this->quoteItemRepository->create(array_merge($itemData, [
                        'quote_id' => $id,
                    ]));
                } else {
                    if (is_numeric($index = $previousItemIds->search($itemId))) {
                        $previousItemIds->forget($index);
                    }

                    $this->quoteItemRepository->update($itemData, $itemId);
                }
            }
        }

        foreach ($previousItemIds as $itemId) {
            $this->quoteItemRepository->delete($itemId);
        }

        return $quote;
    }

    /**
     * Retrieves customers count based on date
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