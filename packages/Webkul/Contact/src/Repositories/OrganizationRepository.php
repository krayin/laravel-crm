<?php

namespace Webkul\Contact\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Contact\Contracts\Organization;
use Webkul\Core\Eloquent\Repository;

class OrganizationRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
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
        return Organization::class;
    }

    /**
     * Create.
     *
     * @return \Webkul\Contact\Contracts\Organization
     */
    public function create(array $data)
    {
        if (isset($data['user_id'])) {
            $data['user_id'] = $data['user_id'] ?: null;
        }

        $organization = parent::create($data);

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $organization->id,
        ]));

        return $organization;
    }

    /**
     * Update.
     *
     * @param  int  $id
     * @param  array  $attribute
     * @return \Webkul\Contact\Contracts\Organization
     */
    public function update(array $data, $id, $attributes = [])
    {
        if (isset($data['user_id'])) {
            $data['user_id'] = $data['user_id'] ?: null;
        }

        $organization = parent::update($data, $id);

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
                'entity_id' => $organization->id,
            ]), $attributes);

            return $organization;
        }

        $this->attributeValueRepository->save(array_merge($data, [
            'entity_id' => $organization->id,
        ]));

        return $organization;
    }

    /**
     * Delete organization and it's persons.
     *
     * @param  int  $id
     * @return @void
     */
    public function delete($id)
    {
        $organization = $this->findOrFail($id);

        DB::transaction(function () use ($organization, $id) {
            $organization->persons()->delete();

            $this->attributeValueRepository->deleteWhere([
                'entity_id'   => $id,
                'entity_type' => 'organizations',
            ]);

            $organization->delete();
        });
    }
}
