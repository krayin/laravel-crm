<?php

namespace Webkul\Contact\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Core\Eloquent\Repository;

class OrganizationRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeValueRepository $attributeValueRepository,
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
        return 'Webkul\Contact\Contracts\Organization';
    }

    /**
     * @return \Webkul\Contact\Contracts\Organization
     */
    public function create(array $data)
    {
        $data['user_id'] = $data['user_id'] ?? null;

        $organization = parent::create($data);

        $this->attributeValueRepository->save($data, $organization->id);

        return $organization;
    }

    /**
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Contact\Contracts\Organization
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        $data['user_id'] = $data['user_id'] ?? null;
        
        $organization = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

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
