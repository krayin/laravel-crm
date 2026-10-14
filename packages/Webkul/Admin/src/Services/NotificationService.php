<?php

namespace Webkul\Admin\Services;

use Illuminate\Support\Facades\Log;
use Webkul\Admin\Events\NotificationCreated;
use Webkul\Admin\Repositories\NotificationRepository;

/**
 * Turns something that happened to a record into notifications addressed to the users entitled
 * to know about it.
 */
class NotificationService
{
    /**
     * How each entity describes itself: which column holds its owner, how it is titled in the
     * list, and where the bell sends the reader.
     *
     * Keeping this in one table rather than in each listener means a new entity is added by
     * describing it, and every entity is addressed and routed the same way.
     */
    const ENTITIES = [
        'lead' => [
            'owner' => 'user_id',
            'label' => 'title',
            'route' => 'admin.leads.view',
        ],
        'quote' => [
            'owner' => 'user_id',
            'label' => 'subject',
            'route' => 'admin.quotes.edit',
        ],
        'person' => [
            'owner' => 'user_id',
            'label' => 'name',
            'route' => 'admin.contacts.persons.view',
        ],
        'product' => [
            'owner' => null,
            'label' => 'name',
            'route' => 'admin.products.edit',
        ],
        'activity' => [
            'owner' => 'user_id',
            'label' => 'title',
            'route' => null,
        ],
        /**
         * A mail is reached through its folder rather than by id alone, so the bell opens the
         * inbox instead of building a link that would not resolve to the message.
         */
        'email' => [
            'owner' => 'user_id',
            'label' => 'subject',
            'route' => 'admin.mail.index',
            'params' => ['inbox'],
        ],
    ];

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(protected NotificationRepository $notificationRepository) {}

    /**
     * Record that something happened, and tell everyone entitled to know.
     *
     * @param  string  $entityType  one of the keys in `ENTITIES`
     * @param  string  $action  `created` or `updated`
     * @param  mixed  $record  the record it happened to
     */
    public function notify(string $entityType, string $action, $record): void
    {
        /**
         * A notification is a side effect of the user's actual request. It must never be the
         * reason that request fails, so anything going wrong here is logged and swallowed.
         */
        try {
            $definition = self::ENTITIES[$entityType] ?? null;

            if (! $definition) {
                return;
            }

            $ownerId = $definition['owner'] ? $record->{$definition['owner']} ?? null : null;

            $causerId = auth()->guard('user')->id();

            $recipients = $this->notificationRepository->resolveRecipients(
                $entityType,
                $ownerId ? (int) $ownerId : null,
                $causerId ? (int) $causerId : null
            );

            if (empty($recipients)) {
                return;
            }

            foreach ($recipients as $userId) {
                $notification = $this->notificationRepository->create([
                    'entity_type' => $entityType,
                    'action' => $action,
                    'entity_id' => $record->id ?? null,
                    'user_id' => $userId,
                    'causer_id' => $causerId,
                    'title' => $this->title($entityType, $action),
                    'description' => $this->description($record, $definition),
                    'route' => $this->route($definition, $record),
                    'is_read' => false,
                ]);

                $this->broadcast($notification);
            }
        } catch (\Throwable $e) {
            Log::error('Notification could not be recorded: '.$e->getMessage(), [
                'entity_type' => $entityType,
                'action' => $action,
            ]);
        }
    }

    /**
     * The headline, e.g. "New lead created".
     */
    protected function title(string $entityType, string $action): string
    {
        return trans('admin::app.notifications.titles.'.$entityType.'-'.$action);
    }

    /**
     * The record's own name, which is what makes one notification distinguishable from another.
     */
    protected function description($record, array $definition): ?string
    {
        $value = $record->{$definition['label']} ?? null;

        return $value ? (string) $value : null;
    }

    /**
     * Where the bell sends the reader, or null when the entity has no page of its own.
     */
    protected function route(array $definition, $record): ?string
    {
        if (! $definition['route']) {
            return null;
        }

        if (! isset($definition['params']) && ! isset($record->id)) {
            return null;
        }

        try {
            /**
             * A route whose parameters do not match the record is not worth a broken link in the
             * bell; the notification is still listed, just without one.
             */
            return route($definition['route'], $definition['params'] ?? $record->id);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Publish the notification, when a realtime driver is in use.
     *
     * Under polling there is nothing to publish: the bell fetches the rows that were just written
     * over HTTP instead. A socket driver that is named but not fully set up resolves to polling,
     * so nothing is published for it either.
     */
    protected function broadcast($notification): void
    {
        if (NotificationDriver::resolve() === NotificationDriver::POLLING) {
            return;
        }

        try {
            NotificationCreated::dispatch($notification);
        } catch (\Throwable $e) {
            Log::warning('Notification could not be broadcast: '.$e->getMessage());
        }
    }
}
