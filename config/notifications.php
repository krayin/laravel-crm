<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Delivery Driver
    |--------------------------------------------------------------------------
    |
    | How a notification reaches the admin's browser, set with NOTIFICATION_DRIVER:
    |
    |   polling  The browser asks over HTTP on a timer. Needs no socket server,
    |            no third-party account and no queue worker, so it works on any
    |            hosting. This is the default.
    |
    |   reverb   Instant, over a WebSocket hosted alongside the application.
    |            Needs `php artisan reverb:start` and a queue worker running,
    |            and the REVERB_* values below filled in.
    |
    |   pusher   Instant, over the hosted Pusher service. Needs a Pusher account,
    |            a queue worker running, and the PUSHER_* values filled in.
    |
    | Polling is also the fallback. Naming a socket driver is not the same as
    | having set one up, so a driver whose credentials are incomplete — or whose
    | BROADCAST_DRIVER points somewhere else — resolves to polling rather than
    | publishing into the void and leaving the bell silently dead.
    |
    | Setting up either socket driver is a matter of filling in .env and
    | rebuilding the admin assets; the dependencies and client code already ship.
    | See NOTIFICATIONS.md.
    |
    */

    'driver' => env('NOTIFICATION_DRIVER', 'polling'),

    /*
    |--------------------------------------------------------------------------
    | Poll Interval
    |--------------------------------------------------------------------------
    |
    | Seconds between checks when polling, set with NOTIFICATION_POLL_INTERVAL.
    |
    | A shorter interval feels faster but makes one request per signed-in user
    | per interval, so on a busy install this is better raised than lowered. A
    | floor of five seconds is enforced whatever is configured here.
    |
    */

    'poll_interval' => (int) env('NOTIFICATION_POLL_INTERVAL', 30),

    /*
    |--------------------------------------------------------------------------
    | Driver Requirements
    |--------------------------------------------------------------------------
    |
    | What each socket driver needs before it can deliver anything, mapping the
    | environment variable an administrator sets to the configuration value it
    | ends up in.
    |
    | The configuration value is what gets checked, not the environment variable
    | directly: once `config:cache` has been run — as it is in production —
    | `env()` returns null for everything, which would otherwise force a
    | correctly configured install back to polling.
    |
    | A driver missing any of these is reported on the configuration screen and
    | falls back to polling.
    |
    */

    'requires' => [
        /*
         * REVERB_HOST is where the application and the browser connect to, which is not the same
         * question as REVERB_SERVER_HOST — the address the Reverb server listens on. The two
         * differ behind a reverse proxy, where Reverb binds 0.0.0.0:8080 while browsers reach it
         * on port 443. Only the address clients dial is a requirement here; a server bound
         * somewhere unreachable is a deployment problem, not a missing setting.
         */
        'reverb' => [
            'REVERB_APP_ID' => 'broadcasting.connections.reverb.app_id',
            'REVERB_APP_KEY' => 'broadcasting.connections.reverb.key',
            'REVERB_APP_SECRET' => 'broadcasting.connections.reverb.secret',
            'REVERB_HOST' => 'broadcasting.connections.reverb.options.host',
        ],

        'pusher' => [
            'PUSHER_APP_ID' => 'broadcasting.connections.pusher.app_id',
            'PUSHER_APP_KEY' => 'broadcasting.connections.pusher.key',
            'PUSHER_APP_SECRET' => 'broadcasting.connections.pusher.secret',
            'PUSHER_APP_CLUSTER' => 'broadcasting.connections.pusher.options.cluster',
        ],
    ],

];
