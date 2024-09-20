<?php

namespace Webkul\Email\InboundEmailProcessor\Contracts;

interface InboundEmailProcessor
{
    /**
     * Get the messages from the mail server.
     *
     * @return mixed
     */
    public function getMessages();

    /**
     * Process the inbound email.
     *
     * @param  mixed|null  $content
     */
    public function process($content = null): void;
}
