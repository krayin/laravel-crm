<?php

namespace Webkul\Email\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
     * Get image url for the product image.
     */
    public function url()
    {
        return Storage::url($this->path);
    }

    /**
     * Accessor for the 'url' attribute.
     */
    public function getUrlAttribute()
    {
        return $this->url();
    }
}
