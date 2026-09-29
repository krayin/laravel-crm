<?php

namespace Webkul\Admin\Traits;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Http\Resources\ActivityResource;

/**
 * Builds a paginated activity timeline for a record (lead, person, product,
 * warehouse). Activities and, where applicable, emails are merged into a single
 * ordered stream through a lightweight union of their references, and only the
 * current page is hydrated. This keeps the request cost constant regardless of
 * how much history the record has.
 *
 * The consuming controller must expose an `$activityRepository`, and — when it
 * supports emails — an `$attachmentRepository`.
 */
trait HandlesActivityTimeline
{
    /**
     * Number of timeline entries loaded per request.
     */
    protected int $timelinePerPage = 10;

    /**
     * The pivot that links activities to the record, e.g.
     * ['table' => 'lead_activities', 'foreign' => 'lead_id'].
     */
    abstract protected function timelinePivot(): array;

    /**
     * The emails column that links to the record (e.g. 'lead_id'), or null when
     * the record type does not carry emails.
     */
    protected function timelineEmailColumn(): ?string
    {
        return null;
    }

    /**
     * Display a paginated listing of the record's activity timeline.
     *
     * @param  int  $id
     * @return AnonymousResourceCollection
     */
    public function index($id)
    {
        $type = request()->input('type', 'all');

        // Clamp so a crafted request can't defeat pagination and pull the whole history.
        $perPage = max(1, min(50, (int) request()->input('per_page', $this->timelinePerPage)));

        $page = $this->timelineReferences($id, $type)->paginate($perPage);

        return ActivityResource::collection($this->hydrateTimeline($page->items()))
            ->additional([
                'meta' => [
                    'current_page' => $page->currentPage(),
                    'last_page' => $page->lastPage(),
                    'per_page' => $page->perPage(),
                    'total' => $page->total(),
                ],
            ]);
    }

    /**
     * Build the ordered union of activity and email references for the record.
     *
     * Each row carries only its source, id and timestamp, which is enough to
     * order and paginate the whole timeline without loading every record.
     */
    protected function timelineReferences($id, string $type)
    {
        $pivot = $this->timelinePivot();

        $references = [];

        if ($type !== 'email') {
            $references[] = DB::table('activities')
                ->join($pivot['table'], 'activities.id', '=', $pivot['table'].'.activity_id')
                ->where($pivot['table'].'.'.$pivot['foreign'], $id)
                ->when($type === 'planned', fn ($query) => $query->where('activities.is_done', 0))
                ->when(
                    ! in_array($type, ['all', 'planned'], true),
                    fn ($query) => $query->where('activities.type', $type)
                )
                ->select('activities.id as ref_id', 'activities.created_at', DB::raw("'activity' as source"));
        }

        if (($emailColumn = $this->timelineEmailColumn()) && in_array($type, ['all', 'email'], true)) {
            $references[] = DB::table('emails')
                ->where($emailColumn, $id)
                ->orWhereIn('parent_id', fn ($query) => $query
                    ->select('id')
                    ->from('emails')
                    ->where($emailColumn, $id)
                )
                ->select('emails.id as ref_id', 'emails.created_at', DB::raw("'email' as source"));
        }

        $union = array_shift($references);

        foreach ($references as $reference) {
            $union->unionAll($reference);
        }

        return DB::query()
            ->fromSub($union, 'timeline')
            ->orderByDesc('created_at')
            ->orderByDesc('ref_id');
    }

    /**
     * Hydrate a page of references into activity-shaped items, preserving order.
     */
    protected function hydrateTimeline($references)
    {
        $references = collect($references);

        $activityIds = $references->where('source', 'activity')->pluck('ref_id')->all();
        $emailIds = $references->where('source', 'email')->pluck('ref_id')->all();

        $activities = empty($activityIds)
            ? collect()
            : $this->activityRepository
                ->with(['files', 'participants.user', 'participants.person.organization'])
                ->findWhereIn('id', $activityIds)
                ->keyBy('id');

        $emails = empty($emailIds)
            ? collect()
            : DB::table('emails')->whereIn('id', $emailIds)->get()->keyBy('id');

        $attachments = empty($emailIds)
            ? collect()
            : $this->attachmentRepository->findWhereIn('email_id', $emailIds)->groupBy('email_id');

        return $references
            ->map(fn ($reference) => $reference->source === 'activity'
                ? $activities->get($reference->ref_id)
                : $this->mapEmailAsActivity($emails->get($reference->ref_id), $attachments->get($reference->ref_id, collect()))
            )
            ->filter()
            ->values();
    }

    /**
     * Transform an email row into an activity-shaped object for the timeline.
     */
    protected function mapEmailAsActivity($email, $attachments)
    {
        if (! $email) {
            return null;
        }

        return (object) [
            'id' => $email->id,
            'parent_id' => $email->parent_id,
            'title' => $email->subject,
            'type' => 'email',
            'is_done' => 1,
            'comment' => $email->reply,
            'schedule_from' => null,
            'schedule_to' => null,
            'user' => auth()->guard('user')->user(),
            'participants' => [],
            'location' => null,
            'additional' => [
                'folders' => json_decode($email->folders),
                'from' => json_decode($email->from),
                'to' => json_decode($email->reply_to),
                'cc' => json_decode($email->cc),
                'bcc' => json_decode($email->bcc),
            ],
            'files' => $attachments->map(function ($attachment) {
                return (object) [
                    'id' => $attachment->id,
                    'name' => $attachment->name,
                    'path' => $attachment->path,
                    'url' => $attachment->url,
                    'created_at' => $attachment->created_at,
                    'updated_at' => $attachment->updated_at,
                ];
            }),
            'created_at' => $email->created_at,
            'updated_at' => $email->updated_at,
        ];
    }
}
