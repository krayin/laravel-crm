<?php

namespace Webkul\Email\Repositories;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
    private function prepareData(Email $email, UploadedFile $attachment): array
    {
        $path = 'emails/'.$email->id.'/'.$attachment->getClientOriginalName();

        Storage::put($path, $attachment->getContent());

        $attributes = [
            'path'         => $path,
            'name'         => $attachment->getClientOriginalName(),
            'content_type' => $attachment->getMimeType(),
            'size'         => Storage::size($path),
            'email_id'     => $email->id,
        ];

        return $attributes;
    }
}
