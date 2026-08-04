<?php

namespace Webkul\Email\Repositories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Webklex\PHPIMAP\Attachment as ImapAttachment;
use Webkul\Core\Eloquent\Repository;
use Webkul\Email\Contracts\Attachment;
use Webkul\Email\Contracts\Email;

class AttachmentRepository extends Repository
{
    /**
     * File extensions that the web server or the browser may execute when a file is served from the
     * public disk (`APP_URL/storage`). Attachments whose name contains one of these are stored under
     * a neutralised name so the uploaded file can never be executed.
     */
    public const DANGEROUS_EXTENSIONS = [
        'php', 'php2', 'php3', 'php4', 'php5', 'php6', 'php7', 'php8', 'phps', 'pht', 'phtm', 'phtml',
        'phar', 'phpt', 'cgi', 'pl', 'py', 'sh', 'bash', 'exe', 'com', 'bat', 'cmd', 'asp', 'aspx',
        'jsp', 'jspx', 'jar', 'war', 'htaccess', 'htpasswd', 'shtml', 'html', 'htm', 'xhtml', 'xht', 'svg',
    ];

    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return Attachment::class;
    }

    /**
     * Upload attachments.
     */
    public function uploadAttachments(Email $email, array $data): void
    {
        if (
            empty($data['attachments'])
            || empty($data['source'])
        ) {
            return;
        }

        foreach ($data['attachments'] as $attachment) {
            $attributes = $this->prepareData($email, $attachment);

            if (
                ! empty($attachment->contentId)
                && $data['source'] === 'email'
            ) {
                $attributes['content_id'] = $attachment->contentId;
            }

            $this->create($attributes);
        }
    }

    /**
     * Get the path for the attachment.
     */
    private function prepareData(Email $email, UploadedFile|ImapAttachment $attachment): array
    {
        if ($attachment instanceof UploadedFile) {
            $name = $attachment->getClientOriginalName();

            $content = file_get_contents($attachment->getRealPath());

            $mimeType = $attachment->getMimeType();
        } else {
            $name = $attachment->name;

            $content = $attachment->content;

            $mimeType = $attachment->mime;
        }

        $name = $this->sanitizeName($name);

        $path = 'emails/'.$email->id.'/'.$name;

        Storage::put($path, $content);

        $attributes = [
            'path' => $path,
            'name' => $name,
            'content_type' => $mimeType,
            'size' => Storage::size($path),
            'email_id' => $email->id,
        ];

        return $attributes;
    }

    /**
     * Neutralise an attachment file name before it is written to the public disk.
     *
     * Directory components (including traversal sequences) are stripped, and any name that carries a
     * dangerous extension has its dots collapsed and a `.txt` suffix appended, so the stored file can
     * never be served as executable code from `APP_URL/storage`.
     */
    private function sanitizeName(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));

        foreach (explode('.', $name) as $segment) {
            if (in_array(strtolower($segment), self::DANGEROUS_EXTENSIONS, true)) {
                return str_replace('.', '_', $name).'.txt';
            }
        }

        return $name;
    }
}
