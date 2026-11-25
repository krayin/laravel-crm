<?php

namespace Webkul\Firma\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Webkul\Activity\Models\ActivityProxy;
use Webkul\Firma\Contracts\SignatureRequest as SignatureRequestContract;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Models\LeadProxy;
use Webkul\Quote\Models\QuoteProxy;
use Webkul\User\Models\UserProxy;

/**
 * A document sent for signature with Firma from a lead.
 *
 * @property int $id
 * @property string|null $firma_id
 * @property string $idempotency_key
 * @property int $lead_id
 * @property int|null $quote_id
 * @property int|null $user_id
 * @property string $name
 * @property string $source
 * @property string|null $template_id
 * @property string|null $template_name
 * @property string $recipient_name
 * @property string $recipient_email
 * @property string $status
 * @property bool $no_invitation_email
 * @property int|null $signed_activity_id
 * @property bool $fetch_failed
 * @property Carbon|null $sent_at
 * @property Carbon|null $completed_at
 * @property-read Lead|null $lead
 */
class SignatureRequest extends Model implements SignatureRequestContract
{
    /**
     * Written before the request to Firma; replaced by the outcome.
     */
    public const STATUS_SENDING = 'sending';

    /**
     * Firma did not confirm the outcome: the request may exist and may have been sent.
     */
    public const STATUS_UNCERTAIN = 'uncertain';

    public const STATUS_SENT = 'sent';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_EXPIRED = 'expired';

    /**
     * Status only moves forward: a finished status never changes, and a late or repeated
     * event never moves a request back.
     */
    public const STATUS_RANK = [
        self::STATUS_SENDING => 0,
        self::STATUS_UNCERTAIN => 0,
        self::STATUS_SENT => 1,
        self::STATUS_IN_PROGRESS => 2,
        self::STATUS_COMPLETED => 3,
        self::STATUS_DECLINED => 3,
        self::STATUS_CANCELLED => 3,
        self::STATUS_EXPIRED => 3,
    ];

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'firma_signature_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firma_id',
        'idempotency_key',
        'lead_id',
        'quote_id',
        'user_id',
        'name',
        'source',
        'template_id',
        'template_name',
        'recipient_name',
        'recipient_email',
        'status',
        'no_invitation_email',
        'signed_activity_id',
        'fetch_failed',
        'sent_at',
        'completed_at',
    ];

    /**
     * The attributes that are castable.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'no_invitation_email' => 'boolean',
        'fetch_failed' => 'boolean',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var list<string>
     */
    protected $hidden = [
        'idempotency_key',
    ];

    /**
     * Applies a status when it moves the request forward. Returns true when it changed.
     */
    public function advanceTo(string $status, mixed $completedAt = null): bool
    {
        if (
            ! isset(self::STATUS_RANK[$status])
            || self::STATUS_RANK[$status] <= (self::STATUS_RANK[$this->status] ?? 0)
        ) {
            return false;
        }

        $this->status = $status;

        if ($status === self::STATUS_COMPLETED) {
            $this->completed_at = $completedAt ?? now();
        }

        $this->save();

        return true;
    }

    /**
     * Get the lead the request was sent from.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(LeadProxy::modelClass());
    }

    /**
     * Get the quote that was sent, when the request carries one.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(QuoteProxy::modelClass());
    }

    /**
     * Get the user who sent the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * Get the activity that holds the signed document.
     */
    public function signedActivity(): BelongsTo
    {
        return $this->belongsTo(ActivityProxy::modelClass(), 'signed_activity_id');
    }
}
