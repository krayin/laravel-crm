<?php

namespace Webkul\Automation\Repositories;

use Webkul\Automation\Contracts\Webhook;
use Webkul\Core\Eloquent\Repository;

class WebhookRepository extends Repository
{
    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return Webhook::class;
    }
}
