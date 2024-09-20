<?php

namespace Webkul\Email\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;

class ProcessInboundEmails extends Command
{
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        protected InboundEmailProcessor $inboundEmailProcessor
    ) {
        parent::__construct();
    }

    /**
     * Handle.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Processing the incoming emails.');

        $messages = $this->inboundEmailProcessor->getMessages();

        foreach ($messages as $message) {
            $this->inboundEmailProcessor->process($message);
        }

        $this->info('Incoming emails processed successfully.');
    }
}
