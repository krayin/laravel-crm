<?php

namespace Webkul\Email\Helpers;

class Attachment
{
    /**
    * @var $filename Filename
    */
    public $filename;

    /**
    * @var $contentType Mime Type
    */
    public $contentType;

    /**
    * @var $content File Content
    */
    private $content;

    /**
    * @var $extension Filename extension
    */
    private $extension;

    /**
    * @var $contentDisposition Content-Disposition (attachment or inline)
    */
    public $contentDisposition;

    /**
    * @var $contentId Content-ID
    */
    public $contentId;

    /**
    * @var $headers An Array of the attachment headers
    */
    public $headers;

    private $stream;

    public function __construct(
        $filename,
        $contentType,
        $stream,
        $contentDisposition = 'attachment',
        $contentId = '',
        $headers = []
    ) {
        $this->filename = $filename;

        $this->contentType = $contentType;

        $this->stream = $stream;

        $this->content = null;

        $this->contentDisposition = $contentDisposition;

        $this->contentId = $contentId;

        $this->headers = $headers;
    }

    /**
     * retrieve the attachment filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Retrieve the Attachment Content-Type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Retrieve the Attachment Content-Disposition
     *
     * @return string
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * Retrieve the Attachment Content-ID
     *
     * @return string
     */
    public function getContentID()
    {
        return $this->contentId;
    }

    /**
    * Retrieve the Attachment Headers
    *
    * @return string
    */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Read the contents a few bytes at a time until completed
     * Once read to completion, it always returns false
     *
     * @param  integer  $bytes
     * @return string
     */
    public function read($bytes = 2082)
    {
        return feof($this->stream) ? false : fread($this->stream, $bytes);
    }

    /**
     * Retrieve the file content in one go
     * Once you retreive the content you cannot use MimeMailParser_attachment::read()
     *
     * @return string
     */
    public function getContent()
    {
        if ($this->content === null) {
            fseek($this->stream, 0);

            while (($buf = $this->read()) !== false) {
                $this->content .= $buf;
            }
        }

        return $this->content;
    }
}
