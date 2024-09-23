<?php

namespace Webkul\Email\Enums;

enum SupportedFolderEnum: string
{
    /**
     * Inbox.
     */
    case INBOX = 'inbox';

    /**
     * Important.
     */
    case IMPORTANT = 'important';

    /**
     * Starred.
     */
    case STARRED = 'starred';

    /**
     * Draft.
     */
    case DRAFT = 'draft';

    /**
     * Outbox.
     */
    case OUTBOX = 'outbox';

    /**
     * Sent.
     */
    case SENT = 'sent';

    /**
     * Spam.
     */
    case SPAM = 'spam';

    /**
     * Trash.
     */
    case TRASH = 'trash';
}
