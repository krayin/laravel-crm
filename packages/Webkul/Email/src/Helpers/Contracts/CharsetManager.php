<?php

namespace Webkul\Email\Helpers\Contracts;

interface CharsetManager
{
    /**
     * Decode the string from Charset.
     *
     * @return string
     */
    public function decodeCharset($encodedString, $charset);

    /**
     * Get charset alias.
     *
     * @return string
     */
    public function getCharsetAlias($charset);
}
