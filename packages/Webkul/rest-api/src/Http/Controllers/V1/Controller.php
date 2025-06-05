<?php

namespace Webkul\RestApi\Http\Controllers\V1;

use Webkul\Core\Eloquent\Repository;
use Webkul\RestApi\Http\Controllers\RestApiController;

class Controller extends RestApiController
{
    /**
     * Exclude keys which not needed during searching.
     *
     * @var array
     */
    protected $excludeKeys = [
        'entity_type',
        'limit',
        'page',
        'pagination',
        'order',
        'sort',
    ];

    /**
     * Add entity type.
     *
     * @return void
     */
    protected function addEntityTypeInRequest($entityType)
    {
        request()->request->add(['entity_type' => $entityType]);
    }

    /**
     * Returns a listing of the resource.
     *
     * @return Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    protected function allResources(Repository $repository)
    {
        $query = $repository->query();

        foreach (request()->except($this->excludeKeys) as $input => $value) {
            $query = $query->whereIn($input, array_map('trim', explode(',', $value)));
        }

        if ($sort = request()->input('sort')) {
            $query = $query->orderBy($sort, request()->input('order') ?? 'desc');
        } else {
            $query = $query->orderBy('id', 'desc');
        }

        if (is_null(request()->input('pagination')) || request()->input('pagination')) {
            return $query->paginate(request()->input('limit') ?? 10);
        }

        return $query->get();
    }
}
