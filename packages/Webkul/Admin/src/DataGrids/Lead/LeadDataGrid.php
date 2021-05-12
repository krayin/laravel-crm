<?php

namespace Webkul\Admin\DataGrids\Lead;

use Webkul\UI\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

class LeadDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('leads')
            ->addSelect(
                'leads.id',
                'leads.title',
                'leads.status',
                'leads.lead_value',
                'leads.created_at',
                'users.name as user_name'
            )
            ->leftJoin('users', 'leads.user_id', '=', 'users.id')
            ->leftJoin('lead_types', 'leads.lead_type_id', '=', 'lead_types.id')
            ->leftJoin('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
            ->leftJoin('lead_pipelines', 'leads.lead_pipeline_id', '=', 'lead_pipelines.id')
            ->leftJoin('lead_pipeline_stages', 'leads.lead_stage_id', '=', 'lead_pipeline_stages.id')
            ;

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'       => 'title',
            'label'       => trans('admin::app.datagrid.subject'),
            'type'        => 'string',
            'searchable'  => true,
        ]);

        $this->addColumn([
            'index'         => 'lead_value',
            'label'         => trans('admin::app.datagrid.deal_amount'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'closure'       => function ($row) {
                return round($row->lead_value, 2);
            },
        ]);

        $this->addColumn([
            'index'             => 'created_at',
            'label'             => trans('admin::app.datagrid.created_at'),
            'type'              => 'string',
            'sortable'          => true,
            'filterable_type'   => 'date_range',
            'closure'           => function ($row) {
                return core()->formatDate($row->created_at);
            },
        ]);

        $this->addColumn([
            'index'     => 'user_name',
            'label'     => trans('admin::app.datagrid.contact_person'),
            'type'      => 'string'
        ]);

        $this->addColumn([
            'index'              => 'status',
            'label'              => trans('admin::app.datagrid.status'),
            'type'               => 'boolean',
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                [
                    'label' => trans('admin::app.datagrid.active'),
                    'value' => 1,
                ], [
                    'label' => trans('admin::app.datagrid.inactive'),
                    'value' => 0,
                ],
            ],
            'closure'            => function ($row) {
                if ($row->status == 1) {
                    return '<span class="badge badge-round badge-primary"></span>' . trans('admin::app.datagrid.active');
                } else {
                    return '<span class="badge badge-round badge-danger"></span>' . trans('admin::app.datagrid.inactive');
                }
            },
        ]);
    }

    public function prepareActions()
    {
        // $this->addAction([
        //     'title'  => trans('ui::app.datagrid.edit'),
        //     'method' => 'GET',
        //     'route'  => 'admin.contacts.persons.edit',
        //     'icon'   => 'icon pencil-icon',
        // ]);

        // $this->addAction([
        //     'title'        => trans('ui::app.datagrid.delete'),
        //     'method'       => 'DELETE',
        //     'route'        => 'admin.contacts.persons.delete',
        //     'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => trans('admin::app.contacts.persons.person')]),
        //     'icon'         => 'icon trash-icon',
        // ]);
    }

    public function prepareMassActions()
    {
        // $this->addMassAction([
        //     'type'   => 'delete',
        //     'label'  => trans('ui::app.datagrid.delete'),
        //     'action' => route('admin.contacts.persons.mass-delete'),
        //     'method' => 'PUT',
        // ]);
    }
}
