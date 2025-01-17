<?php

namespace Webkul\Email\InboundEmailProcessor\Contracts;

interface InboundEmailProcessor
{
    /**
     * Process messages from all folders.
     *
     * @return mixed
     */
    public function processMessagesFromAllFolders();

    /**
     * Process the inbound email.
     *
     * @param  mixed|null  $content
     */
    public function processMessage($content = null): void;
}
