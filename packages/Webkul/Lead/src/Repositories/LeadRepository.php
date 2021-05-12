<?php

namespace Webkul\Lead\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;

class LeadRepository extends Repository
{
    /**
     * PersonRepository object
     *
     * @var \Webkul\Contact\Repositories\PersonRepository
     */
    protected $personRepository;

    /**
     * ProductRepository object
     *
     * @var \Webkul\Lead\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * AttributeValueRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeValueRepository
     */
    protected $attributeValueRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Contact\Repositories\PersonRepository $personRepository
     * @param  \Webkul\Lead\Repositories\ProductRepository $productRepository
     * @param  \Webkul\Attribute\Repositories\AttributeValueRepository $attributeValueRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        PersonRepository $personRepository,
        ProductRepository $productRepository,
        AttributeValueRepository $attributeValueRepository,
        Container $container
    )
    {
        $this->personRepository = $personRepository;

        $this->productRepository = $productRepository;

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
        return 'Webkul\Lead\Contracts\Lead';
    }

    /**
     * @param array $data
     * @return \Webkul\Lead\Contracts\Lead
     */
    public function create(array $data)
    {
        if (isset($data['person']['id'])) {
            $person = $this->personRepository->update($data['person'], $data['person']['id']);
        } else {
            $person = $this->personRepository->create($data['person']);
        }

        $lead = parent::create(array_merge([
            'person_id' => $person->id,
        ], $data));

        $this->attributeValueRepository->save($data, $lead->id);

        if (isset($data['products'])) {
            foreach ($data['products'] as $product) {
                $this->productRepository->create(array_merge($product, [
                    'lead_id' => $lead->id,
                    'amount'  => $product['price'] * $product['quantity'],
                ]));
            }
        }

        return $lead;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \Webkul\Lead\Contracts\Lead
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $lead = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

        return $lead;
    }
}