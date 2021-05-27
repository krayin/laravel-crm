<?php

namespace Webkul\Email\Helpers;

use Webkul\Email\Helpers\Contracts\CharsetManager;

/**
 * Parser of php-mime-mail-parser
 *
 * Fully Tested Mailparse Extension Wrapper for PHP 5.4+
 *
 */
class Parser
{
    /**
     * PHP MimeParser Resource ID
     */
    public $resource;

    /**
     * A file pointer to email
     */
    public $stream;

    /**
     * A text of an email
     */
    public $data;

    /**
     * A text of an email
     */
    public $container;

    public $entity;

    public $files;

    /**
     * Parts of an email
     */
    public $parts;

    /**
     * Charset managemer object
     */
    public $charset;

    public function __construct(CharsetManager $charset = null)
    {
        if (is_null($charset)) {
            $charset = new Charset();
        }

        $this->charset = $charset;
    }

    /**
     * Free the held resouces
     * @return void
     */
    public function __destruct()
    {
        // clear the email file resource
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }

        // clear the MailParse resource
        if (is_resource($this->resource)) {
            mailparse_msg_free($this->resource);
        }
    }

    /**
     * Set the file path we use to get the email text
     * @return Object MimeMailParser Instance
     * @param string $path File path to the MIME mail
     */
    public function setPath($path)
    {
        // should parse message incrementally from file
        $this->resource = mailparse_msg_parse_file($path);

        $this->stream = fopen($path, 'r');

        $this->parse();

        return $this;
    }

    /**
     * Set the Stream resource we use to get the email text
     * @return Object MimeMailParser Instance
     * @param $stream Resource
     */
    public function setStream($stream)
    {
        // streams have to be cached to file first
        $meta = @stream_get_meta_data($stream);

        if (! $meta || !$meta['mode'] || $meta['mode'][0] != 'r' || $meta['eof']) {
            throw new \Exception(
                'setStream() expects parameter stream to be readable stream resource.'
            );
        }

        $tmp_fp = tmpfile();

        if ($tmp_fp) {
            while (! feof($stream)) {
                fwrite($tmp_fp, fread($stream, 2028));
            }

            fseek($tmp_fp, 0);

            $this->stream =& $tmp_fp;
        } else {
            throw new \Exception(
                'Could not create temporary files for attachments. Your tmp directory may be unwritable by PHP.'
            );
        }

        fclose($stream);

        $this->resource = mailparse_msg_create();

        // parses the message incrementally (low memory usage but slower)
        while (! feof($this->stream)) {
            mailparse_msg_parse($this->resource, fread($this->stream, 2082));
        }

        $this->parse();

        return $this;
    }

    /**
     * Set the email text
     * @return Object MimeMailParser Instance
     * @param $data String
     */
    public function setText($data)
    {
        $this->resource = \mailparse_msg_create();

        // does not parse incrementally, fast memory hog might explode
        mailparse_msg_parse($this->resource, $data);

        $this->data = $data;

        $this->parse();

        return $this;
    }

    /**
     * Parse the Message into parts
     * @return void
     * @private
     */
    private function parse()
    {
        $structure = mailparse_msg_get_structure($this->resource);

        $headerType = (stripos($this->data,'Content-Language:') !== false) ? 'Content-Language:' : 'Content-Type:';

        if (count($structure) == 1) {
            $tempParts = explode(PHP_EOL,$this->data);

            foreach ($tempParts as $key => $part) {
                if (stripos($part,$headerType) !== false) {
                    break;
                }

                if (trim($part) == '') {
                    unset($tempParts[$key]);
                }
            }

            $data = implode(PHP_EOL,$tempParts);

            $this->resource = \mailparse_msg_create();

            mailparse_msg_parse($this->resource, $data);

            $this->data = $data;

            $structure = mailparse_msg_get_structure($this->resource);
        }

        $this->parts = array();

        foreach ($structure as $part_id) {
            $part = mailparse_msg_get_part($this->resource, $part_id);

            $this->parts[$part_id] = mailparse_msg_get_part_data($part);
        }
    }

    /**
     * Retrieve a specific Email Header, without charset conversion.
     * @return String
     * @param $name String Header name
     */
    public function getRawHeader($name)
    {
        if (isset($this->parts[1])) {
            $headers = $this->getPart('headers', $this->parts[1]);

            return (isset($headers[$name])) ? $headers[$name] : false;
        } else {
            throw new \Exception(
                'setPath() or setText() or setStream() must be called before retrieving email headers.'
            );
        }
    }

    /**
     * Retrieve a specific Email Header
     * @return String
     * @param $name String Header name
     */
    public function getHeader($name)
    {
        $rawHeader = $this->getRawHeader($name);

        if ($rawHeader === false) {
            return false;
        }

        return $this->decodeHeader($rawHeader);
    }

    /**
     * Retrieve all mail headers
     * @return Array
     */
    public function getHeaders()
    {
        if (isset($this->parts[1])) {
            $headers = $this->getPart('headers', $this->parts[1]);

            foreach ($headers as $name => &$value) {
                if (is_array($value)) {
                    foreach ($value as &$v) {
                        $v = $this->decodeSingleHeader($v);
                    }
                } else {
                    $value = $this->decodeSingleHeader($value);
                }
            }

            return $headers;
        } else {
            throw new \Exception(
                'setPath() or setText() or setStream() must be called before retrieving email headers.'
            );
        }
    }

    public function getFromName()
    {
        $headers = $this->getHeaders();

        return $headers['from'];
    }

    public function extractMultipartMIMEText($part, $source, $encodingType)
    {
        $boundary = trim($part['content-boundary']);
        $boundary = substr($boundary, strpos($boundary, '----=') + strlen('----='));

        preg_match_all('/------=(3D_.*)\sContent-Type:\s(.*)\s*boundary=3D"----=(3D_.*)"/', $source, $matches);

        $delimeter = array_shift($matches);
        $content_delimeter = end($delimeter);

        list($relations, $content_types, $boundaries) = $matches;

        $messageToProcess = substr($source, stripos($source, (string)$content_delimeter) + strlen($content_delimeter));

        array_unshift($boundaries, $boundary);

        // Extract the text
        foreach (array_reverse($boundaries) as $index => $boundary) {
            $processedEmailSegments = [];
            $emailSegments = explode('------=' . $boundary, $messageToProcess);

            // Remove empty parts
            foreach ($emailSegments as $emailSegment) {
                if (!empty(trim($emailSegment))) {
                    $processedEmailSegments[] = trim($emailSegment);
                }
            }

            // Remove unrelated parts
            array_pop($processedEmailSegments);

            for ($i = 0; $i < $index; $i++) {
                if (count($processedEmailSegments) > 1) {
                    array_shift($processedEmailSegments);
                }
            }

            // Parse each parts for text|html content
            foreach ($processedEmailSegments as $emailSegment) {
                $emailSegment = quoted_printable_decode(quoted_printable_decode($emailSegment));

                if (stripos($emailSegment, 'content-type: text/plain;') !== false
                    || stripos($emailSegment, 'content-type: text/html;') !== false
                ) {
                    $search = 'content-transfer-encoding: ' . $encodingType;

                    return substr($emailSegment, stripos($emailSegment, $search) + strlen($search));
                }
            }
        }

        return '';
    }

    /**
     * Returns the email message body in the specified format
     * @return Mixed String Body or False if not found
     * @param $type String text or html or htmlEmbedded
     */
    public function getMessageBody($type = 'text')
    {
        $textBody = $htmlBody = $body = false;

        $mime_types = [
            'text'=> 'text/plain',
            'text'=> 'text/plain; (error)',
            'html'=> 'text/html',
        ];

        if (in_array($type, array_keys($mime_types))) {
            foreach ($this->parts as $key => $part) {
                if (in_array($this->getPart('content-type', $part), $mime_types)
                    && $this->getPart('content-disposition', $part) != 'attachment'
                ) {
                    $headers = $this->getPart('headers', $part);
                    $encodingType = array_key_exists('content-transfer-encoding', $headers) ? $headers['content-transfer-encoding'] : '';

                    if ($this->getPart('content-type', $part) == 'text/plain') {
                        $textBody .= $this->decodeContentTransfer($this->getPartBody($part), $encodingType);
                        $textBody = nl2br($this->charset->decodeCharset($textBody, $this->getPartCharset($part)));
                    } elseif ($this->getPart('content-type', $part) == 'text/plain; (error)') {
                        $part_from_sender = is_array($part['headers']['from']) ? $part['headers']['from'][0] : $part['headers']['from'];
                        $mail_part_addresses = mailparse_rfc822_parse_addresses($part_from_sender);

                        if (! empty($mail_part_addresses[0]['address'])
                            && false !== strrpos($mail_part_addresses[0]['address'], 'pcsms.com')
                        ) {
                            $last_header = end($headers);
                            $partMessage = substr($this->data, strrpos($this->data, $last_header) + strlen($last_header), $part['ending-pos-body']);
                            $textBody .= $this->decodeContentTransfer($partMessage, $encodingType);
                            $textBody = nl2br($this->charset->decodeCharset($textBody, $this->getPartCharset($part)));
                        }
                    } elseif ($this->getPart('content-type', $part) == 'multipart/mixed'
                        || $this->getPart('content-type', $part) == 'multipart/related'
                    ) {
                        $emailContent = $this->extractMultipartMIMEText($part, $this->data, $encodingType);

                        $textBody .= $this->decodeContentTransfer($emailContent, $encodingType);
                        $textBody = nl2br($this->charset->decodeCharset($textBody, $this->getPartCharset($part)));
                    } else {
                        $htmlBody .= $this->decodeContentTransfer($this->getPartBody($part), $encodingType);
                        $htmlBody = $this->charset->decodeCharset($htmlBody, $this->getPartCharset($part));
                    }
                }
            }

            $body = $htmlBody ?: $textBody;

            if (is_array($this->files)) {
                foreach ($this->files as $file) {
                    if ($file['contentId']) {
                        $body = str_replace("cid:".preg_replace('/[<>]/','',$file['contentId']) , $file['path'], $body);
                        $path = $file['path'];
                    }
                }
            }
        } else {
            throw new \Exception('Invalid type specified for getMessageBody(). "type" can either be text or html.');
        }

        return $body;
    }

    public function getTextMessageBody()
    {
        $textBody = null;

        foreach ($this->parts as $key => $part) {
            if ($this->getPart('content-disposition', $part) != 'attachment') {
                $headers = $this->getPart('headers', $part);
                $encodingType = array_key_exists('content-transfer-encoding', $headers) ? $headers['content-transfer-encoding'] : '';

                if ($this->getPart('content-type', $part) == 'text/plain') {
                    $textBody .= $this->decodeContentTransfer($this->getPartBody($part), $encodingType);
                    $textBody = nl2br($this->charset->decodeCharset($textBody, $this->getPartCharset($part)));
                } elseif ($this->getPart('content-type', $part) == 'text/plain; (error)') {
                    $part_from_sender = is_array($part['headers']['from']) ? $part['headers']['from'][0] : $part['headers']['from'];
                    $mail_part_addresses = mailparse_rfc822_parse_addresses($part_from_sender);

                    if (! empty($mail_part_addresses[0]['address'])
                        && false !== strrpos($mail_part_addresses[0]['address'], 'pcsms.com')
                    ) {
                        $last_header = end($headers);
                        $partMessage = substr($this->data, strrpos($this->data, $last_header) + strlen($last_header), $part['ending-pos-body']);
                        $textBody .= $this->decodeContentTransfer($partMessage, $encodingType);
                        $textBody = nl2br($this->charset->decodeCharset($textBody, $this->getPartCharset($part)));
                    }
                } elseif ($this->getPart('content-type', $part) == 'multipart/mixed'
                    || $this->getPart('content-type', $part) == 'multipart/related'
                ) {
                    $emailContent = $this->extractMultipartMIMEText($part, $this->data, $encodingType);

                    $textBody .= $this->decodeContentTransfer($emailContent, $encodingType);
                    $textBody = nl2br($this->charset->decodeCharset($textBody, $this->getPartCharset($part)));
                }
            }
        }

        return $textBody;
    }

    /**
     * Returns the embedded data structure
     * @return String
     * @param $contentId String of Content-Id
     */
    private function getEmbeddedData($contentId)
    {
        $embeddedData = 'data:';

        foreach ($this->parts as $part) {
            if ($this->getPart('content-id', $part) == $contentId) {
                $embeddedData .= $this->getPart('content-type', $part);
                $embeddedData .= ';'.$this->getPart('transfer-encoding', $part);
                $embeddedData .= ','.$this->getPartBody($part);
            }
        }

        return $embeddedData;
    }

    /**
     * Returns the attachments contents in order of appearance
     * @return Array of attachments
     */
    public function getAttachments()
    {
        $attachments = [];
        $dispositions = ['attachment', 'inline'];
        $non_attachment_types = ['text/plain', 'text/html', 'text/plain; (error)'];
        $nonameIter = 0;

        foreach ($this->parts as $part) {
            $disposition = $this->getPart('content-disposition', $part);
            $filename = 'noname';

            if (isset($part['disposition-filename'])) {
                $filename = $this->decodeHeader($part['disposition-filename']);
            } elseif (isset($part['content-name'])) {
                // if we have no disposition but we have a content-name, it's a valid attachment.
                // we simulate the presence of an attachment disposition with a disposition filename
                $filename = $this->decodeHeader($part['content-name']);
                $disposition = 'attachment';
            } elseif (! in_array($part['content-type'], $non_attachment_types, true)
                && substr($part['content-type'], 0, 10) !== 'multipart/'
            ) {
                // if we cannot get it by getMessageBody(), we assume it is an attachment
                $disposition = 'attachment';
            }

            if (in_array($disposition, $dispositions) === true && isset($filename) === true) {
                if ($filename == 'noname') {
                    $nonameIter++;
                    $filename = 'noname' . $nonameIter;
                }

                $headersAttachments = $this->getPart('headers', $part);
                $contentidAttachments = $this->getPart('content-id', $part);

                if (! $contentidAttachments
                    && $disposition == "inline"
                    && ! strpos($this->getPart('content-type', $part), 'image/')
                    && ! stripos($filename,"noname") == false
                ) {
                    //skip
                } else {
                    $attachments[] = new Attachment(
                        $filename,
                        $this->getPart('content-type', $part),
                        $this->getAttachmentStream($part),
                        $disposition,
                        $contentidAttachments,
                        $headersAttachments
                    );
                }
            }
        }

        return ! empty($attachments) ? $attachments : $this->extractMultipartMIMEAttachments();
    }

    public function extractMultipartMIMEAttachments()
    {
        $attachmentCollection = $processedAttachmentCollection = [];

        foreach ($this->parts as $part) {
            $boundary = isset($part['content-boundary']) ? trim($part['content-boundary']) : '';
            $boundary = substr($boundary, strpos($boundary, '----=') + strlen('----='));

            preg_match_all('/------=(3D_.*)\sContent-Type:\s(.*)\s*boundary=3D"----=(3D_.*)"/', $this->data, $matches);

            $delimeter = array_shift($matches);
            $content_delimeter = end($delimeter);

            list($relations, $content_types, $boundaries) = $matches;
            $messageToProcess = substr($this->data, stripos($this->data, (string)$content_delimeter) + strlen($content_delimeter));

            array_unshift($boundaries, $boundary);

            // Extract the text
            foreach (array_reverse($boundaries) as $index => $boundary) {
                $processedEmailSegments = [];
                $emailSegments = explode('------=' . $boundary, $messageToProcess);

                // Remove empty parts
                foreach ($emailSegments as $emailSegment) {
                    if (! empty(trim($emailSegment))) {
                        $processedEmailSegments[] = trim($emailSegment);
                    }
                }

                // Remove unrelated parts
                array_pop($processedEmailSegments);

                for ($i = 0; $i < $index; $i++) {
                    if (count($processedEmailSegments) > 1) {
                        array_shift($processedEmailSegments);
                    }
                }

                // Parse each parts for text|html content
                foreach ($processedEmailSegments as $emailSegment) {
                    $emailSegment = quoted_printable_decode(quoted_printable_decode($emailSegment));

                    if (stripos($emailSegment, 'content-type: text/plain;') === false
                        && stripos($emailSegment, 'content-type: text/html;') === false
                    ) {
                        $attachmentParts = explode("\n\n", $emailSegment);

                        if (! empty($attachmentParts) && count($attachmentParts) == 2) {
                            $attachmentDetails = explode("\n", $attachmentParts[0]);

                            $attachmentDetails = array_map(function($item) {
                                return trim($item);
                            }, $attachmentDetails);

                            $attachmentData = trim($attachmentParts[1]);

                            $attachmentCollection[] = [
                                'details' => $attachmentDetails,
                                'data' => $attachmentData
                            ];
                        }
                    }
                }
            }
        }

        foreach ($attachmentCollection as $attachmentDetails) {
            $stream = '';

            $resourceDetails = [
                'name' => '',
                'fileName' => '',
                'contentType' => '',
                'encodingType' => 'base64',
                'contentDisposition' => 'inline',
                'contentId' => '',
            ];

            foreach ($attachmentDetails['details'] as $attachmentDetail) {
                if (0 === stripos($attachmentDetail, "Content-Type: ")) {
                    $resourceDetails['contentType'] = substr($attachmentDetail, strlen("Content-Type: "));
                } else if (0 === stripos($attachmentDetail, 'name="')) {
                    $resourceDetails['name'] = substr($attachmentDetail, strlen('name="'), -1);
                } else if (0 === stripos($attachmentDetail, "Content-Transfer-Encoding: ")) {
                    $resourceDetails['encodingType'] = substr($attachmentDetail, strlen("Content-Transfer-Encoding: "));
                } else if (0 === stripos($attachmentDetail, 'Content-ID: ')) {
                    $resourceDetails['contentId'] = substr($attachmentDetail, strlen('Content-ID: '));
                } else if (0 === stripos($attachmentDetail, 'filename="')) {
                    $resourceDetails['fileName'] = substr($attachmentDetail, strlen('filename="'), -1);
                } else if (0 === stripos($attachmentDetail, 'Content-Disposition: ')) {
                    $resourceDetails['contentDisposition'] = substr($attachmentDetail, strlen('Content-Disposition: '), -1);
                }
            }

            $resourceDetails['name'] = empty($resourceDetails['name']) ? $resourceDetails['fileName'] : $resourceDetails['name'];
            $resourceDetails['fileName'] = empty($resourceDetails['fileName']) ? $resourceDetails['name'] : $resourceDetails['fileName'];

            $temp_fp = tmpfile();

            fwrite($temp_fp, base64_decode($attachmentDetails['data']), strlen($attachmentDetails['data']));
            fseek($temp_fp, 0, SEEK_SET);

            $processedAttachmentCollection[] = new Attachment(
                $resourceDetails['fileName'],
                $resourceDetails['contentType'],
                $temp_fp,
                $resourceDetails['contentDisposition'],
                $resourceDetails['contentId']
                , []
            );
        }

        return $processedAttachmentCollection;
    }

    /**
     * Read the attachment Body and save temporary file resource
     * @return String Mime Body Part
     * @param $part Array
     */
    private function getAttachmentStream(&$part)
    {
        $temp_fp = tmpfile();

        $headers = $this->getPart('headers', $part);
        $encodingType = array_key_exists('content-transfer-encoding', $headers)
            ? $headers['content-transfer-encoding']
            : '';

        if ($temp_fp) {
            if ($this->stream) {
                $start = $part['starting-pos-body'];
                $end = $part['ending-pos-body'];
                
                fseek($this->stream, $start, SEEK_SET);

                $len = $end-$start;
                $written = 0;

                while ($written < $len) {
                    $write = $len;
                    $part = fread($this->stream, $write);

                    fwrite($temp_fp, $this->decodeContentTransfer($part, $encodingType));

                    $written += $write;
                }
            } elseif ($this->data) {
                $attachment = $this->decodeContentTransfer($this->getPartBodyFromText($part), $encodingType);

                fwrite($temp_fp, $attachment, strlen($attachment));
            }

            fseek($temp_fp, 0, SEEK_SET);
        } else {
            throw new \Exception(
                'Could not create temporary files for attachments. Your tmp directory may be unwritable by PHP.'
            );
        }
        
        return $temp_fp;
    }

    /**
     * Decode the string from Content-Transfer-Encoding
     * @return String the decoded string
     * @param $encodedString    The string in its original encoded state
     * @param $encodingType     The encoding type from the Content-Transfer-Encoding header of the part.
     */
    private function decodeContentTransfer($encodedString, $encodingType)
    {
        $encodingType = strtolower($encodingType);

        if ($encodingType == 'base64') {
            return base64_decode($encodedString);
        } elseif ($encodingType == 'quoted-printable') {
            return quoted_printable_decode($encodedString);
        } else {
            return $encodedString; //8bit, 7bit, binary
        }
    }

    /**
    * $input can be a string or array
    * @param string|array $input
    * @return string
    */
    private function decodeHeader($input)
    {
        //Sometimes we have 2 label From so we take only the first
        if (is_array($input)) {
            return $this->decodeSingleHeader($input[0]);
        }

        return $this->decodeSingleHeader($input);
    }

    /**
     * Decodes a single header (= string)
     * @param string
     * @return string
     */
    private function decodeSingleHeader($input)
    {
        // Remove white space between encoded-words
        $input = preg_replace('/(=\?[^?]+\?(q|b)\?[^?]*\?=)(\s)+=\?/i', '\1=?', $input);

        // For each encoded-word...
        while (preg_match('/(=\?([^?]+)\?(q|b)\?([^?]*)\?=)/i', $input, $matches)) {
            $encoded = $matches[1];
            $charset = $matches[2];
            $encoding = $matches[3];
            $text = $matches[4];


            switch (strtolower($encoding)) {
                case 'b':
                    $text = $this->decodeContentTransfer($text, 'base64');
                    break;

                case 'q':
                    $text = str_replace('_', ' ', $text);

                    preg_match_all('/=([a-f0-9]{2})/i', $text, $matches);

                    foreach ($matches[1] as $value) {
                        $text = str_replace('=' . $value, chr(hexdec($value)), $text);
                    }

                    break;
            }

            $text = $this->charset->decodeCharset($text, $this->charset->getCharsetAlias($charset));

            $input = str_replace($encoded, $text, $input);
        }

        return $input;
    }

    /**
     * Return the charset of the MIME part
     * @return String|false
     * @param $part Array
     */
    private function getPartCharset($part)
    {
        if (isset($part['charset'])) {
            return $charset = $this->charset->getCharsetAlias($part['charset']);
        } else {
            return false;
        }
    }

    /**
     * Retrieve a specified MIME part
     * @return String|Array
     * @param $type String, $parts Array
     */
    private function getPart($type, $parts)
    {
        return (isset($parts[$type])) ? $parts[$type] : false;
    }

    /**
     * Retrieve the Body of a MIME part
     * @return String
     * @param $part Object
     */
    private function getPartBody(&$part)
    {
        $body = '';

        if ($this->stream) {
            $body = $this->getPartBodyFromFile($part);
        } elseif ($this->data) {
            $body = $this->getPartBodyFromText($part);
        }

        return $body;
    }

    /**
     * Retrieve the Body from a MIME part from file
     * @return String Mime Body Part
     * @param $part Array
     */
    private function getPartBodyFromFile(&$part)
    {
        $start = $part['starting-pos-body'];
        $end = $part['ending-pos-body'];

        fseek($this->stream, $start, SEEK_SET);

        return fread($this->stream, $end - $start);
    }

    /**
     * Retrieve the Body from a MIME part from text
     * @return String Mime Body Part
     * @param $part Array
     */
    private function getPartBodyFromText(&$part)
    {
        $start = $part['starting-pos-body'];
        $end = $part['ending-pos-body'];
        
        return substr($this->data, $start, $end - $start);
    }
}
