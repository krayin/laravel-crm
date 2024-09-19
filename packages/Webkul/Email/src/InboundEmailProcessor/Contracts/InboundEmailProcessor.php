<?php

namespace Webkul\Email\InboundEmailProcessor\Contracts;

interface InboundEmailProcessor
{
    /**
     * Process the inbound email.
     *
     * @param  mixed|null  $content
     */
    public function process($content = null): void;
}
