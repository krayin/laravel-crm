<?php

namespace SuiteZap\LawFirm\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class ProcessoDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'id';

    /**
     * Default sort order.
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('processos')
            ->leftJoin('persons', 'processos.person_id', '=', 'persons.id')
            ->addSelect(
                'processos.id',
                'processos.titulo',
                'processos.numero_cnj',
                'processos.status',
                'processos.area_direito',
                'processos.data_audiencia',
                'persons.name as cliente_nome'
            );

        $this->addFilter('id', 'processos.id');
        $this->addFilter('titulo', 'processos.titulo');
        $this->addFilter('numero_cnj', 'processos.numero_cnj');
        $this->addFilter('cliente_nome', 'persons.name');
        $this->addFilter('area_direito', 'processos.area_direito');
        $this->addFilter('status', 'processos.status');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     *
     * @return void
     */
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
            'index' => 'cliente_nome',
            'label' => trans('lawfirm::app.processos.form.person'), // 'Cliente'
            'type' => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'area_direito',
            'label' => trans('lawfirm::app.processos.form.area'),
            'type' => 'string',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'data_audiencia',
            'label' => trans('lawfirm::app.processos.form.data_audiencia'),
            'type' => 'datetime',
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

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        // Ação de Editar
        $this->addAction([
            'title' => trans('lawfirm::app.processos.edit'),
            'method' => 'GET',
            'route' => 'admin.processos.edit',
            'icon' => 'icon-edit',
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
            'url' => function ($row) {
                return route('admin.processos.destroy', $row->id);
            },
        ]);
    }
}
