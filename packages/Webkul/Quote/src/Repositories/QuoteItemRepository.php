<?php

namespace Webkul\Quote\Repositories;

use Webkul\Core\Eloquent\Repository;

class QuoteItemRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Quote\Contracts\QuoteItem';
    }
}