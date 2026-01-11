<?php

namespace SuiteZap\LawFirm\DataGrids;

class PersonProcessosDataGrid extends ProcessoDataGrid
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

        // Get person_id from the route parameter
        $personId = request()->route()->parameter('id');

        if ($personId) {
            $queryBuilder->where('processos.person_id', $personId);
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
