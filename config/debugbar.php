<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Debugbar Settings
     |--------------------------------------------------------------------------
     |
     | Debugbar is enabled by default, when debug is set to true in app.php.
     | You can override the value by setting enable to true or false instead of null.
     |
     | You can provide an array of URI's that must be ignored (eg. 'api/*')
     |
     */

    'enabled' => env('DEBUGBAR_ENABLED'),
    'hide_empty_tabs' => env('DEBUGBAR_HIDE_EMPTY_TABS', true), // Hide tabs until they have content
    'except' => [
        'telescope*',
        'horizon*',
        '_boost/browser-logs',
    ],

    /*
     |--------------------------------------------------------------------------
     | Storage settings
     |--------------------------------------------------------------------------
     |
     | Debugbar stores data for session/ajax requests.
     | You can disable this, so the debugbar stores data in headers/session,
     | but this can cause problems with large data collectors.
     | By default, file storage (in the storage folder) is used. Redis and PDO
     | can also be used. For PDO, run the package migrations first.
     |
     | Warning: Enabling storage.open will allow everyone to access previous
     | request, do not enable open storage in publicly available environments!
     | Specify a callback if you want to limit based on IP or authentication.
     | Leaving it to null will allow localhost only.
     */
    'storage' => [
        'enabled'    => env('DEBUGBAR_STORAGE_ENABLED', true),
        'open'       => env('DEBUGBAR_OPEN_STORAGE'), // bool/callback.
        'driver'     => env('DEBUGBAR_STORAGE_DRIVER', 'file'), // redis, file, pdo, socket, custom
        'path'       => env('DEBUGBAR_STORAGE_PATH', storage_path('debugbar')), // For file driver
        'connection' => env('DEBUGBAR_STORAGE_CONNECTION'), // Leave null for default connection (Redis/PDO)
        'provider'   => env('DEBUGBAR_STORAGE_PROVIDER', ''), // Instance of StorageInterface for custom driver
        'hostname'   => env('DEBUGBAR_STORAGE_HOSTNAME', '127.0.0.1'), // Hostname to use with the "socket" driver
        'port'       => env('DEBUGBAR_STORAGE_PORT', 2304), // Port to use with the "socket" driver
    ],

    /*
    |--------------------------------------------------------------------------
    | Editor
    |--------------------------------------------------------------------------
    |
    | Choose your preferred editor to use when clicking file name.
    |
    | Supported: "sublime", "textmate", "emacs", "macvim", "codelite",
    |            "phpstorm", "phpstorm-remote", "idea", "idea-remote",
    |            "vscode", "vscode-insiders", "vscode-remote", "vscode-insiders-remote",
    |            "vscodium", "nova", "xdebug", "atom", "espresso",
    |            "netbeans", "cursor", "windsurf", "zed", "antigravity"
    |
    */

    'editor' => env('DEBUGBAR_EDITOR') ?: env('IGNITION_EDITOR', 'phpstorm'),

    /*
    |--------------------------------------------------------------------------
    | Remote Path Mapping
    |--------------------------------------------------------------------------
    |
    | If you are using a remote dev server, like Laravel Homestead, Docker, or
    | even a remote VPS, it will be necessary to specify your path mapping.
    |
    | Leaving one, or both of these, empty or null will not trigger the remote
    | URL changes and Debugbar will treat your editor links as local files.
    |
    | "remote_sites_path" is an absolute base path for your sites or projects
    | in Homestead, Vagrant, Docker, or another remote development server.
    |
    | Example value: "/home/vagrant/Code"
    |
    | "local_sites_path" is an absolute base path for your sites or projects
    | on your local computer where your IDE or code editor is running on.
    |
    | Example values: "/Users/<name>/Code", "C:\Users\<name>\Documents\Code"
    |
    */

    'remote_sites_path' => env('DEBUGBAR_REMOTE_SITES_PATH'),
    'local_sites_path' => env('DEBUGBAR_LOCAL_SITES_PATH', env('IGNITION_LOCAL_SITES_PATH')),

    /*
     |--------------------------------------------------------------------------
     | Vendors
     |--------------------------------------------------------------------------
     |
     | Vendor files are included by default, but can be set to false.
     | This can also be set to 'js' or 'css', to only include javascript or css vendor files.
     | Vendor files are for css: font-awesome (including fonts) and highlight.js (css files)
     | and for js: jquery and highlight.js
     | So if you want syntax highlighting, set it to true.
     | jQuery is set to not conflict with existing jQuery scripts.
     |
     */

    'include_vendors' => env('DEBUGBAR_INCLUDE_VENDORS', true),

    /*
     |--------------------------------------------------------------------------
     | Capture Ajax Requests
     |--------------------------------------------------------------------------
     |
     | The Debugbar can capture Ajax requests and display them. If you don't want this (ie. because of errors),
     | you can use this option to disable sending the data through the headers.
     |
     | Optionally, you can also send ServerTiming headers on ajax requests for the Chrome DevTools.
     |
     | Note for your request to be identified as ajax requests they must either send the header
     | X-Requested-With with the value XMLHttpRequest (most JS libraries send this), or have application/json as a Accept header.
     |
     | By default `ajax_handler_auto_show` is set to true allowing ajax requests to be shown automatically in the Debugbar.
     | Changing `ajax_handler_auto_show` to false will prevent the Debugbar from reloading.
     |
     | You can defer loading the dataset, so it will be loaded with ajax after the request is done. (Experimental)
     */

    'capture_ajax' => env('DEBUGBAR_CAPTURE_AJAX', true),
    'add_ajax_timing' => env('DEBUGBAR_ADD_AJAX_TIMING', false),
    'ajax_handler_auto_show' => env('DEBUGBAR_AJAX_HANDLER_AUTO_SHOW', true),
    'ajax_handler_enable_tab' => env('DEBUGBAR_AJAX_HANDLER_ENABLE_TAB', true),
    'defer_datasets' => env('DEBUGBAR_DEFER_DATASETS', false),
    /*
     |--------------------------------------------------------------------------
     | Custom Error Handler for Deprecated warnings
     |--------------------------------------------------------------------------
     |
     | When enabled, the Debugbar shows deprecated warnings for Symfony components
     | in the Messages tab.
     |
     | You can set a custom error reporting level to filter which errors are
     | handled. For example, to exclude deprecation warnings:
     |   E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED
     |
     | To exclude notices, strict warnings, and deprecations:
     |   E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED
     |
     | Defaults to E_ALL (all errors).
     |
     */
    'error_handler' => env('DEBUGBAR_ERROR_HANDLER', false),
    'error_level' => env('DEBUGBAR_ERROR_LEVEL', E_ALL),

    /*
     |--------------------------------------------------------------------------
     | Clockwork integration
     |--------------------------------------------------------------------------
     |
     | The Debugbar can emulate the Clockwork headers, so you can use the Chrome
     | Extension, without the server-side code. It uses Debugbar collectors instead.
     |
     */
    'clockwork' => env('DEBUGBAR_CLOCKWORK', false),

    /*
     |--------------------------------------------------------------------------
     | DataCollectors
     |--------------------------------------------------------------------------
     |
     | Enable/disable DataCollectors
     |
     */

    'collectors' => [
        'phpinfo'         => env('DEBUGBAR_COLLECTORS_PHPINFO', false),         // Php version
        'messages'        => env('DEBUGBAR_COLLECTORS_MESSAGES', true),         // Messages
        'time'            => env('DEBUGBAR_COLLECTORS_TIME', true),             // Time Datalogger
        'memory'          => env('DEBUGBAR_COLLECTORS_MEMORY', true),           // Memory usage
        'exceptions'      => env('DEBUGBAR_COLLECTORS_EXCEPTIONS', true),       // Exception displayer
        'log'             => env('DEBUGBAR_COLLECTORS_LOG', true),              // Logs from Monolog (merged in messages if enabled)
        'db'              => env('DEBUGBAR_COLLECTORS_DB', true),               // Show database (PDO) queries and bindings
        'views'           => env('DEBUGBAR_COLLECTORS_VIEWS', true),            // Views with their data
        'route'           => env('DEBUGBAR_COLLECTORS_ROUTE', false),           // Current route information
        'auth'            => env('DEBUGBAR_COLLECTORS_AUTH', false),            // Display Laravel authentication status
        'gate'            => env('DEBUGBAR_COLLECTORS_GATE', true),             // Display Laravel Gate checks
        'session'         => env('DEBUGBAR_COLLECTORS_SESSION', false),         // Display session data
        'symfony_request' => env('DEBUGBAR_COLLECTORS_SYMFONY_REQUEST', true),  // Only one can be enabled..
        'mail'            => env('DEBUGBAR_COLLECTORS_MAIL', true),             // Catch mail messages
        'laravel'         => env('DEBUGBAR_COLLECTORS_LARAVEL', true),          // Laravel version and environment
        'events'          => env('DEBUGBAR_COLLECTORS_EVENTS', false),          // All events fired
        'default_request' => env('DEBUGBAR_COLLECTORS_DEFAULT_REQUEST', false), // Regular or special Symfony request logger
        'logs'            => env('DEBUGBAR_COLLECTORS_LOGS', false),            // Add the latest log messages
        'files'           => env('DEBUGBAR_COLLECTORS_FILES', false),           // Show the included files
        'config'          => env('DEBUGBAR_COLLECTORS_CONFIG', false),          // Display config settings
        'cache'           => env('DEBUGBAR_COLLECTORS_CACHE', false),           // Display cache events
        'models'          => env('DEBUGBAR_COLLECTORS_MODELS', true),           // Display models
        'livewire'        => env('DEBUGBAR_COLLECTORS_LIVEWIRE', true),         // Display Livewire (when available)
        'jobs'            => env('DEBUGBAR_COLLECTORS_JOBS', false),            // Display dispatched jobs
        'pennant'         => env('DEBUGBAR_COLLECTORS_PENNANT', false),         // Display Pennant feature flags
    ],

    /*
     |--------------------------------------------------------------------------
     | Extra options
     |--------------------------------------------------------------------------
     |
     | Configure some DataCollectors
     |
     */

    'options' => [
        'time' => [
            'memory_usage' => env('DEBUGBAR_OPTIONS_TIME_MEMORY_USAGE', false), // Calculated by subtracting memory start and end, it may be inaccurate
        ],
        'messages' => [
            'trace' => env('DEBUGBAR_OPTIONS_MESSAGES_TRACE', true),                  // Trace the origin of the debug message
            'capture_dumps' => env('DEBUGBAR_OPTIONS_MESSAGES_CAPTURE_DUMPS', false), // Capture laravel `dump();` as message
        ],
        'memory' => [
            'reset_peak' => env('DEBUGBAR_OPTIONS_MEMORY_RESET_PEAK', false),       // run memory_reset_peak_usage before collecting
            'with_baseline' => env('DEBUGBAR_OPTIONS_MEMORY_WITH_BASELINE', false), // Set boot memory usage as memory peak baseline
            'precision' => (int) env('DEBUGBAR_OPTIONS_MEMORY_PRECISION', 0),       // Memory rounding precision
        ],
        'auth' => [
            'show_name' => env('DEBUGBAR_OPTIONS_AUTH_SHOW_NAME', true),     // Also show the users name/email in the debugbar
            'show_guards' => env('DEBUGBAR_OPTIONS_AUTH_SHOW_GUARDS', true), // Show the guards that are used
        ],
        'gate' => [
            'trace' => false,      // Trace the origin of the Gate checks
        ],
        'db' => [
            'with_params'       => env('DEBUGBAR_OPTIONS_WITH_PARAMS', true),   // Render SQL with the parameters substituted
            'exclude_paths'     => [       // Paths to exclude entirely from the collector
                //'vendor/laravel/framework/src/Illuminate/Session', // Exclude sessions queries
            ],
            'backtrace'         => env('DEBUGBAR_OPTIONS_DB_BACKTRACE', true),   // Use a backtrace to find the origin of the query in your files.
            'backtrace_exclude_paths' => [],   // Paths to exclude from backtrace. (in addition to defaults)
            'timeline'          => env('DEBUGBAR_OPTIONS_DB_TIMELINE', false),  // Add the queries to the timeline
            'duration_background'  => env('DEBUGBAR_OPTIONS_DB_DURATION_BACKGROUND', true),   // Show shaded background on each query relative to how long it took to execute.
            'explain' => [                 // Show EXPLAIN output on queries
                'enabled' => env('DEBUGBAR_OPTIONS_DB_EXPLAIN_ENABLED', false),
            ],
            'hints'             => env('DEBUGBAR_OPTIONS_DB_HINTS', false),          // Show hints for common mistakes
            'show_copy'         => env('DEBUGBAR_OPTIONS_DB_SHOW_COPY', true),       // Show copy button next to the query,
            'only_slow_queries' => env('DEBUGBAR_OPTIONS_DB_ONLY_SLOW_QUERIES', true), // Only track queries that last longer than `slow_threshold`
            'slow_threshold'    => env('DEBUGBAR_OPTIONS_DB_SLOW_THRESHOLD', false), // Max query execution time (ms). Exceeding queries will be highlighted
            'memory_usage'      => env('DEBUGBAR_OPTIONS_DB_MEMORY_USAGE', false),   // Show queries memory usage
            'soft_limit'       => (int) env('DEBUGBAR_OPTIONS_DB_SOFT_LIMIT', 100),  // After the soft limit, no parameters/backtrace are captured
            'hard_limit'       => (int) env('DEBUGBAR_OPTIONS_DB_HARD_LIMIT', 500),  // After the hard limit, queries are ignored
        ],
        'mail' => [
            'timeline' => env('DEBUGBAR_OPTIONS_MAIL_TIMELINE', true),  // Add mails to the timeline
            'show_body' => env('DEBUGBAR_OPTIONS_MAIL_SHOW_BODY', true),
        ],
        'views' => [
            'timeline' => env('DEBUGBAR_OPTIONS_VIEWS_TIMELINE', true),                  // Add the views to the timeline
            'data' => env('DEBUGBAR_OPTIONS_VIEWS_DATA', false),                         // True for all data, 'keys' for only names, false for no parameters.
            'group' => (int) env('DEBUGBAR_OPTIONS_VIEWS_GROUP', 50),                    // Group duplicate views. Pass value to auto-group, or true/false to force
            'inertia_pages' => env('DEBUGBAR_OPTIONS_VIEWS_INERTIA_PAGES', 'js/Pages'),  // Path for Inertia views
            'exclude_paths' => [    // Add the paths which you don't want to appear in the views
                'vendor/filament'   // Exclude Filament components by default
            ],
        ],
        'route' => [
            'label' => env('DEBUGBAR_OPTIONS_ROUTE_LABEL', true),  // Show complete route on bar
        ],
        'session' => [
            'hiddens' => [], // Hides sensitive values using array paths
        ],
        'symfony_request' => [
            'label' => env('DEBUGBAR_OPTIONS_SYMFONY_REQUEST_LABEL', true),  // Show route on bar
            'hiddens' => [], // Hides sensitive values using array paths, example: request_request.password
        ],
        'events' => [
            'data' => env('DEBUGBAR_OPTIONS_EVENTS_DATA', false), // Collect events data, listeners
            'excluded' => [], // Example: ['eloquent.*', 'composing', Illuminate\Cache\Events\CacheHit::class]
        ],
        'logs' => [
            'file' => env('DEBUGBAR_OPTIONS_LOGS_FILE'),
        ],
        'cache' => [
            'values' => env('DEBUGBAR_OPTIONS_CACHE_VALUES', true), // Collect cache values
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Inject Debugbar in Response
     |--------------------------------------------------------------------------
     |
     | Usually, the debugbar is added just before </body>, by listening to the
     | Response after the App is done. If you disable this, you have to add them
     | in your template yourself. See http://phpdebugbar.com/docs/rendering.html
     |
     */

    'inject' => env('DEBUGBAR_INJECT', true),

    /*
     |--------------------------------------------------------------------------
     | Debugbar route prefix
     |--------------------------------------------------------------------------
     |
     | Sometimes you want to set route prefix to be used by Debugbar to load
     | its resources from. Usually the need comes from misconfigured web server or
     | from trying to overcome bugs like this: http://trac.nginx.org/nginx/ticket/97
     |
     */
    'route_prefix' => env('DEBUGBAR_ROUTE_PREFIX', '_debugbar'),

    /*
     |--------------------------------------------------------------------------
     | Debugbar route middleware
     |--------------------------------------------------------------------------
     |
     | Additional middleware to run on the Debugbar routes
     */
    'route_middleware' => [],

    /*
     |--------------------------------------------------------------------------
     | Debugbar route domain
     |--------------------------------------------------------------------------
     |
     | By default Debugbar route served from the same domain that request served.
     | To override default domain, specify it as a non-empty value.
     */
    'route_domain' => env('DEBUGBAR_ROUTE_DOMAIN'),

    /*
     |--------------------------------------------------------------------------
     | Debugbar theme
     |--------------------------------------------------------------------------
     |
     | Switches between light and dark theme. If set to auto it will respect system preferences
     | Possible values: auto, light, dark
     */
    'theme' => env('DEBUGBAR_THEME', 'auto'),

    /*
     |--------------------------------------------------------------------------
     | Backtrace stack limit
     |--------------------------------------------------------------------------
     |
     | By default, the Debugbar limits the number of frames returned by the 'debug_backtrace()' function.
     | If you need larger stacktraces, you can increase this number. Setting it to 0 will result in no limit.
     */
    'debug_backtrace_limit' => (int) env('DEBUGBAR_DEBUG_BACKTRACE_LIMIT', 50),
];
