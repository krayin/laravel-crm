<?php

namespace Webkul\Admin\DataGrids\Mail;

use Illuminate\Support\Facades\DB;
use Webkul\UI\DataGrid\DataGrid;

class EmailDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('emails')
            ->select(
                'emails.id',
                'emails.name',
                'emails.subject',
                'emails.reply',
                'emails.created_at',
                DB::raw('COUNT(DISTINCT ' . DB::getTablePrefix() . 'email_attachments.id) as attachments')
            )
            ->leftJoin('email_attachments', 'emails.id', '=', 'email_attachments.email_id')
            ->groupBy('emails.id')
            ->where('folders', 'like', '%"' . request('route') . '"%')
            ->whereNull('parent_id');

        $this->addFilter('id', 'emails.id');
        $this->addFilter('name', 'emails.name');
        $this->addFilter('created_at', 'emails.created_at');

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
            'index'      => 'attachments',
            'label'      => '<i class="icon attachment-icon"></i>',
            'type'       => 'string',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                if ($row->attachments) {
                    return '<i class="icon attachment-icon"></i>';
                }
            },
        ]);

        $this->addColumn([
            'index'    => 'name',
            'label'    => trans('admin::app.datagrid.from'),
            'type'     => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'subject',
            'label'    => trans('admin::app.datagrid.subject'),
            'type'     => 'string',
            'sortable' => true,
            'closure'  => function ($row) {
                return '<div class="subject-wrapper"><span class="subject-content">' . $row->subject . '</span><span class="reply"> - ' . substr(strip_tags($row->reply), 0, 225) . '<span></div>';
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
            'title'  => request('route') == 'draft'
                ? trans('ui::app.datagrid.edit')
                : trans('ui::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'admin.mail.view',
            'params' => ['route' => request('route')],
            'icon'   => request('route') == 'draft'
                ? 'pencil-icon'
                : 'eye-icon'
        ]);

        $this->addAction([
            'title'        => trans('ui::app.datagrid.delete'),
            'method'       => 'DELETE',
            'route'        => 'admin.mail.delete',
            'params'       => [
                                'type' => request('route') == 'trash'
                                    ? 'delete'
                                    : 'trash'
                            ],
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'email']),
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
        if (request('route') == 'trash') {
            $this->addMassAction([
                'type'   => 'delete',
                'label'  => trans('admin::app.datagrid.move-to-inbox'),
                'action' => route('admin.mail.mass_update', [
                                'folders' => ['inbox'],
                            ]),
                'method' => 'PUT',
            ]);
        }

        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('ui::app.datagrid.delete'),
            'action' => route('admin.mail.mass_delete', [
                            'type' => request('route') == 'trash'
                                ? 'delete'
                                : 'trash',
                        ]),
            'method' => 'PUT',
        ]);
    }
}
