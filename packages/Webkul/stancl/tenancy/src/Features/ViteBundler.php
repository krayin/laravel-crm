<?php

declare(strict_types=1);

namespace Stancl\Tenancy\Features;

use Illuminate\Foundation\Application;
use Stancl\Tenancy\Contracts\Feature;
use Stancl\Tenancy\Tenancy;
use Stancl\Tenancy\Vite;

class ViteBundler implements Feature
{
    /** @var Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bootstrap(Tenancy $tenancy): void
    {
        $this->app->singleton(\Illuminate\Foundation\Vite::class, Vite::class);
    }
}
