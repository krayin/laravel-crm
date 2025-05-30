<?php

namespace Stancl\Tenancy\Concerns;

trait ExtendsLaravelCommand
{
    protected function specifyTenantSignature(): void
    {
        $this->specifyParameters();
    }

    public function getName(): ?string
    {
        return static::getTenantCommandName();
    }

    public static function getDefaultName(): ?string
    {
        return static::getTenantCommandName();
    }

    abstract protected static function getTenantCommandName(): string;
}
