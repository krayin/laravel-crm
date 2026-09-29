<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Mail Receiver
    |--------------------------------------------------------------------------
    |
    | This option controls the default mail receiver that is used to receive any email
    | messages sent by third party application.
    |
    | Supported: "webklex-imap", "sendgrid"
    |
    */

    'default' => env('MAIL_RECEIVER_DRIVER', 'sendgrid'),

    /*
    |--------------------------------------------------------------------------
    | Inbound Parse Token
    |--------------------------------------------------------------------------
    |
    | Shared secret for the inbound parse webhook. The request arrives from the mail
    | provider rather than from a signed-in admin, so the route carries no session and
    | no CSRF token, and this secret is what tells a genuine delivery from a forged one.
    |
    | Send it as an "X-Inbound-Token" header, or as the password of HTTP basic
    | credentials embedded in the webhook URL where the provider only supports that.
    |
    | Until it is set the endpoint answers 503, which providers retry, so mail waits in
    | their queue rather than being dropped.
    |
    */

    'inbound_token' => env('MAIL_RECEIVER_INBOUND_TOKEN'),
];
