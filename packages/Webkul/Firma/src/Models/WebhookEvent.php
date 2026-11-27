<?php

namespace Webkul\Firma\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Firma\Contracts\WebhookEvent as WebhookEventContract;

/**
 * A Firma webhook delivery already processed, keyed by the payload's top level id.
 *
 * Firma retries a delivery up to 3 times with the same id, so a repeated id is acknowledged
 * without being processed again.
 *
 * @property int $id
 * @property string $event_id
 * @property string|null $event_type
 * @property string|null $firma_id
 */
class WebhookEvent extends Model implements WebhookEventContract
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'firma_webhook_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'event_id',
        'event_type',
        'firma_id',
    ];
}
