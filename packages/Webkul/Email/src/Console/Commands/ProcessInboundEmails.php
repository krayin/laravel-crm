<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use League\OAuth2\Client\Provider\GenericProvider as Microsoft;
use League\OAuth2\Client\Token\AccessToken;
use Illuminate\Support\Facades\Storage;

class ProcessInboundEmails extends Command
{
    protected $signature = 'emails:process-inbound';
    protected $description = 'Process inbound emails using Outlook IMAP via OAuth2';

    public function handle()
    {
        $this->info('--- Starting inbound email processing ---');

        $tokenPath = storage_path('app/oauth-token.json');

        if (!file_exists($tokenPath)) {
            $this->error('OAuth token not found. Please run `php artisan test:read-mail` first.');
            return 1;
        }

        $tokenData = json_decode(file_get_contents($tokenPath), true);
        $accessToken = new AccessToken($tokenData);

        // Refresh token if expired
        if ($accessToken->hasExpired()) {
            $this->warn('Access token expired. Refreshing...');

            $provider = new Microsoft([
                'clientId'                => env('OAUTH_CLIENT_ID'),
                'clientSecret'            => env('OAUTH_CLIENT_SECRET'),
                'redirectUri'             => env('OAUTH_REDIRECT_URI'),
                'urlAuthorize'            => 'https://login.microsoftonline.com/' . env('OAUTH_TENANT_ID') . '/oauth2/v2.0/authorize',
                'urlAccessToken'          => 'https://login.microsoftonline.com/' . env('OAUTH_TENANT_ID') . '/oauth2/v2.0/token',
                'urlResourceOwnerDetails' => '',
                'scopes'                  => 'offline_access IMAP.AccessAsUser.All Mail.Read',
            ]);

            $newToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $accessToken->getRefreshToken(),
            ]);

            file_put_contents($tokenPath, json_encode($newToken->jsonSerialize()));
            $accessToken = $newToken;
            $this->info('Access token refreshed successfully.');
        }

        // Connect IMAP
        $this->info('Connecting to Outlook IMAP...');
        try {
            $client = Client::account('default');
            $client->connect([
                'host'           => 'outlook.office365.com',
                'port'           => 993,
                'encryption'     => 'ssl',
                'validate_cert'  => true,
                'username'       => env('OUTLOOK_EMAIL'),
                'password'       => $accessToken->getToken(),
                'authentication' => 'xoauth2',
            ]);

            $inbox = $client->getFolder('INBOX');
            $unreadMessages = $inbox->messages()->unseen()->limit(10)->get();

            foreach ($unreadMessages as $message) {
                $subject = $message->getSubject();
                $from = $message->getFrom()[0]->mail;
                $body = $message->getTextBody();

                $this->info("Processing email from: $from | Subject: $subject");

                Storage::put('emails/' . time() . '-' . preg_replace('/[^a-z0-9]/i', '_', $subject) . '.txt', $body);

                $message->setFlag('Seen');
            }

            $this->info('--- Inbound email processing completed successfully ---');
        } catch (\Exception $e) {
            $this->error('Error during IMAP connection or processing: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
