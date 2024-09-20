<?php

namespace Webkul\Email\Repositories;

use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\Repository;
use Webkul\Email\Contracts\Attachment;

class AttachmentRepository extends Repository
{
    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Attachment::class;
    }

    /**
     * Upload attachments.
     *
     * @param  \Webkul\Email\Contracts\Email  $email
     * @return void
     */
    public function uploadAttachments($email, array $data)
    {
        if (! isset($data['source'])) {
            return;
        }

        if ($data['source'] == 'email') {
            foreach ($data['attachments'] as $attachment) {
                Storage::put($path = 'emails/'.$email->id.'/'.$attachment->getFilename(), $attachment->getContent());

                $this->create([
                    'path'         => $path,
                    'name'         => $attachment->getFileName(),
                    'content_type' => $attachment->contentType,
                    'content_id'   => $attachment->contentId,
                    'size'         => Storage::size($path),
                    'email_id'     => $email->id,
                ]);
            }
        } else {
            if (! isset($data['attachments'])) {
                return;
            }

            foreach ($data['attachments'] as $index => $attachment) {
                $this->create([
                    'path'         => $path = request()->file('attachments.'.$index)->store('emails/'.$email->id),
                    'name'         => $attachment->getClientOriginalName(),
                    'content_type' => $attachment->getClientMimeType(),
                    'size'         => Storage::size($path),
                    'email_id'     => $email->id,
                ]);
            }
        }
    }
}
