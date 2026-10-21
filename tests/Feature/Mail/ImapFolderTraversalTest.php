<?php

use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Client as ImapClient;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Query\WhereQuery;
use Webklex\PHPIMAP\Support\FolderCollection;
use Webklex\PHPIMAP\Support\MessageCollection;
use Webkul\Email\Helpers\HtmlFilter;
use Webkul\Email\InboundEmailProcessor\WebklexImapEmailProcessor;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Email\Repositories\EmailRepository;

/**
 * Many servers (Dovecot, cPanel) list `INBOX` as a parent of the other folders, such as
 * `INBOX.Sent` and `INBOX.Trash`. A folder with subfolders still holds its own messages, so the
 * poller reads it as well as its subfolders, and only skips containers that cannot be opened.
 */
function makeImapFolder(string $name, array $messages = [], array $children = [], bool $noSelect = false): Folder
{
    $folder = Mockery::mock(Folder::class);

    $folder->name = $name;
    $folder->no_select = $noSelect;
    $folder->children = new FolderCollection($children);

    $query = Mockery::mock(WhereQuery::class);
    $query->shouldReceive('since')->andReturnSelf();
    $query->shouldReceive('get')->andReturn(new MessageCollection($messages));

    $folder->shouldReceive('query')->times($noSelect ? 0 : 1)->andReturn($query);

    return $folder;
}

function makeImapProcessor(FolderCollection $folders): WebklexImapEmailProcessor
{
    $client = Mockery::mock(ImapClient::class);
    $client->shouldReceive('connect');
    $client->shouldReceive('isConnected')->andReturnTrue();
    $client->shouldReceive('disconnect');
    $client->shouldReceive('getFolders')->andReturn($folders);

    Client::shouldReceive('make')->andReturn($client);

    return Mockery::mock(WebklexImapEmailProcessor::class, [
        app(EmailRepository::class),
        app(AttachmentRepository::class),
        app(HtmlFilter::class),
    ])->makePartial();
}

it('imports the messages of a folder that has subfolders', function () {
    $inboxMessage = Mockery::mock(Message::class);
    $sentMessage = Mockery::mock(Message::class);

    $processor = makeImapProcessor(new FolderCollection([
        makeImapFolder('INBOX', [$inboxMessage], [
            makeImapFolder('Sent', [$sentMessage]),
        ]),
    ]));

    $processor->shouldReceive('processMessage')->with($inboxMessage)->once();
    $processor->shouldReceive('processMessage')->with($sentMessage)->once();

    $processor->processMessagesFromAllFolders();
});

it('does not open a folder that cannot hold messages', function () {
    $sentMessage = Mockery::mock(Message::class);

    $processor = makeImapProcessor(new FolderCollection([
        makeImapFolder('[Gmail]', [], [
            makeImapFolder('Sent Mail', [$sentMessage]),
        ], noSelect: true),
    ]));

    $processor->shouldReceive('processMessage')->with($sentMessage)->once();

    $processor->processMessagesFromAllFolders();
});
