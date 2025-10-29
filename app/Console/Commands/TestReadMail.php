<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\OAuth2\Client\Provider\GenericProvider;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Exceptions\AuthFailedException;

class TestReadMail extends Command
{
    protected $signature = 'test:read-mail';
    protected $description = 'Test reading Outlook mail via OAuth2 IMAP';

    public function handle()
    {
        $this->info('--- Starting Outlook OAuth2 IMAP Test ---');

        $tenantId = 'c1018ad6-79f6-4ce8-92a0-b797019cca0d';
        $clientId = '3bf5abf4-a3ee-40bb-b133-a69123a3e141';
        $clientSecret = env('OAUTH_CLIENT_SECRET'); // đảm bảo bạn có trong .env
        $redirectUri = 'http://localhost:8085/oauth/callback';
        $tokenPath = storage_path('app/outlook_token.json');

        $provider = new GenericProvider([
            'clientId'                => $clientId,
            'clientSecret'            => $clientSecret,
            'redirectUri'             => $redirectUri,
            'urlAuthorize'            => "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/authorize",
            'urlAccessToken'          => "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token",
            'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
            'scopes'                  => 'offline_access IMAP.AccessAsUser.All Mail.Read Mail.ReadWrite Mail.Send SMTP.Send User.Read'
        ]);

        $token = null;
        if (file_exists($tokenPath)) {
            $token = json_decode(file_get_contents($tokenPath), true);
            if (isset($token['expires']) && $token['expires'] < time()) {
                $this->info('Access token expired. Refreshing...');
                try {
                    $newToken = $provider->getAccessToken('refresh_token', [
                        'refresh_token' => $token['refresh_token']
                    ]);
                    file_put_contents($tokenPath, json_encode($newToken->jsonSerialize(), JSON_PRETTY_PRINT));
                    $token = $newToken->jsonSerialize();
                } catch (\Exception $e) {
                    $this->error('Token refresh failed: ' . $e->getMessage());
                    unlink($tokenPath);
                    return;
                }
            }
        }

        if (!$token) {
            $authUrl = $provider->getAuthorizationUrl();
            $this->info("Go to the following URL and authorize the app:\n" . $authUrl);
            $authCode = $this->ask('Enter the authorization code:');
            try {
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => trim($authCode),
                ]);
                file_put_contents($tokenPath, json_encode($accessToken->jsonSerialize(), JSON_PRETTY_PRINT));
                $token = $accessToken->jsonSerialize();
                $this->info('Access token saved.');
            } catch (\Exception $e) {
                $this->error('Failed to get access token: ' . $e->getMessage());
                return;
            }
        }

        // --- Connect IMAP using XOAUTH2 ---
        $cm = new ClientManager(['options' => [
            'fetch' => \Webklex\PHPIMAP\IMAP::FT_PEEK,
            'sequence' => \Webklex\PHPIMAP\IMAP::ST_UID,
        ]]);

        $client = $cm->make([
            'host'          => 'outlook.office365.com',
            'port'          => 993,
            'encryption'    => 'ssl',
            'validate_cert' => true,
            'protocol'      => 'imap',
            'authentication' => 'oauth',
            'username'      => 'duypm@aiot-global.com',
            'password'      => $token['access_token'],
        ]);

        try {
            $this->info('Connecting directly to outlook.office365.com:993 via XOAUTH2...');
            $client->connect();
            $this->info('✅ Successfully authenticated via XOAUTH2!');
            $folder = $client->getFolder('INBOX');
            $messages = $folder->messages()->limit(5)->get();
            $this->info("Fetched {$messages->count()} messages from INBOX.");
        } catch (AuthFailedException $e) {
            $this->error("❌ IMAP XOAUTH2 failed: " . $e->getMessage());
        } catch (\Exception $e) {
            $this->error("Error occurred: " . $e->getMessage());
        }

        $this->info('--- Test completed ---');
    }
}
