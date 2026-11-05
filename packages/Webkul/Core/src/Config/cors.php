<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS)
    |--------------------------------------------------------------------------
    |
    | CORS is enabled only for the public web form endpoints, so a form embedded
    | on another website can load and submit. Every other route, including the
    | whole admin panel, stays same-origin only.
    |
    | No origin is trusted by default. List the websites that embed your web
    | forms in CORS_ALLOWED_ORIGINS, comma separated, as exact origins:
    |
    |     CORS_ALLOWED_ORIGINS=https://www.example.com,https://shop.example.com
    |
    | A bare "*" is ignored: it would let any website call these endpoints
    | from a visitor's browser and read the responses.
    |
    */

    /*
     * The public web form endpoints (form.js, form.html and the submission).
     * The authenticated admin preview at web-forms/form/{id} is not matched.
     */
    'paths' => [
        'web-forms/forms/*',
    ],

    /*
     * The web form loads with GET and submits with POST.
     */
    'allowed_methods' => ['GET', 'POST'],

    /*
     * Exact trusted origins (scheme, host and optional port), read from CORS_ALLOWED_ORIGINS.
     */
    'allowed_origins' => array_values(array_filter(
        array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))),
        fn (string $origin) => $origin !== '' && $origin !== '*'
    )),

    /*
     * Patterns that can be used with `preg_match` to match the origin.
     */
    'allowed_origins_patterns' => [],

    /*
     * The request headers the web form sends.
     */
    'allowed_headers' => ['Accept', 'Content-Type', 'X-Requested-With'],

    /*
     * Sets the Access-Control-Expose-Headers response header with these headers.
     */
    'exposed_headers' => [],

    /*
     * Sets the Access-Control-Max-Age response header when > 0.
     */
    'max_age' => 0,

    /*
     * Cookies and HTTP authentication are never sent with cross-origin requests, so a trusted
     * origin can still only reach what an anonymous visitor could.
     */
    'supports_credentials' => false,
];
