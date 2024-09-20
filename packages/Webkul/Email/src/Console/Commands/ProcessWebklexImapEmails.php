<?php

namespace Webkul\Email\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use Webkul\Email\InboundEmailProcessor\WebklexImapEmailProcessor;

class ProcessWebklexImapEmails extends Command
{
    /**
     * Folder name.
     */
    protected const FOLDER_NAME = 'INBOX';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webklex-imap-emails:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will process the incoming emails from the mail server.';

    /**
     * Handle.
     *
     * @return void
     */
    public function handle()
    {
        if (config('mail-receiver.default') !== 'webklex-imap') {
            $this->error('The mail receiver driver is not set to webklex-imap.');

            return;
        }

        try {
            $this->info('Processing the incoming emails.');

            $client = Client::account('default');

            $client->connect();

            if (! $client->isConnected()) {
                throw new \Exception('Failed to connect to the mail server.');
            }

            $folder = $client->getFolder(self::FOLDER_NAME);

            $messages = $folder->query()->since(now()->subDays(10))->get();

            foreach ($messages as $message) {
                $processor = app(WebklexImapEmailProcessor::class);

                $processor->process($message);
            }

            $client->disconnect();

            $this->info('Incoming emails processed successfully.');
        } catch (\Exception $e) {
            $client->disconnect();

            $this->error($e->getMessage());
        }
    }
}
