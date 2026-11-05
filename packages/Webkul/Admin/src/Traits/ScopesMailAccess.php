<?php

namespace Webkul\Admin\Traits;

use Webkul\Email\Repositories\EmailRepository;

/**
 * The data-scope rule for mail, shared by the mail controllers and the listeners that link mail to
 * new records. `EmailDataGrid::prepareQueryBuilder()` applies the same rule in SQL.
 *
 * A mail linked to a lead or person is scoped to whoever owns those records; unlinked mail stays
 * available to everyone with mail access, because the mailbox itself is shared.
 */
trait ScopesMailAccess
{
    /**
     * Resolve the user ids a mail is scoped to, through the lead and person it is linked to and
     * those of every mail above it in its thread.
     *
     * The thread is included because a reply is stored unlinked under its root, and the mail
     * resource returns the parent mail in full: without it, any reply would hand out the root's
     * subject, body and linked lead to whoever opened the reply.
     *
     * An unlinked thread returns an empty list.
     */
    protected function emailOwnerIds($email): array
    {
        $ownerIds = [];

        $visitedIds = [];

        while ($email && ! in_array($email->id, $visitedIds)) {
            $visitedIds[] = $email->id;

            $ownerIds[] = $email->lead?->user_id;

            $ownerIds[] = $email->person?->user_id;

            $email = $email->parent;
        }

        return array_values(array_unique(array_filter($ownerIds)));
    }

    /**
     * Whether a mail is inside the given data scope (`null` meaning unrestricted).
     *
     * Every owner must be in scope, not just one of them: the mail resource discloses the linked
     * lead, person and parent mail together, so a mail tied to one record of the caller's and one
     * of someone else's would otherwise hand them the other record.
     */
    protected function isMailInScope($email, ?array $userIds): bool
    {
        if ($userIds === null) {
            return true;
        }

        /**
         * A linked lead or person with no owner counts as out of scope, as it does on its own page:
         * `preventUnauthorizedAccess(null)` refuses it, and the mail would hand it out in full.
         */
        foreach ($this->emailLinkedRecords($email) as $record) {
            if (! in_array($record->user_id, $userIds)) {
                return false;
            }
        }

        return true;
    }

    /**
     * The leads and persons a mail is linked to, directly or through the mails above it in its
     * thread.
     */
    protected function emailLinkedRecords($email): array
    {
        $records = [];

        $visitedIds = [];

        while ($email && ! in_array($email->id, $visitedIds)) {
            $visitedIds[] = $email->id;

            array_push($records, ...array_filter([$email->lead, $email->person]));

            $email = $email->parent;
        }

        return $records;
    }

    /**
     * Deny access to a mail linked, directly or through its thread, to a lead or person outside the
     * acting user's data scope. Unlinked mail is left alone: the mailbox itself is shared.
     *
     * `preventUnauthorizedAccess()` is not used directly because it denies on an empty owner list,
     * which would make shared, unlinked mail unreachable for restricted users, and accepts a record
     * when any one owner is in scope.
     */
    protected function preventUnauthorizedMailAccess($email): void
    {
        abort_unless(
            $this->isMailInScope($email, bouncer()->getAuthorizedUserIds()),
            401,
            trans('admin::app.errors.unauthorized')
        );
    }

    /**
     * The mail named by the request's `email_id`, when the signed-in user may file a new record
     * owned by `$ownerId` under it; `null` otherwise.
     *
     * Linking writes to the mail, so it takes the same checks as linking through the mail
     * controller: the mail itself must be in the user's scope, and so must the record it is filed
     * under. Requests without a signed-in user — the public web form — never link anything.
     */
    protected function linkableRequestedEmail($ownerId)
    {
        $emailId = request('email_id');

        if (
            ! is_numeric($emailId)
            || ! auth()->guard('user')->check()
        ) {
            return null;
        }

        $email = app(EmailRepository::class)->find($emailId);

        if (! $email) {
            return null;
        }

        $userIds = bouncer()->getAuthorizedUserIds();

        if (! $this->isMailInScope($email, $userIds)) {
            return null;
        }

        if (
            $userIds !== null
            && ! in_array($ownerId, $userIds)
        ) {
            return null;
        }

        return $email;
    }
}
