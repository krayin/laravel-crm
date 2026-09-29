<?php

namespace Webkul\GoogleContact\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Contact\Models\PersonProxy;
use Webkul\GoogleContact\Contracts\ContactExportBatchItem as ContactExportBatchItemContract;

class ContactExportBatchItem extends Model implements ContactExportBatchItemContract
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_DUPLICATE = 'duplicate';

    public const STATUS_EXPORTED = 'exported';

    public const STATUS_FAILED = 'failed';

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'contact_export_batch_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'batch_id',
        'person_id',
        'status',
        'google_resource_name',
        'error_message',
    ];

    /**
     * Get the export batch this item belongs to.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ContactExportBatchProxy::modelClass(), 'batch_id');
    }

    /**
     * Get the CRM person this item refers to.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(PersonProxy::modelClass(), 'person_id');
    }
}
