<?php

namespace Webkul\Admin\DataGrids\Setting;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class TagDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('tags')
            ->addSelect(
                'tags.id',
                'tags.name',
                'tags.created_at',
                'users.name as user_name',
            )
            ->leftJoin('users', 'tags.user_id', '=', 'users.id');
        
        $currentUser = auth()->guard('user')->user();

        if ($currentUser->view_permission != 'global') {
            if ($currentUser->view_permission == 'group') {
                $queryBuilder->whereIn('tags.user_id', app('\Webkul\User\Repositories\UserRepository')->getCurrentUserGroupsUserIds());
            } else {
                $queryBuilder->where('tags.user_id', $currentUser->id);
            }
        }

        $this->addFilter('id', 'tags.id');
        $this->addFilter('name', 'tags.name');
        $this->addFilter('created_at', 'tags.created_at');
        $this->addFilter('user_name', 'users.id');

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
            'index'           => 'id',
            'label'           => trans('admin::app.datagrid.id'),
            'type'            => 'string',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'           => 'name',
            'label'           => trans('admin::app.datagrid.name'),
            'type'            => 'string',
            'searchable'      => true,
            'sortable'        => true,
        ]);

        $this->addColumn([
            'index'            => 'user_name',
            'label'            => trans('admin::app.datagrid.user'),
            'type'             => 'dropdown',
            'dropdown_options' => app('\Webkul\User\Repositories\UserRepository')->get(['id as value', 'name as label'])->toArray(),
            'searchable'       => false,
            'sortable'         => true,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('admin::app.datagrid.created_at'),
            'type'            => 'date_range',
            'searchable'      => false,
            'sortable'        => true,
            'closure'         => function ($row) {
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
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.settings.tags.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'source']),
            'icon'         => 'trash-icon',
        ]);
    }
}
