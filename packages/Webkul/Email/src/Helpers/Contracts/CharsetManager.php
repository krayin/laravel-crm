<?php

namespace Webkul\Email\Helpers\Contracts;

interface CharsetManager
{
    /**
     * Decode the string from Charset
     *
     * @param  $encodedString  The string in its original encoded state
     * @param  $charset  The Charset header of the part.
     * @return string the decoded string
     */
    public function decodeCharset($encodedString, $charset);

    /**
     * Get charset alias
     *
     * @return string the charset alias
     */
    public function getCharsetAlias($charset);
}
