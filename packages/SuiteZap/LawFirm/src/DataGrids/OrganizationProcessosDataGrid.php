<?php

namespace SuiteZap\LawFirm\DataGrids;

class OrganizationProcessosDataGrid extends ProcessoDataGrid
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

        // Get organization_id from the route parameter
        $organizationId = request()->route()->parameter('id');

        if ($organizationId) {
            $queryBuilder->where('persons.organization_id', $organizationId);
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
        parent::prepareColumns();

        // Não remover cliente_nome aqui, pois o processo é da Pessoa vinculada à Organização.
        // É útil ver de qual pessoa é o processo dentro da organização.
    }
}
