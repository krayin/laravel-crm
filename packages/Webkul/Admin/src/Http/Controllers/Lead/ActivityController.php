<?php

namespace Webkul\Admin\Http\Controllers\Lead;

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
     * The pivot linking activities to the lead.
     */
    protected function timelinePivot(): array
    {
        return ['table' => 'lead_activities', 'foreign' => 'lead_id'];
    }

    /**
     * Emails are linked to the lead through the `lead_id` column.
     */
    protected function timelineEmailColumn(): ?string
    {
        return 'lead_id';
    }
}
