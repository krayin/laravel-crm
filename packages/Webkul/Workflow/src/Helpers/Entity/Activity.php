<?php

namespace Webkul\Workflow\Helpers\Entity;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Webkul\Admin\Notifications\Common;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Activity\Repositories\ActivityRepository;

class Activity extends AbstractEntity
{
    /**
     * @var string  $code
     */
    protected $entityType = 'activities';

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected EmailTemplateRepository $emailTemplateRepository,
        protected LeadRepository $leadRepository,
        protected PersonRepository $personRepository,
        protected ActivityRepository $activityRepository
    ) {
    }

    /**
     * Returns attributes
     *
     * @param  string  $entityType
     * @param  array  $skipAttributes
     * @return array
     */
    public function getAttributes($entityType, $skipAttributes = [])
    {
        $attributes = [
            [
                'id'          => 'title',
                'type'        => 'text',
                'name'        => 'Title',
                'lookup_type' => null,
                'options'     => collect([]),
            ], [
                'id'          => 'type',
                'type'        => 'multiselect',
                'name'        => 'Type',
                'lookup_type' => null,
                'options'     => collect([
                    (object) [
                        'id'   => 'note',
                        'name' => 'Note',
                    ], (object) [
                        'id'   => 'call',
                        'name' => 'Call',
                    ], (object) [
                        'id'   => 'meeting',
                        'name' => 'Meeting',
                    ], (object) [
                        'id'   => 'lunch',
                        'name' => 'Lunch',
                    ], (object) [
                        'id'   => 'file',
                        'name' => 'File',
                    ],
                ]),
            ], [
                'id'          => 'location',
                'type'        => 'text',
                'name'        => 'Location',
                'lookup_type' => null,
                'options'     => collect([]),
            ], [
                'id'          => 'comment',
                'type'        => 'textarea',
                'name'        => 'Comment',
                'lookup_type' => null,
                'options'     => collect([]),
            ], [
                'id'          => 'schedule_from',
                'type'        => 'datetime',
                'name'        => 'Schedule From',
                'lookup_type' => null,
                'options'     => collect([]),
            ], [
                'id'          => 'schedule_to',
                'type'        => 'datetime',
                'name'        => 'Schedule To',
                'lookup_type' => null,
                'options'     => collect([]),
            ], [
                'id'          => 'user_id',
                'type'        => 'select',
                'name'        => 'User',
                'lookup_type' => 'users',
                'options'     => $this->attributeRepository->getLookUpOptions('users'),
            ]
        ];

        return $attributes;
    }

    /**
     * Returns placeholders for email templates
     * 
     * @param  array  $entity
     * @return array
     */
    public function getEmailTemplatePlaceholders($entity)
    {
        $emailTemplates = parent::getEmailTemplatePlaceholders($entity);

        $emailTemplates['menu'][] = [
            'text'  => 'Participants',
            'value' => '{%activities.participants%}'
        ];

        return $emailTemplates;
    }

    /**
     * Replace placeholders with values
     * 
     * @param  array  $entity
     * @param  array  $values
     * @return string
     */
    public function replacePlaceholders($entity, $content)
    {
        $content = parent::replacePlaceholders($entity, $content);

        $value = '<ul style="padding-left: 18px;margin: 0;">';

        foreach ($entity->participants as $participant) {
            $value .= '<li>' . ($participant->user ? $participant->user->name : $participant->person->name) . '</li>';
        }

        $value .= '</ul>';

        $content = strtr($content, [
            '{%' . $this->entityType . '.participants%}'   => $value,
            '{% ' . $this->entityType . '.participants %}' => $value,
        ]);

        return $content;
    }

    /**
     * Returns entity
     * 
     * @param  \Webkul\Activity\Contracts\Activity|integer  $entity
     * @return \Webkul\Activity\Contracts\Activity
     */
    public function getEntity($entity)
    {
        if (! $entity instanceof \Webkul\Activity\Contracts\Activity) {
            $entity = $this->activityRepository->find($entity);
        }

        return $entity;
    }

    /**
     * Returns workflow actions
     * 
     * @return array
     */
    public function getActions()
    {
        $emailTemplates = $this->emailTemplateRepository->all(['id', 'name']);

        return [
            [
                'id'         => 'update_related_leads',
                'name'       => __('admin::app.settings.workflows.update-related-leads'),
                'attributes' => $this->getAttributes('leads'),
            ], [
                'id'      => 'send_email_to_sales_owner',
                'name'    => __('admin::app.settings.workflows.send-email-to-sales-owner'),
                'options' => $emailTemplates,
            ], [
                'id'      => 'send_email_to_participants',
                'name'    => __('admin::app.settings.workflows.send-email-to-participants'),
                'options' => $emailTemplates,
            ], [
                'id'   => 'trigger_webhook',
                'name' => __('admin::app.settings.workflows.add-webhook'),
                'request_methods' => [
                    'get' => __('admin::app.settings.workflows.get_method'),
                    'post' => __('admin::app.settings.workflows.post_method'),
                    'put' => __('admin::app.settings.workflows.put_method'),
                    'patch' => __('admin::app.settings.workflows.patch_method'),
                    'delete' => __('admin::app.settings.workflows.delete_method'),
                ],
                'encodings' => [
                    'json' => __('admin::app.settings.workflows.encoding_json'),
                    'http_query' => __('admin::app.settings.workflows.encoding_http_query')
                ]
            ],
        ];
    }

    /**
     * Execute workflow actions
     * 
     * @param  \Webkul\Workflow\Contracts\Workflow  $workflow
     * @param  \Webkul\Activity\Contracts\Activity  $activity
     * @return array
     */
    public function executeActions($workflow, $activity)
    {
        foreach ($workflow->actions as $action) {
            switch ($action['id']) {
                case 'update_related_leads':
                    $leadIds = $this->activityRepository->getModel()
                        ->leftJoin('lead_activities', 'activities.id', 'lead_activities.activity_id')
                        ->leftJoin('leads', 'lead_activities.lead_id', 'leads.id')
                        ->addSelect('leads.id')
                        ->where('activities.id', $activity->id)
                        ->pluck('id');

                    foreach ($leadIds as $leadId) {
                        $this->leadRepository->update([
                            'entity_type'        => 'leads',
                            $action['attribute'] => $action['value'],
                        ], $leadId);
                    }

                    break;

                case 'send_email_to_sales_owner':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        Mail::queue(new Common([
                            'to'          => $activity->user->email,
                            'subject'     => $this->replacePlaceholders($activity, $emailTemplate->subject),
                            'body'        => $this->replacePlaceholders($activity, $emailTemplate->content),
                            'attachments' => [
                                [
                                    'name'    => 'invite.ics',
                                    'mime'    => 'text/calendar',
                                    'content' => $this->getICSContent($activity),
                                ],
                            ],
                        ]));
                    } catch (\Exception $e) {}

                    break;

                case 'send_email_to_participants':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        foreach ($activity->participants as $participant) {
                            Mail::queue(new Common([
                                'to'          => $participant->user
                                    ? $participant->user->email
                                    : data_get($participant->person->emails, '*.value'),
                                'subject'     => $this->replacePlaceholders($activity, $emailTemplate->subject),
                                'body'        => $this->replacePlaceholders($activity, $emailTemplate->content),
                                'attachments' => [
                                    [
                                        'name'    => 'invite.ics',
                                        'mime'    => 'text/calendar',
                                        'content' => $this->getICSContent($activity),
                                    ],
                                ],
                            ]));
                        }
                    } catch (\Exception $e) {}

                    break;

                case 'trigger_webhook':
                    if (isset($action['hook'])) {
                        try {
                            $this->triggerWebhook(
                                $action['hook'],
                                $activity
                            );
                        } catch (\Exception $e) {}
                    }

                    break;
            }
        }
    }

    /**
     * trigger webhook
     * 
     * @param  $hook
     * @param  $activity
     * @return void
     */
    private function triggerWebhook($hook, $activity)
    {
        if (in_array($hook['method'], ['get', 'delete'])) {
            Http::withHeaders(
                $this->formatHeaders($hook)
            )->{$hook['method']}(
                $hook['url']
            );
        } else {
            Http::withHeaders(
                $this->formatHeaders($hook)
            )->{$hook['method']}(
                $hook['url'],
                $this->getRequestBody($hook, $activity)
            );
        }
    }

    /**
     * format headers
     * 
     * @param  $hook
     * @return array
     */
    private function formatHeaders($hook)
    {
        $results = ($hook['encoding'] == 'json')
            ? array('Content-Type: application/json')
            : array('Content-Type: application/x-www-form-urlencoded');

        if (isset($hook['headers'])) {
            array_walk(
                $hook['headers'],
                function (&$arr, $key) use (&$results) {
                    $results[$arr['key']] = $arr['value'];
                }
            );
        }

        return $results;
    }

    /**
     * prepare request body
     * 
     * @param  $hook
     * @param  $quote
     * @return array
     */
    private function getRequestBody($hook, $activity)
    {
        $hook['simple'] = str_replace(
            'activity_',
            '',
            $hook['simple']
        );

        $results = $this
            ->quoteRepository
            ->find($activity->id)
            ->get($hook['simple'])
            ->first()
            ->toArray();

        if (isset($hook['custom'])) {
            $custom_unformatted = preg_split(
                "/[\r\n,]+/",
                $hook['custom']
            );

            array_walk(
                $custom_unformatted,
                function (&$raw, $key) use (&$custom_results) {
                    $arr = explode('=', $raw);

                    $custom_results[$arr[0]] = $arr[1];
                }
            );

            $results = array_merge(
                $results,
                $custom_results
            );
        }

        return ($hook['encoding'] == 'http_query')
            ? Arr::query($results)
            : json_encode($results);
    }

    /**
     * Returns .ics file for attachments
     * 
     * @param  \Webkul\Activity\Contracts\Activity  $activity
     * @return string
     */
    public function getICSContent($activity)
    {
        $content = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Krayincrm//Krayincrm//EN',
            'BEGIN:VEVENT',
            'UID:' . time() . '-' . $activity->id,
            'DTSTAMP:' . Carbon::now()->format('YmdTHis'),
            'CREATED:' . $activity->created_at->format('YmdTHis'),
            'SEQUENCE:1',
            'ORGANIZER;CN=' . $activity->user->name . ':MAILTO:' . $activity->user->email,
        ];

        foreach ($activity->participants as $participant) {
            $emails = $participant->user
                ? [$participant->user->email]
                : data_get($participant->person->emails, '*.value');

            if ($participant->user) {
                $content[] = 'ATTENDEE;ROLE=REQ-PARTICIPANT;CN=' . $participant->user->name . ';PARTSTAT=NEEDS-ACTION:MAILTO:' . $participant->user->email;
            } else {
                foreach (data_get($participant->person->emails, '*.value') as $email) {
                    $content[] = 'ATTENDEE;ROLE=REQ-PARTICIPANT;CN=' . $participant->person->name . ';PARTSTAT=NEEDS-ACTION:MAILTO:' . $email;
                }
            }
        }

        $content = array_merge($content, [
            'DTSTART:' . $activity->schedule_from->format('YmdTHis'),
            'DTEND:' . $activity->schedule_to->format('YmdTHis'),
            'SUMMARY:' . $activity->title,
            'LOCATION:' . $activity->location,
            'DESCRIPTION:' . $activity->comment,
            'END:VEVENT',
            'END:VCALENDAR',
        ]);

        return implode("\r\n", $content);
    }
}
