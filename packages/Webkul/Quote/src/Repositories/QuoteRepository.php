<?php

namespace Webkul\Quote\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\Attribute\Repositories\AttributeValueRepository;

class QuoteRepository extends Repository
{
    /**
     * AttributeValueRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeValueRepository
     */
    protected $attributeValueRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeValueRepository  $attributeValueRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        AttributeValueRepository $attributeValueRepository,
        Container $container
    )
    {
        $this->attributeValueRepository = $attributeValueRepository;

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
        $quote = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

        return $quote;
    }
}