<?php

namespace Webkul\Admin\Listeners;

use Webkul\Admin\Services\NotificationService;

/**
 * Listens to the events the modules already dispatch, so notifications are added without any
 * module's controller having to know they exist.
 */
class Notification
{
    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(protected NotificationService $notificationService) {}

    /**
     * A record was created.
     */
    public function created($record): void
    {
        $this->notificationService->notify($this->entityFor($record), 'created', $record);
    }

    /**
     * A record was updated.
     */
    public function updated($record): void
    {
        $this->notificationService->notify($this->entityFor($record), 'updated', $record);
    }

    /**
     * Which entity a record belongs to.
     *
     * The events carry the model rather than a name, so the name is taken from the model's class.
     * A record arriving as a bare id — as some delete events do — has no entity to resolve and is
     * ignored rather than guessed at.
     */
    protected function entityFor($record): string
    {
        if (! is_object($record)) {
            return '';
        }

        return match (class_basename($record)) {
            'Lead' => 'lead',
            'Quote' => 'quote',
            'Person' => 'person',
            'Product' => 'product',
            'Activity' => 'activity',
            'Email' => 'email',
            default => '',
        };
    }
}
