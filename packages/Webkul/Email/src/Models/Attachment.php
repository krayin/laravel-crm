<?php

namespace Webkul\Email\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Webkul\Email\Contracts\Attachment as AttachmentContract;

class Attachment extends Model implements AttachmentContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string
     */
    protected $table = 'email_attachments';

    /**
     * The attributes that are appended.
     *
     * @var array
     */
    protected $appends = ['url'];

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
        'content_id',
        'email_id',
    ];

    /**
     * Get the email.
     */
    public function email()
    {
        return $this->belongsTo(EmailProxy::modelClass());
    }

    /**
     * Get the download url for the attachment.
     *
     * Attachments are stored on the private disk and are never exposed through a public storage url;
     * they are served only through the authenticated download route.
     */
    public function url()
    {
        return Route::has('admin.mail.attachment_download')
            ? route('admin.mail.attachment_download', $this->id)
            : null;
    }

    /**
     * Accessor for the 'url' attribute.
     */
    public function getUrlAttribute()
    {
        return $this->url();
    }
}
