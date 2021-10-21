<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Webkul\Admin\Traits\ProvideDropdownOptions;
use Webkul\UI\DataGrid\DataGrid;

class PipelineDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('lead_pipelines')
            ->addSelect(
                'lead_pipelines.id',
                'lead_pipelines.name',
                'lead_pipelines.rotten_days',
                'lead_pipelines.is_default',
            );

        $this->addFilter('id', 'lead_pipelines.id');

        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index'    => 'id',
            'label'    => trans('admin::app.datagrid.id'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => trans('admin::app.datagrid.name'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'rotten_days',
            'label'    => trans('admin::app.datagrid.rotten-days'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'            => 'is_default',
            'label'            => trans('admin::app.datagrid.is-default'),
            'type'             => 'dropdown',
            'dropdown_options' => $this->getBooleanDropdownOptions('yes_no'),
            'sortable'         => false,
            'closure'          => function ($row) {
                return (bool) $row->is_default
                    ? __('admin::app.common.yes')
                    : __('admin::app.common.no');
            }
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('ui::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.settings.pipelines.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.pipelines.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'pipeline']),
            'icon'         => 'trash-icon',
        ]);
    }
}
