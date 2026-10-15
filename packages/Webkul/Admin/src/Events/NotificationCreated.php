<?php

namespace Webkul\Admin\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Carries one notification to the user it is addressed to.
 *
 * Broadcasting is only reached when the admin has turned on a realtime driver; under the default
 * polling driver the bell fetches the same rows over HTTP and nothing is published here.
 */
class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $notification) {}

    /**
     * The channel the event should broadcast on.
     *
     * One private channel per user: the audience was already decided when the rows were written,
     * so a channel per user keeps a notification from reaching anyone it was not addressed to.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.'.$this->notification->user_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    /**
     * The data to broadcast.
     *
     * Only what the bell renders is sent. The model is not broadcast wholesale so that a column
     * added later cannot start leaking through the socket unnoticed.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'description' => $this->notification->description,
            'route' => $this->notification->route,
            'entity_type' => $this->notification->entity_type,
            'action' => $this->notification->action,
            'created_at' => $this->notification->created_at?->toIso8601String(),
        ];
    }

    /**
     * Broadcasting a notification must never delay the request that caused it.
     */
    public function broadcastQueue(): string
    {
        return 'notifications';
    }
}
