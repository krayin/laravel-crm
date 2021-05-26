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
    function model()
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
     * @param  \Webkul\Email\Contracts\Thread  $thread
     * @param  array $data
     * @return void
     */
    public function uploadAttachments($thread, array $data)
    {
        if ($data['source'] == 'email') {
            foreach ($this->emailParser->getAttachments() as $attachment) {
                $fileName = 'emails/' . $thread->email_id . '/' . $thread->id . '/' . $attachment->getFilename();

                $this->create([
                    'path'            => $path = Storage::put($fileName, $attachment->getContent()),
                    'name'            => $attachment->getFileName(),
                    'content_type'    => $attachment->contentType,
                    'content_id'      => $attachment->contentId,
                    'size'            => Storage::size($path),
                    'email_thread_id' => $thread->id,
                ]);
            }
        } else {
            if (! isset($data['attachments'])) {
                return;
            }

            foreach ($data['attachments'] as $index => $attachment) {
                $this->create([
                    'path'            => $path = request()->file('attachments.' . $index)->store('emails/' . $thread->email_id . '/' . $thread->id),
                    'name'            => $attachment->getClientOriginalName(),
                    'content_type'    => $attachment->getClientMimeType(),
                    'size'            => Storage::size($path),
                    'email_thread_id' => $thread->id,
                ]);
            }
        }
    }
}