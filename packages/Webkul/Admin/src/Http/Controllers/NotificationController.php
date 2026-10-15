<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Webkul\Admin\Repositories\NotificationRepository;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected NotificationRepository $notificationRepository) {}

    /**
     * The acting user's notifications.
     *
     * Every query here is keyed on the authenticated user's own id rather than on anything the
     * request carries, so one user cannot read another's feed by changing a parameter.
     */
    public function index(): JsonResponse
    {
        $userId = auth()->guard('user')->id();

        $result = $this->notificationRepository->getForUser($userId);

        return new JsonResponse([
            'data' => $result['notifications']->map(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'description' => $notification->description,
                'route' => $notification->route,
                'entity_type' => $notification->entity_type,
                'action' => $notification->action,
                'is_read' => $notification->is_read,
                'causer' => $notification->causer?->name,
                'created_at' => $notification->created_at?->diffForHumans(),
            ]),
            'meta' => [
                'unread_count' => $result['unread_count'],
            ],
        ]);
    }

    /**
     * Mark one notification as read.
     */
    public function markAsRead(int $id): JsonResponse
    {
        $userId = auth()->guard('user')->id();

        /**
         * Scoped to the acting user, so marking another user's notification read is a miss rather
         * than a write to a row that is not theirs.
         */
        $notification = $this->notificationRepository
            ->findWhere(['id' => $id, 'user_id' => $userId])
            ->first();

        if (! $notification) {
            return new JsonResponse(['message' => trans('admin::app.notifications.not-found')], 404);
        }

        $notification->update(['is_read' => true]);

        return new JsonResponse([
            'message' => trans('admin::app.notifications.marked-as-read'),
            'unread_count' => $this->notificationRepository->unreadCount($userId),
        ]);
    }

    /**
     * Mark every notification as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $userId = auth()->guard('user')->id();

        $this->notificationRepository
            ->getModel()
            ->newQuery()
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return new JsonResponse([
            'message' => trans('admin::app.notifications.marked-all-as-read'),
            'unread_count' => 0,
        ]);
    }
}
