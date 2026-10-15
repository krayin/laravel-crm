<?php

namespace Webkul\Admin\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'contacts.person.create.after' => [
            'Webkul\Admin\Listeners\Person@linkToEmail',
            'Webkul\Admin\Listeners\Notification@created',
        ],

        'lead.create.after' => [
            'Webkul\Admin\Listeners\Lead@linkToEmail',
            'Webkul\Admin\Listeners\Notification@created',
        ],

        'activity.create.after' => [
            'Webkul\Admin\Listeners\Activity@afterUpdateOrCreate',
            'Webkul\Admin\Listeners\Notification@created',
        ],

        'activity.update.after' => [
            'Webkul\Admin\Listeners\Activity@afterUpdateOrCreate',
        ],

        /**
         * Notifications hang off the events the modules already dispatch, so no module's
         * controller has to know that notifications exist.
         *
         * Only creations are notified. An update fires on every edit, including the routine ones
         * a user makes while working a record, and a bell that fills up with those stops being
         * read at all.
         */
        'quote.create.after' => [
            'Webkul\Admin\Listeners\Notification@created',
        ],

        'product.create.after' => [
            'Webkul\Admin\Listeners\Notification@created',
        ],

        'email.create.after' => [
            'Webkul\Admin\Listeners\Notification@created',
        ],
    ];
}
