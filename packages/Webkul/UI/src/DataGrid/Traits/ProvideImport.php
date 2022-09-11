<?php

namespace Webkul\UI\DataGrid\Traits;

trait ProvideImport
{
    /**
     * Export data.
     *
     * @return object
     */
    public function import()
    {
        if ($this->import) {
        
            $this->init();

            $this->importCsv();

            $this->prepareTabFilters();

            $this->prepareActions();

            $this->prepareMassActions();

            $this->prepareQueryBuilder();

            $this->getCollection();

            $this->transformColumnsForExport();

            return $this->collection;
        }
        return [];
    }
}
