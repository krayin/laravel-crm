<?php

namespace Webkul\GoogleContact\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\GoogleContact\Contracts\ContactExportBatchItem;

class ContactExportBatchItemRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ContactExportBatchItem::class;
    }
}
