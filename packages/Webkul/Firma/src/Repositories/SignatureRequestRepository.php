<?php

namespace Webkul\Firma\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Firma\Contracts\SignatureRequest;

class SignatureRequestRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return SignatureRequest::class;
    }
}
