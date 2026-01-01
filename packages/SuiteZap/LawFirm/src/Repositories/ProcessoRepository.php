<?php

namespace SuiteZap\LawFirm\Repositories;

use Webkul\Core\Eloquent\Repository;

class ProcessoRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'SuiteZap\LawFirm\Contracts\Processo';
    }
}
