<?php 
namespace Webkul\Email\Helpers\Contracts;

interface CharsetManager
{
    /**
     * Decode the string from Charset
     * @return String the decoded string
     * @param $encodedString    The string in its original encoded state
     * @param $charset          The Charset header of the part.
     */
    public function decodeCharset($encodedString, $charset);

    /**
     * Get charset alias
     * @return string the charset alias
     * @param $charset.
     */
    public function getCharsetAlias($charset);
}
