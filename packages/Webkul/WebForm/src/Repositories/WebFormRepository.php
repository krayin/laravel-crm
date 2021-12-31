<?php

namespace Webkul\WebForm\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Core\Eloquent\Repository;

class WebFormRepository extends Repository
{
    /**
     * WebFormAttributeRepository instance.
     *
     * @var \Webkul\WebForm\Repositories\WebFormAttributeRepository
     */
    protected $webFormAttributeRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\WebForm\Repositories\WebFormAttributeRepository  $webFormAttributeRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        WebFormAttributeRepository $webFormAttributeRepository,
        Container $container
    ) {
        $this->webFormAttributeRepository = $webFormAttributeRepository;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\WebForm\Contracts\WebForm';
    }

    /**
     * Create Web Form.
     *
     * @param  array  $data
     * @return \Webkul\WebForm\Contracts\WebForm
     */
    public function create(array $data)
    {
        $webForm = $this->model->create(array_merge($data, [
            'form_id' => Str::random(50),
        ]));

        foreach ($data['attributes'] as $attributeData) {
            $this->webFormAttributeRepository->create(array_merge([
                'web_form_id' => $webForm->id,
            ], $attributeData));
        }

        return $webForm;
    }

    /**
     * Update Web Form.
     *
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\WebForm\Contracts\WebForm
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $webForm = parent::update($data, $id);

        $previousAttributeIds = $webForm->attributes()->pluck('id');

        foreach ($data['attributes'] as $attributeId => $attributeData) {
            if (Str::contains($attributeId, 'attribute_')) {
                $this->webFormAttributeRepository->create(array_merge([
                    'web_form_id' => $webForm->id,
                ], $attributeData));
            } else {
                if (is_numeric($index = $previousAttributeIds->search($attributeId))) {
                    $previousAttributeIds->forget($index);
                }

                $this->webFormAttributeRepository->update($attributeData, $attributeId);
            }
        }

        foreach ($previousAttributeIds as $attributeId) {
            $this->webFormAttributeRepository->delete($attributeId);
        }

        return $webForm;
    }
}