<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\Admin\Traits\ProvideDropdownOptions;
use Webkul\UI\DataGrid\DataGrid;

class UserDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('users')
            ->addSelect(
                'users.id',
                'users.name',
                'users.email',
                'users.image',
                'users.status',
                'users.created_at'
            );

        $this->addFilter('id', 'users.id');

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
            'closure'  => function ($row) {
                if ($row->image) {
                    return '<div class="avatar"><img src="' . Storage::url($row->image) . '"></div>' . $row->name;
                } else {
                    return '<div class="avatar"><span class="icon avatar-icon"></span></div>' . $row->name;
                }
            },
        ]);

        $this->addColumn([
            'index'    => 'email',
            'label'    => trans('admin::app.datagrid.email'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'            => 'status',
            'label'            => trans('admin::app.datagrid.status'),
            'type'             => 'dropdown',
            'dropdown_options' => $this->getBooleanDropdownOptions(),
            'searchable'       => false,
            'closure'          => function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge badge-round badge-primary"></span>' . trans('admin::app.datagrid.active');
                } else {
                    return '<span class="badge badge-round badge-danger"></span>' . trans('admin::app.datagrid.inactive');
                }
            },
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.created_at'),
            'type'       => 'date_range',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatDate($row->created_at);
            },
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
            'route'  => 'admin.settings.users.edit',
            'icon'   => 'pencil-icon',
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.users.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'user']),
            'icon'         => 'trash-icon',
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.settings.users.mass_delete'),
            'method' => 'PUT',
        ]);

        $this->addMassAction([
            'type'    => 'update',
            'label'   => trans('ui::app.datagrid.update-status'),
            'action'  => route('admin.settings.users.mass_update'),
            'method'  => 'PUT',
            'options' => [
                trans('admin::app.datagrid.active')   => 1,
                trans('admin::app.datagrid.inactive') => 0,
            ],
        ]);
    }
}
