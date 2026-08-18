<?php

namespace Webkul\Email\Repositories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webklex\PHPIMAP\Attachment as ImapAttachment;
use Webkul\Core\Eloquent\Repository;
use Webkul\Email\Contracts\Attachment;
use Webkul\Email\Contracts\Email;

class AttachmentRepository extends Repository
{
    /**
     * Disk used to store email attachments.
     *
     * Attachments are stored on the private disk (outside the web root) so an uploaded file can never
     * be requested or executed directly. They are served only through the authenticated download
     * route, regardless of their type.
     */
    public const DISK = 'local';

    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return Attachment::class;
    }

    /**
     * Resolve the disk an attachment is stored on.
     *
     * New attachments live on the private disk; this falls back to the default disk so attachments
     * created before this change (stored on the public disk) can still be read.
     */
    public static function resolveDisk(string $path): string
    {
        return Storage::disk(self::DISK)->exists($path)
            ? self::DISK
            : config('filesystems.default');
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
     * Prepare the attributes for an attachment.
     *
     * The file is written to the private disk under a random name so it can never be requested or
     * executed from a web accessible path, and its content type is detected from the file contents
     * rather than trusting the client supplied header. Only the original file name is retained, purely
     * as metadata used for display and for the download response.
     */
    private function prepareData(Email $email, UploadedFile|ImapAttachment $attachment): array
    {
        if ($attachment instanceof UploadedFile) {
            $name = $attachment->getClientOriginalName();

            $content = file_get_contents($attachment->getRealPath());
        } else {
            $name = $attachment->name;

            $content = $attachment->content;
        }

        $path = 'emails/'.$email->id.'/'.Str::random(40);

        Storage::disk(self::DISK)->put($path, $content);

        return [
            'path' => $path,
            'name' => basename(str_replace('\\', '/', (string) $name)),
            'content_type' => Storage::disk(self::DISK)->mimeType($path) ?: 'application/octet-stream',
            'size' => Storage::disk(self::DISK)->size($path),
            'email_id' => $email->id,
        ];
    }
}
