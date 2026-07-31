<?php

namespace Webkul\GoogleContact\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\GoogleContact\Contracts\ContactExportBatch;

class ContactExportBatchRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ContactExportBatch::class;
    }
}
