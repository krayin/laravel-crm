<?php

namespace Webkul\WebForm\DataGrids;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class WebFormDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('web_forms')
            ->addSelect(
                'web_forms.id',
                'web_forms.title',
            );

        $this->addFilter('id', 'web_forms.id');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'    => 'id',
            'label'    => trans('Id'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'title',
            'label'    => trans('Title'),
            'type'     => 'string',
            'sortable' => true,
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        $this->addAction([
            'index'  => 'view',
            'icon'   => 'icon-edit',
            'title'  => trans('View'),
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.settings.web_forms.view', $row->id),
        ]);

        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => trans('Edit'),
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.settings.web_forms.edit', $row->id),
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => trans('Delete'),
            'method' => 'DELETE',
            'url'    => fn ($row) => route('admin.settings.web_forms.delete', $row->id),
        ]);
    }
}
