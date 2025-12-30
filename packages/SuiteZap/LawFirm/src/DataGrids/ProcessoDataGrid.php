<?php

namespace SuiteZap\LawFirm\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class ProcessoDataGrid extends DataGrid
{
    /**
     * Importante: Nas novas versões, usa-se primaryColumn em vez de index
     */
    protected $primaryColumn = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('processos')
            ->addSelect(
                'processos.id',
                'processos.titulo',
                'processos.numero_cnj',
                'processos.status',
                'processos.created_at'
            );

        $this->addFilter('id', 'processos.id');
        $this->addFilter('titulo', 'processos.titulo');
        $this->addFilter('numero_cnj', 'processos.numero_cnj');

        return $queryBuilder;
    }

    public function prepareColumns()
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('lawfirm::app.processos.datagrid.id'),
            'type' => 'integer',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'titulo',
            'label' => trans('lawfirm::app.processos.datagrid.titulo'),
            'type' => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'numero_cnj',
            'label' => trans('lawfirm::app.processos.datagrid.cnj'),
            'type' => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('lawfirm::app.processos.datagrid.status'),
            'type' => 'string',
            'sortable' => true,
        ]);
    }

    public function prepareActions()
    {
        // Ação de Editar
        $this->addAction([
            'title' => trans('lawfirm::app.processos.edit'),
            'method' => 'GET',
            'route' => 'admin.processos.edit',
            'icon' => 'icon-edit',
            // AQUI ESTÁ A CORREÇÃO: Forçamos a geração da URL manualmente
            'url' => function ($row) {
                return route('admin.processos.edit', $row->id);
            },
        ]);

        // Ação de Excluir
        $this->addAction([
            'title' => trans('lawfirm::app.processos.delete-success'),
            'method' => 'DELETE',
            'route' => 'admin.processos.destroy',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete'),
            'icon' => 'icon-delete',
            // CORREÇÃO AQUI TAMBÉM:
            'url' => function ($row) {
                return route('admin.processos.destroy', $row->id);
            },
        ]);
    }
}
