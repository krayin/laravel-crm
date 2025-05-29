<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Database\Concerns;

use Stancl\Tenancy\Contracts\UniqueIdentifierGenerator;

trait GeneratesIds
{
    public static function bootGeneratesIds()
    {
        static::creating(function (self $model) {
            if (! $model->getKey() && $model->shouldGenerateId()) {
                $model->setAttribute($model->getKeyName(), app(UniqueIdentifierGenerator::class)->generate($model));
            }
        });
    }

    public function getIncrementing()
    {
        return ! app()->bound(UniqueIdentifierGenerator::class);
    }

    public function shouldGenerateId(): bool
    {
        return ! $this->getIncrementing();
    }

    public function getKeyType()
    {
        return $this->shouldGenerateId() ? 'string' : $this->keyType;
    }
}
