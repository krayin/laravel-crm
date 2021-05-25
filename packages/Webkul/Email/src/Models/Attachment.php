<?php

namespace Webkul\Email\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Email\Contracts\Attachment as AttachmentContract;

class Attachment extends Model implements AttachmentContract
{
    protected $table = 'email_thread_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'size',
        'content_type',
        'email_thread_id',
    ];

    /**
     * Get the thread.
     */
    public function thread()
    {
        return $this->belongsTo(ThreadProxy::modelClass());
    }
}
