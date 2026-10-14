<?php

namespace Webkul\Admin\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\User\Repositories\UserRepository;

class NotificationRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected UserRepository $userRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Admin\Contracts\Notification';
    }

    /**
     * Which permission a user must hold to be told about each kind of record.
     *
     * Data scope and role permissions are independent in Krayin: a user may have a global scope
     * and still have no access to a module at all. Scope alone therefore decides only *whose*
     * records a user may know about, not *which kinds* — both have to agree before a notification
     * is addressed to them.
     */
    const ENTITY_PERMISSIONS = [
        'lead' => 'leads',
        'quote' => 'quotes',
        'person' => 'contacts.persons',
        'product' => 'products',
        'activity' => 'activities',
        'email' => 'mail',
    ];

    /**
     * The users who should be told about something one user did.
     *
     * A notification is addressed to people rather than to a role, so the audience is resolved
     * once, when the event happens. A user is told only when all of the following hold:
     *
     *  - their role grants them access to that kind of record;
     *  - their data scope covers the record's owner — global sees everything, group sees that
     *    group's records, individual sees only their own;
     *  - their account is still active;
     *  - they are not the person who caused it, since being told about your own action is noise.
     *
     * @param  string  $entityType  the kind of record, as keyed in `ENTITY_PERMISSIONS`
     * @param  int|null  $ownerId  the user the record belongs to
     * @param  int|null  $causerId  the user who acted
     */
    public function resolveRecipients(string $entityType, ?int $ownerId, ?int $causerId): array
    {
        $users = $this->userRepository->all();

        $recipients = [];

        foreach ($users as $user) {
            if ($user->id === $causerId) {
                continue;
            }

            /**
             * A deactivated account cannot sign in to read what it is sent, so addressing it only
             * accumulates rows nobody will ever see.
             */
            if (! $user->status) {
                continue;
            }

            if (! $this->canAccess($user, $entityType)) {
                continue;
            }

            if ($this->canSee($user, $ownerId)) {
                $recipients[] = $user->id;
            }
        }

        return $recipients;
    }

    /**
     * Whether a user's role lets them reach this kind of record at all.
     *
     * Without this a user with a global data scope but, say, a contacts-only role would be told
     * about every lead in the system — the record's own title and owner — while being unable to
     * open any of them.
     */
    protected function canAccess($user, string $entityType): bool
    {
        $permission = self::ENTITY_PERMISSIONS[$entityType] ?? null;

        /**
         * An entity with no permission mapped is not assumed to be public: nothing is sent for it
         * rather than sending it to everyone.
         */
        if (! $permission) {
            return false;
        }

        if (! $user->role) {
            return false;
        }

        if ($user->role->permission_type === 'all') {
            return true;
        }

        return $user->hasPermission($permission);
    }

    /**
     * Whether a user's data scope covers a record owned by someone.
     *
     * This mirrors `bouncer()->getAuthorizedUserIds()`, which answers the same question for the
     * *acting* user from the session. Here the question is asked about another user, so the scope
     * is read from their record instead of from the request.
     */
    protected function canSee($user, ?int $ownerId): bool
    {
        if ($user->view_permission === 'global') {
            return true;
        }

        /**
         * An unowned record — one created with no user attached — is only ever shown to users with
         * a global scope. Showing it to everyone else would leak records that no scope covers.
         */
        if (is_null($ownerId)) {
            return false;
        }

        if ($user->view_permission === 'group') {
            return in_array($ownerId, $this->groupUserIds($user), true);
        }

        return $user->id === $ownerId;
    }

    /**
     * The ids of every user sharing a group with this one, including the user themselves.
     */
    protected function groupUserIds($user): array
    {
        $ids = [$user->id];

        foreach ($user->groups as $group) {
            foreach ($group->users as $groupUser) {
                $ids[] = $groupUser->id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * The newest notifications for a user, with the unread count.
     */
    public function getForUser(int $userId, int $limit = 10): array
    {
        $notifications = $this->model
            ->with('causer')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return [
            'notifications' => $notifications,
            'unread_count' => $this->unreadCount($userId),
        ];
    }

    /**
     * How many notifications this user has not yet read.
     */
    public function unreadCount(int $userId): int
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}
