<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\Crypt;

class DataGridController extends Controller
{
    /**
     * Look up.
     */
    public function lookUp()
    {
        /**
         * Validation for parameters.
         */
        $params = $this->validate(request(), [
            'datagrid_id' => ['required'],
            'column'      => ['required'],
            'search'      => ['required', 'min:2'],
        ]);

        /**
         * Preparing the datagrid instance and only columns.
         */
        $datagrid = app(Crypt::decryptString($params['datagrid_id']));
        $datagrid->prepareColumns();

        /**
         * Finding the first column from the collection.
         */
        $column = collect($datagrid->getColumns())->map(fn ($column) => $column->toArray())->where('index', $params['column'])->firstOrFail();

        /**
         * Fetching on the basis of column options.
         */
        return app($column['filterable_options']['repository'])
            ->select([$column['filterable_options']['column']['label'].' as label', $column['filterable_options']['column']['value'].' as value'])
            ->where($column['filterable_options']['column']['label'], 'LIKE', '%'.$params['search'].'%')
            ->get();
    }
}
