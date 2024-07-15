<?php

namespace Webkul\Admin\DataGrids\Contact;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class PersonDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        return DB::table('persons')
            ->addSelect(
                'persons.id',
                'persons.name as person_name',
                'persons.emails',
                'persons.contact_numbers',
                'organizations.name as organization',
                'organizations.id as organization_id'
            )
            ->leftJoin('organizations', 'persons.organization_id', '=', 'organizations.id');
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.catalog.families.index.datagrid.id'),
            'type'       => 'integer',
            'filterable' => true,
            'sortable'   => true,
        ]);
    }
}
