<?php

namespace Webkul\GoogleContact\Services;

use Google\Client;

class GoogleClientFactory
{
    /**
     * Scopes requested during the OAuth consent flow.
     *
     * @var array
     */
    protected const SCOPES = [
        'https://www.googleapis.com/auth/contacts',
        'https://www.googleapis.com/auth/userinfo.email',
    ];

    /**
     * Build a Google API client configured with this app's OAuth credentials.
     */
    public function make(): Client
    {
        $client = new Client;

        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setScopes(self::SCOPES);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return $client;
    }
}
