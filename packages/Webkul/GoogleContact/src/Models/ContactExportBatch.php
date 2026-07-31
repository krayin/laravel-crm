<?php

namespace Webkul\GoogleContact\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\GoogleContact\Contracts\ContactExportBatch as ContactExportBatchContract;
use Webkul\User\Models\UserProxy;

class ContactExportBatch extends Model implements ContactExportBatchContract
{
    public const STATE_PENDING = 'pending';

    public const STATE_FETCHING_EXISTING = 'fetching_existing';

    public const STATE_PROCESSING = 'processing';

    public const STATE_COMPLETED = 'completed';

    public const STATE_COMPLETED_WITH_ERRORS = 'completed_with_errors';

    public const STATE_FAILED = 'failed';

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'contact_export_batches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'state',
        'total_count',
        'exported_count',
        'duplicate_count',
        'failed_count',
        'error_file_path',
        'started_at',
        'completed_at',
    ];

    /**
     * The attributes that are castable.
     *
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * States that mean the batch is no longer running.
     */
    public const TERMINAL_STATES = [
        self::STATE_COMPLETED,
        self::STATE_COMPLETED_WITH_ERRORS,
        self::STATE_FAILED,
    ];

    /**
     * Get the user who triggered the export.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * Get the items belonging to this export batch.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ContactExportBatchItemProxy::modelClass(), 'batch_id');
    }

    /**
     * Whether the batch has reached a terminal state.
     */
    public function isFinished(): bool
    {
        return in_array($this->state, self::TERMINAL_STATES);
    }
}
