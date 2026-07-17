<?php

namespace Webkul\Attribute\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Webkul\Attribute\Contracts\Attribute;
use Webkul\Core\Eloquent\Repository;

class AttributeRepository extends Repository
{
    /**
     * The attribute codes which are never exposed on a web form.
     */
    protected $restrictedWebFormAttributeCodes = [
        'lead_pipeline_id',
        'lead_pipeline_stage_id',
    ];

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeOptionRepository $attributeOptionRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Attribute\Contracts\Attribute';
    }

    /**
     * @return Attribute
     */
    public function create(array $data)
    {
        $options = isset($data['options']) ? $data['options'] : [];

        $attribute = $this->model->create($data);

        if (in_array($attribute->type, ['select', 'multiselect', 'checkbox']) && count($options)) {
            $sortOrder = 1;

            foreach ($options as $optionInputs) {
                $this->attributeOptionRepository->create(array_merge([
                    'attribute_id' => $attribute->id,
                    'sort_order' => $sortOrder++,
                ], $optionInputs));
            }
        }

        return $attribute;
    }

    /**
     * @param  int  $id
     * @param  string  $attribute
     * @return Attribute
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        $attribute = $this->find($id);

        $attribute->update($data);

        if (! in_array($attribute->type, ['select', 'multiselect', 'checkbox'])) {
            return $attribute;
        }

        if (! isset($data['options'])) {
            return $attribute;
        }

        foreach ($data['options'] as $optionId => $optionInputs) {
            $isNew = $optionInputs['isNew'] == 'true';

            if ($isNew) {
                $this->attributeOptionRepository->create(array_merge([
                    'attribute_id' => $attribute->id,
                ], $optionInputs));
            } else {
                $isDelete = $optionInputs['isDelete'] == 'true';

                if ($isDelete) {
                    $this->attributeOptionRepository->delete($optionId);
                } else {
                    $this->attributeOptionRepository->update($optionInputs, $optionId);
                }
            }
        }

        return $attribute;
    }

    /**
     * @param  string  $code
     * @return Attribute
     */
    public function getAttributeByCode($code)
    {
        static $attributes = [];

        if (array_key_exists($code, $attributes)) {
            return $attributes[$code];
        }

        return $attributes[$code] = $this->findOneByField('code', $code);
    }

    /**
     * @param  int  $lookup
     * @param  string  $query
     * @param  array  $columns
     * @return array
     */
    public function getLookUpOptions($lookup, $query = '', $columns = [])
    {
        $lookup = config('attribute_lookups.'.$lookup);

        if (! count($columns)) {
            $columns = [($lookup['value_column'] ?? 'id').' as id', ($lookup['label_column'] ?? 'name').' as name'];
        }

        if (Str::contains($lookup['repository'], 'UserRepository')) {
            $userRepository = app($lookup['repository'])->where('status', 1);

            $currentUser = auth()->guard('user')->user();

            if ($currentUser?->view_permission === 'group') {
                $query = urldecode($query);

                $userIds = bouncer()->getAuthorizedUserIds();

                return $userRepository
                    ->when(! empty($userIds), fn ($queryBuilder) => $queryBuilder->whereIn('users.id', $userIds))
                    ->when(! empty($query), fn ($queryBuilder) => $queryBuilder->where('users.name', 'like', "%{$query}%"))
                    ->get();
            } elseif ($currentUser?->view_permission === 'individual') {
                return $userRepository->where('users.id', $currentUser->id)->get();
            }

            return $userRepository->where('users.name', 'like', '%'.urldecode($query).'%')->get();
        }

        return app($lookup['repository'])->findWhere([
            [$lookup['label_column'] ?? 'name', 'like', '%'.urldecode($query).'%'],
        ], $columns);
    }

    /**
     * @param  string  $lookup
     * @param  int|array  $entityId
     * @param  array  $columns
     * @return mixed
     */
    public function getLookUpEntity($lookup, $entityId = null, $columns = [])
    {
        if (! $entityId) {
            return;
        }

        $lookup = config('attribute_lookups.'.$lookup);

        if (! count($columns)) {
            $columns = [($lookup['value_column'] ?? 'id').' as id', ($lookup['label_column'] ?? 'name').' as name'];
        }

        if (is_array($entityId)) {
            return app($lookup['repository'])->findWhereIn(
                'id',
                $entityId,
                $columns
            );
        } else {
            return app($lookup['repository'])->find($entityId, $columns);
        }
    }

    /**
     * Returns the attributes which can be added to a web form.
     *
     * The pipeline and the stage of a lead are decided by the web form itself, so they are never
     * offered to the visitor and are excluded from the builder.
     *
     * @param  array  $excludedAttributeIds
     * @return Collection
     */
    public function getWebFormAttributes($excludedAttributeIds = [])
    {
        return $this->model
            ->whereIn('entity_type', ['persons', 'leads'])
            ->whereNotIn('id', $excludedAttributeIds)
            ->whereNotIn('code', $this->restrictedWebFormAttributeCodes)
            ->get();
    }

    /**
     * Returns the ids of the attributes which are never exposed on a web form.
     *
     * @return array
     */
    public function getRestrictedWebFormAttributeIds()
    {
        return $this->model
            ->whereIn('code', $this->restrictedWebFormAttributeCodes)
            ->pluck('id')
            ->toArray();
    }
}
