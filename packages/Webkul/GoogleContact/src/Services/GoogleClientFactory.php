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
     * Determine whether the Google OAuth credentials are configured, so the consent flow can be
     * skipped gracefully instead of letting the Google client throw when they are missing.
     */
    public function isConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }

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
