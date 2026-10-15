<?php

namespace Webkul\Admin\Http\Controllers\Contact\Persons;

use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Traits\HandlesActivityTimeline;
use Webkul\Email\Repositories\AttachmentRepository;

class ActivityController extends Controller
{
    use HandlesActivityTimeline;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ActivityRepository $activityRepository,
        protected AttachmentRepository $attachmentRepository
    ) {}

    /**
     * The pivot linking activities to the person.
     */
    protected function timelinePivot(): array
    {
        return ['table' => 'person_activities', 'foreign' => 'person_id'];
    }

    /**
     * The timeline carries the record's notes and correspondence, so it is held to the same data
     * scope as the record's own page.
     */
    protected function timelineOwnerTable(): ?string
    {
        return 'persons';
    }

    /**
     * Emails are linked to the person through the `person_id` column.
     */
    protected function timelineEmailColumn(): ?string
    {
        return 'person_id';
    }
}
