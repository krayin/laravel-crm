<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class TagDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('tags')
            ->addSelect(
                'tags.id',
                'tags.name',
                'tags.color',
                'tags.created_at',
                'users.name as user_name',
            )
            ->leftJoin('users', 'tags.user_id', '=', 'users.id');

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('tags.user_id', $userIds);
        }

        $this->addFilter('id', 'tags.id');
        $this->addFilter('name', 'tags.name');
        $this->addFilter('created_at', 'tags.created_at');
        $this->addFilter('user_name', 'users.id');

        return $queryBuilder;
    }

    /**
     * Prepare Columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.settings.tags.index.datagrid.id'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.settings.tags.index.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => false,
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $color = $row->color ?? 'gray-500';
                $html = "<div class=\"flex items-center gap-2\">
                            <span class='inline-block h-4 w-4 cursor-pointer rounded-full bg-{$color } shadow-md'></span>
                            <span>{$row->name}</span>
                        </div>";

                return $html;
            },
        ]);

        $this->addColumn([
            'index'      => 'user_name',
            'label'      => trans('admin::app.settings.tags.index.datagrid.users'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('admin::app.settings.tags.index.datagrid.created-at'),
            'type'            => 'date',
            'searchable'      => true,
            'filterable'      => true,
            'sortable'        => true,
            'filterable_type' => 'date_range',
            'closure'         => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => trans('admin::app.settings.tags.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.settings.tags.edit', $row->id);
            },
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.tags.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => function ($row) {
                return route('admin.settings.tags.delete', $row->id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.settings.tags.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.settings.tags.mass_delete'),
        ]);
    }
}
