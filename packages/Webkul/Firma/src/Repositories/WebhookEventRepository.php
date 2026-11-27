<?php

namespace Webkul\Firma\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Firma\Contracts\WebhookEvent;

class WebhookEventRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return WebhookEvent::class;
    }
}
