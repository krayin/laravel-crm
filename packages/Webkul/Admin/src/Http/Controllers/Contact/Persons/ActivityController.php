<?php

namespace Webkul\Admin\Http\Controllers\Contact\Persons;

use Illuminate\Support\Facades\DB;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Resources\ActivityResource;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Email\Repositories\EmailRepository;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ActivityRepository $activityRepository,
        protected EmailRepository $emailRepository,
        protected AttachmentRepository $attachmentRepository
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $activities = $this->activityRepository
            ->leftJoin('person_activities', 'activities.id', '=', 'person_activities.activity_id')
            ->where('person_activities.person_id', $id)
            ->get();

        $response = $this->concatLeadAsActivities($id, $activities);
        return ActivityResource::collection($this->concatEmailAsActivities($id, $response));
    }

    public function concatLeadAsActivities($personId, $activities){
        $leads = DB::table('leads')
        ->leftJoin('lead_sources', 'leads.lead_source_id', '=', 'lead_sources.id')
        ->leftJoin('lead_types', 'leads.lead_type_id', '=', 'lead_types.id')
        ->leftJoin('lead_pipelines', 'leads.lead_pipeline_id', '=', 'lead_pipelines.id')
        ->leftJoin('lead_pipeline_stages', 'leads.lead_pipeline_stage_id', '=', 'lead_pipeline_stages.id')
        ->where('leads.person_id', $personId)
        ->select(
            'leads.*',
            'lead_sources.name as lead_source_name',
            'lead_types.name as lead_type_name',
            'lead_pipelines.name as lead_pipeline_name',
            'lead_pipeline_stages.name as lead_pipeline_stage_name'
        )
        ->get();

        return $activities->concat($leads->map(function ($lead) {
                return (object) [
                    'id'            => $lead->id,
                    'parent_id'     => null,
                    'title'         => $lead->title ?? 'Lead',
                    'type'          => 'leads',
                    'is_done'       => $lead->status ?? null,
                    'comment'       => $lead->description ?? null,
                    'schedule_from' => $lead->created_at ?? null,
                    'schedule_to'   => $lead->expected_close_date ?? null,
                    'user'          => auth()->guard('user')->user(),
                    'participants'  => [],
                    'location'      => null,
                    'additional'    => [
                        'status'     => $lead->status ?? null,
                        'source'     => $lead->lead_source_name ?? null,
                        'lead_type'  => $lead->lead_type_name ?? null,
                        'lead_stage' => $lead->lead_pipeline_stage_name ?? null,
                        'lost_reason'=> $lead->lost_reason ?? null, 
                        'closed_at'  => $lead->closed_at ?? null,

                    ],
                    'files'         => [],
                    'created_at'    => $lead->created_at,
                    'updated_at'    => $lead->updated_at,
                ];
            }));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function concatEmailAsActivities($personId, $activities)
    {
        $emails = DB::table('emails as child')
            ->select('child.*')
            ->join('emails as parent', 'child.parent_id', '=', 'parent.id')
            ->where('parent.person_id', $personId)
            ->union(DB::table('emails as parent')->where('parent.person_id', $personId))
            ->get();

        logger('Email pór aqui ' . $emails);

        return $activities->concat($emails->map(function ($email) {
            return (object) [
                'id'            => $email->id,
                'parent_id'     => $email->parent_id,
                'title'         => $email->subject,
                'type'          => 'email',
                'is_done'       => 1,
                'comment'       => $email->reply,
                'schedule_from' => null,
                'schedule_to'   => null,
                'user'          => auth()->guard('user')->user(),
                'participants'  => [],
                'location'      => null,
                'additional'    => [
                    'folders' => json_decode($email->folders),
                    'from'    => json_decode($email->from),
                    'to'      => json_decode($email->reply_to),
                    'cc'      => json_decode($email->cc),
                    'bcc'     => json_decode($email->bcc),
                ],
                'files'         => $this->attachmentRepository->findWhere(['email_id' => $email->id])->map(function ($attachment) {
                    return (object) [
                        'id'         => $attachment->id,
                        'name'       => $attachment->name,
                        'path'       => $attachment->path,
                        'url'        => $attachment->url,
                        'created_at' => $attachment->created_at,
                        'updated_at' => $attachment->updated_at,
                    ];
                }),
                'created_at'    => $email->created_at,
                'updated_at'    => $email->updated_at,
            ];
        }))->sortByDesc('id')->sortByDesc('created_at');
    }
}
