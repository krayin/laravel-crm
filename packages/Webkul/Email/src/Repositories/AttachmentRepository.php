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

        $path = 'emails/'.$email->id.'/'.$name;

        Storage::put($path, $content);

        $attributes = [
            'path'         => $path,
            'name'         => $name,
            'content_type' => $mimeType,
            'size'         => Storage::size($path),
            'email_id'     => $email->id,
        ];

        return $attributes;
    }
}
