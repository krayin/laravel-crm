# Job Pipeline

<p align="center">
    <img width="600" src="https://i.imgur.com/AcVXakZ.png" alt="Job Pipeline" />
</p>

The `JobPipeline` is a simple, yet **extremely powerful** class that lets you **convert any (series of) jobs into event listeners.**

You may use a job pipeline like any other listener, so you can register it in the `EventServiceProvider` using the `$listen` array, or in any other place using `Event::listen()` — up to you.

## Creating job pipelines

> These code snippets will use examples from [my multi-tenancy package](https://github.com/stancl/tenancy).

To create a job pipeline, start by specifying the jobs you want to use:

```php
<?php

use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Jobs\{CreateDatabase, MigrateDatabase, SeedDatabase};

JobPipeline::make([
    CreateDatabase::class,
    MigrateDatabase::class,
    SeedDatabase::class,
])
```

Then, specify what variable you want to pass to the jobs. This will usually come from the event.

```php
<?php

use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Jobs\{CreateDatabase, MigrateDatabase, SeedDatabase};
use Stancl\Tenancy\Events\TenantCreated;

JobPipeline::make([
    CreateDatabase::class,
    MigrateDatabase::class,
    SeedDatabase::class,
])->send(function (TenantCreated $event) {
    return $event->tenant;
})
```

Next, decide if you want to queue the pipeline. By default, pipelines are synchronous (= not queued) by default.

> 🔥 If you **do** want pipelines to be queued by default, you can do that by setting a static property:
`\Stancl\JobPipeline\JobPipeline::$shouldBeQueuedByDefault = true;`

```php
<?php

use Stancl\Tenancy\Events\TenantCreated;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Jobs\{CreateDatabase, MigrateDatabase, SeedDatabase};

JobPipeline::make([
    CreateDatabase::class,
    MigrateDatabase::class,
    SeedDatabase::class,
])->send(function (TenantCreated $event) {
    return $event->tenant;
})->shouldBeQueued(true)
```

Finally, convert the pipeline to a listener and bind it to an event:

```php
<?php

use Stancl\Tenancy\Events\TenantCreated;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Jobs\{CreateDatabase, MigrateDatabase, SeedDatabase};
use Illuminate\Support\Facades\Event;

Event::listen(TenantCreated::class, JobPipeline::make([
    CreateDatabase::class,
    MigrateDatabase::class,
    SeedDatabase::class,
])->send(function (TenantCreated $event) {
    return $event->tenant;
})->shouldBeQueued(true)->toListener());
```

Note that you can use job pipelines even for converting single jobs to event listeners. That's useful if you have some logic in job classes and don't want to create listener classes just to be able to run these jobs as a result of an event being fired.

Tip: Returning `false` from a job cancels the execution of all following jobs in the pipeline. This can be useful to cancel a job pipeline that creates, migrates, and seeds databases if the create database job exists (e.g. because it detects that a database already exists). So it can be good to separate jobs into multiple pipelines, so that each logical category of jobs can be stopped individually.
