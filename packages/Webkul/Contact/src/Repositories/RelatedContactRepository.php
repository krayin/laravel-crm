<?php

namespace Webkul\Contact\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Container\Container;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Contact\Models\RelatedContact;

/**
 * Class RelatedContactRepository.
 *
 * @package namespace App\Entities;
 */
class RelatedContactRepository extends Repository
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'emails',
        'mobile_numbers',
        'type',
    ];

    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        Container $container
    ) {
        parent::__construct($container);
    }


    public function model()
    {
        return RelatedContact::class;
    }


}
