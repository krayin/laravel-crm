<?php

namespace Webkul\GoogleContact\Services;

use Google\Client;
use RuntimeException;
use Webkul\GoogleContact\Models\GoogleContactAccount;
use Webkul\GoogleContact\Repositories\GoogleContactAccountRepository;

class TokenRefresher
{
    public function __construct(
        protected GoogleClientFactory $clientFactory,
        protected GoogleContactAccountRepository $googleContactAccountRepository,
    ) {}

    /**
     * Build an authenticated Google client for the given account, refreshing
     * the access token first if it has expired.
     */
    public function clientFor(GoogleContactAccount $account): Client
    {
        $client = $this->clientFactory->make();

        $client->setAccessToken([
            'access_token' => $account->access_token,
            'refresh_token' => $account->refresh_token,
            'expires_in' => $account->expires_at ? now()->diffInSeconds($account->expires_at, false) : 0,
            'created' => now()->timestamp,
        ]);

        if ($client->isAccessTokenExpired()) {
            $this->refresh($client, $account);
        }

        return $client;
    }

    /**
     * Refresh and persist a new access token.
     *
     * Google frequently omits `refresh_token` on refresh responses, so the
     * previously stored refresh token is only replaced when a new one is given.
     */
    protected function refresh(Client $client, GoogleContactAccount $account): void
    {
        if (! $account->refresh_token) {
            throw new RuntimeException('Google account has no refresh token; the user must reconnect their Google account.');
        }

        $token = $client->fetchAccessTokenWithRefreshToken($account->refresh_token);

        if (isset($token['error'])) {
            throw new RuntimeException('Failed to refresh Google access token: '.($token['error_description'] ?? $token['error']));
        }

        $this->googleContactAccountRepository->update([
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'] ?? $account->refresh_token,
            'expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
        ], $account->id);

        $client->setAccessToken($token);
    }
}
