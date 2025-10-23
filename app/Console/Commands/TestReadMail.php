<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestReadMail extends Command
{
    protected $signature = 'test:read-mail';
    protected $description = 'Read emails from Gmail using IMAP';

    public function handle()
    {
        $host = env('IMAP_HOST');
        $port = env('IMAP_PORT');
        $protocol = 'imap';
        $encryption = env('IMAP_ENCRYPTION');
        $username = env('IMAP_USERNAME');
        $password = env('IMAP_PASSWORD');

        $mailbox = "{{$host}:{$port}/{$protocol}/{$encryption}}INBOX";

        $this->info("Connecting to $mailbox ...");

        $inbox = @imap_open($mailbox, $username, $password);

        if (!$inbox) {
            $this->error('Cannot connect: ' . imap_last_error());
            return;
        }

        $emails = imap_search($inbox, 'ALL');

        if (!$emails) {
            $this->info('No emails found.');
            return;
        }

        rsort($emails);

        $this->info("Found " . count($emails) . " emails");
        foreach (array_slice($emails, 0, 5) as $email_number) {
            $overview = imap_fetch_overview($inbox, $email_number, 0)[0];
            $this->line("From: {$overview->from}");
            $this->line("Subject: {$overview->subject}");
            $this->line(str_repeat('-', 40));
        }

        imap_close($inbox);
    }
}
