<?php

namespace Webkul\Email\Helpers;

class Attachment
{
    /**
     * @var File Content
     */
    private $content;

    /**
     * @var Filename extension
     */
    private $extension;

    /**
     * Create an helper instance
     */
    public function __construct(
        public $filename,
        public $contentType,
        public $stream,
        public $contentDisposition = 'attachment',
        public $contentId = '',
        public $headers = []
    ) {
        $this->content = null;
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
     * @param  int  $bytes
     * @return string
     */
    public function read($bytes = 2082)
    {
        return feof($this->stream) ? false : fread($this->stream, $bytes);
    }

    /**
     * Retrieve the file content in one go
     * Once you retrieve the content you cannot use MimeMailParser_attachment::read()
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
