<?php

namespace Webkul\Contact\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\Attribute\Repositories\AttributeValueRepository;

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
    function model()
    {
        return 'Webkul\Contact\Contracts\Organization';
    }

    /**
     * @param array $data
     * @return \Webkul\Contact\Contracts\Organization
     */
    public function create(array $data)
    {
        $organization = parent::create($data);

        $this->attributeValueRepository->save($data, $organization->id);

        return $organization;
    }

    /**
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * @return \Webkul\Contact\Contracts\Organization
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $organization = parent::update($data, $id);

        $this->attributeValueRepository->save($data, $id);

        return $organization;
    }

    /**
     * Delete organization and it's persons.
     * 
     * @param int $id
     * @return @void
     */
    public function delete($id)
    {
        $organization = $this->findOrFail($id);
    
        DB::transaction(function() use($organization, $id) {
            $organization->persons()->delete();
    
            $this->attributeValueRepository->deleteWhere([
                'entity_id'   => $id,
                'entity_type' => 'organizations',
            ]);
    
            $organization->delete();
        });
    }
}