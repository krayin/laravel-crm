<?php

namespace Webkul\Lead\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Carbon\Carbon;
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
     * @param  \Webkul\Contact\Repositories\PersonRepository  $personRepository
     * @param  \Webkul\Lead\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Attribute\Repositories\AttributeValueRepository  $attributeValueRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        PersonRepository $personRepository,
        ProductRepository $productRepository,
        AttributeValueRepository $attributeValueRepository,
        Container $container
    ) {
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
            $person = $this->personRepository->update(array_merge($data['person'], [
                'entity_type' => 'persons',
            ]), $data['person']['id']);
        } else {
            $person = $this->personRepository->create(array_merge($data['person'], [
                'entity_type' => 'persons',
            ]));
        }

        $lead = parent::create(array_merge([
            'person_id'        => $person->id,
            'lead_pipeline_id' => 1,
            'lead_stage_id'    => $data['lead_stage_id'] ?? 1,
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
        if (isset($data['person'])) {
            if (isset($data['person']['id'])) {
                $person = $this->personRepository->update(array_merge($data['person'], [
                    'entity_type' => 'persons',
                ]), $data['person']['id']);
            } else {
                $person = $this->personRepository->create(array_merge($data['person'], [
                    'entity_type' => 'persons',
                ]));
            }

            $data = array_merge([
                'person_id' => $person->id,
            ], $data);
        }

        if (isset($data['closed_at']) && ! $data['closed_at']) {
            $data['closed_at'] = Carbon::now();
        }

        $lead = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

        $previousProductIds = $lead->products()->pluck('id');

        if (isset($data['products'])) {
            foreach ($data['products'] as $productId => $productInputs) {
                if (Str::contains($productId, 'product_')) {
                    $this->productRepository->create(array_merge([
                        'lead_id' => $lead->id,
                    ], $productInputs));
                } else {
                    if (is_numeric($index = $previousProductIds->search($productId))) {
                        $previousProductIds->forget($index);
                    }

                    $this->productRepository->update($productInputs, $productId);
                }
            }
        }

        foreach ($previousProductIds as $productId) {
            $this->productRepository->delete($productId);
        }

        return $lead;
    }

    /**
     * Retreives lead count based on lead stage name
     *
     * @return number
     */
    public function getLeadsCount($leadStage, $startDate, $endDate)
    {
        $query = $this->whereBetween('leads.created_at', [$startDate, $endDate]);

        if ($leadStage != "all") {
            $query
                ->leftJoin('lead_stages', 'leads.lead_stage_id', '=', 'lead_stages.id')
                ->where('lead_stages.name', $leadStage);
        }

        return $query
                ->get()
                ->count();
    }
}