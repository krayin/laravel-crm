<?php

namespace Webkul\Admin\DataGrids\Mail;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Email\Repositories\EmailRepository;
use Webkul\Tag\Repositories\TagRepository;

class EmailDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('emails')
            ->select(
                'emails.id',
                'emails.name',
                'emails.subject',
                'emails.reply',
                'emails.is_read',
                'emails.created_at',
                'tags.name as tags',
                DB::raw('COUNT(DISTINCT '.DB::getTablePrefix().'email_attachments.id) as attachments')
            )
            ->leftJoin('email_attachments', 'emails.id', '=', 'email_attachments.email_id')
            ->leftJoin('email_tags', 'emails.id', '=', 'email_tags.email_id')
            ->leftJoin('tags', 'tags.id', '=', 'email_tags.tag_id')
            ->groupBy('emails.id')
            ->where('folders', 'like', '%"'.request('route').'"%')
            ->whereNull('parent_id');

        $this->addFilter('id', 'emails.id');
        $this->addFilter('name', 'emails.name');
        $this->addFilter('tags', 'tags.name');
        $this->addFilter('created_at', 'emails.created_at');

        return $queryBuilder;
    }

    /**
     * Prepare Columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.mail.index.datagrid.id'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'attachments',
            'label'      => '<span class="icon-attachment text-2xl"></span>',
            'type'       => 'string',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => false,
            'closure'    => fn ($row) => $row->attachments ? '<i class="icon-attachment text-2xl"></i>' : '',
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.mail.index.datagrid.from'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'subject',
            'label'      => trans('admin::app.mail.index.datagrid.subject'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'tags',
            'label'              => trans('admin::app.mail.index.datagrid.tag-name'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'searchable_dropdown',
            'closure'            => function ($row) {
                if ($email = app(EmailRepository::class)->find($row->id)) {
                    return $email->tags;
                }

                return '--';
            },
            'filterable_options' => [
                'repository' => TagRepository::class,
                'column'     => [
                    'label' => 'name',
                    'value' => 'name',
                ],
            ],
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
                return Carbon::parse($row->created_at)->isToday()
                    ? Carbon::parse($row->created_at)->format('h:i A')
                    : Carbon::parse($row->created_at)->format('M d');
            },
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('mail.view')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => request('route') == 'draft'
                    ? 'icon-edit'
                    : 'icon-eye',
                'title'  => request('route') == 'draft'
                    ? trans('admin::app.mail.index.datagrid.edit')
                    : trans('admin::app.mail.index.datagrid.view'),
                'method' => 'GET',
                'params' => [
                    'type' => request('route') == 'trash'
                        ? 'delete'
                        : 'trash',
                ],
                'url'    => fn ($row) => route('admin.mail.view', [request('route'), $row->id]),
            ]);
        }

        if (bouncer()->hasPermission('mail.delete')) {
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
                'url'    => fn ($row) => route('admin.mail.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (request('route') == 'trash') {
            $this->addMassAction([
                'title'   => trans('admin::app.mail.index.datagrid.move-to-inbox'),
                'method'  => 'POST',
                'url'     => route('admin.mail.mass_update', ['folders' => ['inbox']]),
                'options' => [
                    [
                        'value' => 'trash',
                        'label' => trans('admin::app.mail.index.datagrid.move-to-inbox'),
                    ],
                ],
            ]);
        }

        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.mail.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.mail.mass_delete', [
                'type' => request('route') == 'trash'
                    ? 'delete'
                    : 'trash',
            ]),
        ]);
    }
}
