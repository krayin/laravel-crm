<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Exceptions;

use Exception;

class ModelNotSyncMasterException extends Exception
{
    public function __construct(string $class)
    {
        parent::__construct("Model of $class class is not a SyncMaster model. Make sure you're using the central model to make changes to synced resources when you're in the central context.");
    }
}
