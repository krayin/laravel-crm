<?php

namespace Webkul\WebForm\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Core\Eloquent\Repository;
use Webkul\WebForm\Contracts\WebForm;

class WebFormRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected WebFormAttributeRepository $webFormAttributeRepository,
        protected AttributeRepository $attributeRepository,
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
        return WebForm::class;
    }

    /**
     * Create Web Form.
     *
     * @return WebForm
     */
    public function create(array $data)
    {
        $webForm = $this->model->create(array_merge($data, [
            'form_id' => Str::random(50),
        ]));

        foreach ($this->filterRestrictedAttributes($data['attributes']) as $attributeData) {
            $this->webFormAttributeRepository->create(array_merge([
                'web_form_id' => $webForm->id,
            ], $attributeData));
        }

        return $webForm;
    }

    /**
     * Update Web Form.
     *
     * @param  int  $id
     * @param  string  $attribute
     * @return WebForm
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        $webForm = parent::update($data, $id);

        $previousAttributeIds = $webForm->attributes()->pluck('id');

        foreach ($this->filterRestrictedAttributes($data['attributes']) as $attributeId => $attributeData) {
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

    /**
     * Removes the attributes which are never exposed on a web form, such as the pipeline and the
     * stage of a lead. Those are decided by the web form itself, so they are dropped here as well
     * to keep them out of the public form when the request is not made through the builder.
     *
     * @return array
     */
    protected function filterRestrictedAttributes(array $attributes)
    {
        $restrictedAttributeIds = $this->attributeRepository->getRestrictedWebFormAttributeIds();

        return array_filter($attributes, function ($attributeData) use ($restrictedAttributeIds) {
            return ! in_array($attributeData['attribute_id'] ?? null, $restrictedAttributeIds);
        });
    }
}
