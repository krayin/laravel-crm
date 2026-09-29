<?php

namespace Webkul\GoogleContact\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\GoogleContact\Contracts\GoogleContactAccount;

class GoogleContactAccountRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return GoogleContactAccount::class;
    }
}
