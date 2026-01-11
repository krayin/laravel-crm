<?php

namespace SuiteZap\LawFirm\DataGrids;

use Illuminate\Support\Facades\DB;

class LeadProcessosDataGrid extends ProcessoDataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        parent::prepareQueryBuilder();

        $queryBuilder = $this->queryBuilder;

        // Get lead_id from the route parameter
        $leadId = request()->route()->parameter('id');

        if ($leadId) {
            $queryBuilder->where('processos.lead_id', $leadId);
        }

        $this->setQueryBuilder($queryBuilder);

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

        // Removido 'cliente_nome' pois estamos visualizando o Lead

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
}
