<?php

namespace Webkul\Firma\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Activity\Models\Activity;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Activity\Repositories\FileRepository;
use Webkul\Firma\Models\SignatureRequest;
use Webkul\Firma\Repositories\SignatureRequestRepository;

/**
 * Writes Firma events onto the lead's activity timeline.
 */
class LeadTimeline
{
    /**
     * Create a new timeline instance.
     */
    public function __construct(
        protected ActivityRepository $activityRepository,
        protected FileRepository $fileRepository,
        protected SignatureRequestRepository $signatureRequestRepository,
    ) {}

    /**
     * Adds a system entry to the lead of the request.
     */
    public function note(SignatureRequest $request, string $title, ?int $userId = null): void
    {
        $lead = $request->lead;

        if (! $lead) {
            return;
        }

        /** @var Activity $activity */
        $activity = $this->activityRepository->create([
            'type' => 'system',
            'title' => Str::limit($title, 252),
            'is_done' => 1,
            'user_id' => $userId,
        ]);

        $lead->activities()->attach($activity->id);
    }

    /**
     * Attaches the signed PDF to the lead as a file activity, once per request.
     *
     * Returns false when another run attached it first or the lead is gone.
     */
    public function attachSignedDocument(SignatureRequest $request, string $content): bool
    {
        return DB::transaction(function () use ($request, $content) {
            /** @var SignatureRequest|null $locked */
            $locked = $this->signatureRequestRepository->getModel()->newQuery()->whereKey($request->id)->lockForUpdate()->first();

            if (! $locked || $locked->signed_activity_id || ! $locked->lead) {
                return false;
            }

            /** @var Activity $activity */
            $activity = $this->activityRepository->create([
                'type' => 'file',
                'title' => Str::limit(trans('admin::app.firma.timeline.signed-document', ['name' => $locked->name]), 252),
                'is_done' => 1,
                'user_id' => $locked->user_id,
            ]);

            $path = 'activities/'.$activity->id.'/'.Str::uuid().'.pdf';

            Storage::put($path, $content);

            $this->fileRepository->create([
                'name' => Str::limit(str_replace(['/', '\\'], '_', $locked->name), 200, '').' (signed).pdf',
                'path' => $path,
                'activity_id' => $activity->id,
            ]);

            $locked->lead->activities()->attach($activity->id);

            $locked->update([
                'signed_activity_id' => $activity->id,
                'fetch_failed' => false,
            ]);

            return true;
        });
    }
}
