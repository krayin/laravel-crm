<?php

namespace Webkul\Email\Repositories;

use Illuminate\Support\Facades\Storage;
use Webkul\Core\Eloquent\Repository;

class AttachmentRepository extends Repository
{
    /**
     * Parser object
     *
     * @var \Webkul\Email\Helpers\Parser
     */
    protected $emailParser;

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Email\Contracts\Attachment';
    }

    /**
     * @param  \Webkul\Email\Helpers\Parser  $emailParser
     * @return self
     */
    public function setEmailParser($emailParser)
    {
        $this->emailParser = $emailParser;

        return $this;
    }

    /**
     * @param  \Webkul\Email\Contracts\Email  $email
     * @return void
     */
    public function uploadAttachments($email, array $data)
    {
        if (! isset($data['source'])) {
            return;
        }

        if ($data['source'] == 'email') {
            foreach ($this->emailParser->getAttachments() as $attachment) {
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
