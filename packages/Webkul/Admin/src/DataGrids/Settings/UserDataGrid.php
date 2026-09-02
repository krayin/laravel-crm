<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\DataGrid\DataGrid;
use Webkul\User\Repositories\GroupRepository;

class UserDataGrid extends DataGrid
{
    /**
     * Create data grid instance.
     *
     * @return void
     */
    public function __construct(protected GroupRepository $groupRepository) {}

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $tablePrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('users')
            ->addSelect(
                'users.id',
                'users.name',
                'users.email',
                'users.image',
                'users.status',
                'users.created_at',
                DB::raw('GROUP_CONCAT(DISTINCT '.$tablePrefix.'groups.name ORDER BY '.$tablePrefix.'groups.name SEPARATOR \', \') as group_name')
            )
            ->leftJoin('user_groups', 'users.id', '=', 'user_groups.user_id')
            ->leftJoin('groups', 'user_groups.group_id', '=', 'groups.id')
            ->groupBy('users.id');

        /**
         * Scope the listing to the acting user's data scope, mirroring how Krayin scopes lead and
         * contact records by their owner. A `global` scope (or a full administrator) returns null and
         * sees every user; a `group` scope sees the users belonging to their group; an `individual`
         * scope sees the users they created together with their own account. What a user may then edit
         * or delete is further constrained by the role hierarchy enforced in the controller.
         */
        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->where(function ($query) use ($userIds) {
                $query->whereIn('users.created_by', $userIds)
                    ->orWhereIn('users.id', $userIds);
            });
        }

        /**
         * The `groups` join introduces columns that also exist on `users` (e.g. `name`,
         * `created_at`), so the searchable and filterable columns are qualified to keep the
         * generated where clauses unambiguous.
         */
        $this->addFilter('name', 'users.name');
        $this->addFilter('email', 'users.email');
        $this->addFilter('created_at', 'users.created_at');
        $this->addFilter('group_name', 'groups.name');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('admin::app.settings.users.index.datagrid.id'),
            'type' => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.settings.users.index.datagrid.name'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                return [
                    'image' => $row->image ? Storage::url($row->image) : null,
                    'name' => $row->name,
                ];
            },
        ]);

        $this->addColumn([
            'index' => 'email',
            'label' => trans('admin::app.settings.users.index.datagrid.email'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'group_name',
            'label' => trans('admin::app.settings.users.index.datagrid.group'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => $this->groupRepository->all(['name as label', 'name as value'])->toArray(),
            'closure' => fn ($row) => $row->group_name ?: trans('admin::app.settings.users.index.datagrid.no-group'),
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('admin::app.settings.users.index.datagrid.status'),
            'type' => 'boolean',
            'filterable' => true,
            'sortable' => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('admin::app.settings.users.index.datagrid.created-at'),
            'type' => 'date',
            'sortable' => true,
            'searchable' => true,
            'filterable_type' => 'date_range',
            'filterable' => true,
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('settings.user.users.edit')) {
            $this->addAction([
                'index' => 'edit',
                'icon' => 'icon-edit',
                'title' => trans('admin::app.settings.users.index.datagrid.edit'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.settings.users.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('settings.user.users.delete')) {
            $this->addAction([
                'index' => 'delete',
                'icon' => 'icon-delete',
                'title' => trans('admin::app.settings.users.index.datagrid.delete'),
                'method' => 'DELETE',
                'url' => fn ($row) => route('admin.settings.users.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon' => 'icon-delete',
            'title' => trans('admin::app.settings.users.index.datagrid.delete'),
            'method' => 'POST',
            'url' => route('admin.settings.users.mass_delete'),
        ]);

        $this->addMassAction([
            'title' => trans('admin::app.settings.users.index.datagrid.update-status'),
            'method' => 'POST',
            'url' => route('admin.settings.users.mass_update'),
            'options' => [
                [
                    'label' => trans('admin::app.settings.users.index.datagrid.active'),
                    'value' => 1,
                ],
                [
                    'label' => trans('admin::app.settings.users.index.datagrid.inactive'),
                    'value' => 0,
                ],
            ],
        ]);
    }
}
