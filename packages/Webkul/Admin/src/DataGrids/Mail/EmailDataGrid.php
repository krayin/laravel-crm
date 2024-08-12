<?php

namespace Webkul\Admin\DataGrids\Mail;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Illuminate\Support\Str;

class EmailDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('emails')
            ->select(
                'emails.id',
                'emails.name',
                'emails.subject',
                'emails.reply',
                'emails.created_at',
                DB::raw('COUNT(DISTINCT '.DB::getTablePrefix().'email_attachments.id) as attachments')
            )
            ->leftJoin('email_attachments', 'emails.id', '=', 'email_attachments.email_id')
            ->groupBy('emails.id')
            ->where('folders', 'like', '%"'.request('route').'"%')
            ->whereNull('parent_id');

        $this->addFilter('id', 'emails.id');
        $this->addFilter('name', 'emails.name');
        $this->addFilter('created_at', 'emails.created_at');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'attachments',
            'label'      => '<span class="icon-attachment text-2xl"></span>',
            'type'       => 'string',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                if ($row->attachments) {
                    return '<i class="icon-leads"></i>';
                }
            },
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => trans('admin::app.mail.index.datagrid.from'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'subject',
            'label'    => trans('admin::app.mail.index.datagrid.subject'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return Str::limit(strip_tags($row->reply), 50);
            },
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('admin::app.mail.index.datagrid.created-at'),
            'type'            => 'date',
            'searchable'      => true,
            'filterable'      => true,
            'filterable_type' => 'date_range',
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
            'index'  => 'edit',
            'icon'   => request('route') == 'draft'
                ? 'icon-edit'
                : 'icon-eye',
            'title'  => request('route') == 'draft'
                ? trans('admin::app.mail.index.datagrid.edit')
                : trans('admin::app.mail.index.datagrid.view'),
            'method'       => 'GET',
            'params'       => [
                'type' => request('route') == 'trash'
                    ? 'delete'
                    : 'trash',
            ],
            'url'    => function ($row) {
                return route('admin.mail.view', [request('route'), $row->id]);
            },
        ]);

        $this->addAction([
            'index'        => 'delete',
            'icon'         => 'icon-delete',
            'title'        => trans('admin::app.mail.index.datagrid.delete'),
            'method'       => 'DELETE',
            'params'       => [
                'type' => request('route') == 'trash'
                    ? 'delete'
                    : 'trash',
            ],
            'url'    => function ($row) {
                return route('admin.mail.delete', $row->id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (request('route') == 'trash') {
            $this->addMassAction([
                'title'  => trans('admin::app.mail.index.datagrid.move-to-inbox'),
                'method' => 'POST',
                'url'    => route('admin.mail.mass_update', ['folders' => ['inbox']]),
            ]);
        }

        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.mail.index.datagrid..delete'),
            'method' => 'POST',
            'url'    => route('admin.mail.mass_delete', [
                'type' => request('route') == 'trash'
                    ? 'delete'
                    : 'trash',
            ]),
        ]);
    }
}
