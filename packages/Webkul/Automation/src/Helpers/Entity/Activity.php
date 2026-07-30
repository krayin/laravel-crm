<?php

namespace Webkul\Automation\Helpers\Entity;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Webkul\Activity\Contracts\Activity as ContractsActivity;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Admin\Notifications\Common;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Automation\Repositories\WebhookRepository;
use Webkul\Automation\Services\WebhookService;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Lead\Repositories\LeadRepository;

class Activity extends AbstractEntity
{
    /**
     * Define the entity type.
     *
     * @var string
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
        protected ActivityRepository $activityRepository,
        protected WebhookRepository $webhookRepository,
        protected WebhookService $webhookService
    ) {}

    /**
     * Get the attributes for workflow conditions.
     */
    public function getAttributes(string $entityType, array $skipAttributes = []): array
    {
        $attributes = [
            [
                'id' => 'title',
                'type' => 'text',
                'name' => 'Title',
                'lookup_type' => null,
                'options' => collect(),
            ], [
                'id' => 'type',
                'type' => 'multiselect',
                'name' => 'Type',
                'lookup_type' => null,
                'options' => collect([
                    (object) [
                        'id' => 'note',
                        'name' => 'Note',
                    ], (object) [
                        'id' => 'call',
                        'name' => 'Call',
                    ], (object) [
                        'id' => 'meeting',
                        'name' => 'Meeting',
                    ], (object) [
                        'id' => 'lunch',
                        'name' => 'Lunch',
                    ], (object) [
                        'id' => 'file',
                        'name' => 'File',
                    ],
                ]),
            ], [
                'id' => 'location',
                'type' => 'text',
                'name' => 'Location',
                'lookup_type' => null,
                'options' => collect(),
            ], [
                'id' => 'comment',
                'type' => 'textarea',
                'name' => 'Comment',
                'lookup_type' => null,
                'options' => collect(),
            ], [
                'id' => 'schedule_from',
                'type' => 'datetime',
                'name' => 'Schedule From',
                'lookup_type' => null,
                'options' => collect(),
            ], [
                'id' => 'schedule_to',
                'type' => 'datetime',
                'name' => 'Schedule To',
                'lookup_type' => null,
                'options' => collect(),
            ], [
                'id' => 'user_id',
                'type' => 'select',
                'name' => 'User',
                'lookup_type' => 'users',
                'options' => $this->attributeRepository->getLookUpOptions('users'),
            ],
        ];

        return $attributes;
    }

    /**
     * Returns placeholders for email templates.
     */
    public function getEmailTemplatePlaceholders(array $entity): array
    {
        $emailTemplates = parent::getEmailTemplatePlaceholders($entity);

        $emailTemplates['menu'][] = [
            'text' => 'Participants',
            'value' => '{%activities.participants%}',
        ];

        return $emailTemplates;
    }

    /**
     * Replace placeholders with values.
     */
    public function replacePlaceholders(mixed $entity, string $content): string
    {
        $content = parent::replacePlaceholders($entity, $content);

        $value = '<ul style="padding-left: 18px;margin: 0;">';

        foreach ($entity->participants as $participant) {
            $value .= '<li>'.($participant->user ? $participant->user->name : $participant->person->name).'</li>';
        }

        $value .= '</ul>';

        return strtr($content, [
            '{%'.$this->entityType.'.participants%}' => $value,
            '{% '.$this->entityType.'.participants %}' => $value,
        ]);
    }

    /**
     * Listing of the entities.
     */
    public function getEntity(mixed $entity): mixed
    {
        if (! $entity instanceof ContractsActivity) {
            $entity = $this->activityRepository->find($entity);
        }

        return $entity;
    }

    /**
     * Returns workflow actions.
     */
    public function getActions(): array
    {
        $emailTemplates = $this->emailTemplateRepository->all(['id', 'name']);

        $webhooksOptions = $this->webhookRepository->all(['id', 'name']);

        return [
            [
                'id' => 'update_related_leads',
                'name' => trans('admin::app.settings.workflows.helpers.update-related-leads'),
                'attributes' => $this->getAttributes('leads'),
            ], [
                'id' => 'send_email_to_sales_owner',
                'name' => trans('admin::app.settings.workflows.helpers.send-email-to-sales-owner'),
                'options' => $emailTemplates,
            ], [
                'id' => 'send_email_to_participants',
                'name' => trans('admin::app.settings.workflows.helpers.send-email-to-participants'),
                'options' => $emailTemplates,
            ], [
                'id' => 'trigger_webhook',
                'name' => trans('admin::app.settings.workflows.helpers.add-webhook'),
                'options' => $webhooksOptions,
            ],
        ];
    }

    /**
     * Execute workflow actions.
     */
    public function executeActions(mixed $workflow, mixed $activity): void
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
                        $this->leadRepository->update(
                            [
                                'entity_type' => 'leads',
                                $action['attribute'] => $action['value'],
                            ],
                            $leadId,
                            [$action['attribute']]
                        );
                    }

                    break;

                case 'send_email_to_sales_owner':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        Mail::queue(new Common([
                            'to' => $activity->user->email,
                            'subject' => $this->replacePlaceholders($activity, $emailTemplate->subject),
                            'body' => $this->replacePlaceholders($activity, $emailTemplate->content),
                            'attachments' => [
                                [
                                    'name' => 'invite.ics',
                                    'mime' => 'text/calendar',
                                    'content' => $this->getICSContent($activity),
                                ],
                            ],
                        ]));
                    } catch (\Exception $e) {
                    }

                    break;

                case 'send_email_to_participants':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        foreach ($activity->participants as $participant) {
                            Mail::queue(new Common([
                                'to' => $participant->user
                                    ? $participant->user->email
                                    : data_get($participant->person->emails, '*.value'),
                                'subject' => $this->replacePlaceholders($activity, $emailTemplate->subject),
                                'body' => $this->replacePlaceholders($activity, $emailTemplate->content),
                                'attachments' => [
                                    [
                                        'name' => 'invite.ics',
                                        'mime' => 'text/calendar',
                                        'content' => $this->getICSContent($activity),
                                    ],
                                ],
                            ]));
                        }
                    } catch (\Exception $e) {
                    }

                    break;

                case 'trigger_webhook':
                    try {
                        $this->triggerWebhook($action['value'], $activity);
                    } catch (\Exception $e) {
                        report($e);
                    }

                    break;
            }
        }
    }

    /**
     * Returns .ics file for attachments.
     */
    public function getICSContent(ContractsActivity $activity): string
    {
        $content = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Krayincrm//Krayincrm//EN',
            'METHOD:REQUEST',
            'BEGIN:VEVENT',
            'UID:'.time().'-'.$activity->id,
            'DTSTAMP:'.Carbon::now()->utc()->format('Ymd\THis\Z'),
            'CREATED:'.$activity->created_at->copy()->utc()->format('Ymd\THis\Z'),
            'SEQUENCE:1',
            'ORGANIZER;CN='.$activity->user->name.':MAILTO:'.$activity->user->email,
        ];

        foreach ($activity->participants as $participant) {
            if ($participant->user) {
                $content[] = 'ATTENDEE;ROLE=REQ-PARTICIPANT;CN='.$this->escapeICSParameter($participant->user->name).';PARTSTAT=NEEDS-ACTION:MAILTO:'.$participant->user->email;
            } else {
                foreach (data_get($participant->person->emails, '*.value') as $email) {
                    $content[] = 'ATTENDEE;ROLE=REQ-PARTICIPANT;CN='.$this->escapeICSParameter($participant->person->name).';PARTSTAT=NEEDS-ACTION:MAILTO:'.$email;
                }
            }
        }

        $content = array_merge($content, [
            'DTSTART:'.$activity->schedule_from->copy()->utc()->format('Ymd\THis\Z'),
            'DTEND:'.$activity->schedule_to->copy()->utc()->format('Ymd\THis\Z'),
            'SUMMARY:'.$this->escapeICSText($activity->title),
            'LOCATION:'.$this->escapeICSText($activity->location),
            'DESCRIPTION:'.$this->escapeICSText($activity->comment),
            'END:VEVENT',
            'END:VCALENDAR',
        ]);

        return implode("\r\n", array_map([$this, 'foldICSLine'], $content));
    }

    /**
     * Escape a TEXT property value per RFC 5545 section 3.3.11.
     *
     * Backslash, semicolon and comma are delimiters in a TEXT value, and a
     * line break has no literal representation at all — a raw one ends the
     * content line, so whatever follows is read as a new property. An activity
     * comment is free text written by a user, so it routinely contains all
     * four: a multi-line comment whose second line happens to hold a colon
     * ("Note: ...") yields a property named "Note", which is not a valid name,
     * and strict parsers reject the whole calendar rather than that one line.
     */
    protected function escapeICSText(?string $value): string
    {
        return str_replace(
            ['\\', ';', ',', "\r\n", "\n", "\r"],
            ['\\\\', '\\;', '\\,', '\\n', '\\n', '\\n'],
            (string) $value
        );
    }

    /**
     * Escape a parameter value (CN=, …) per RFC 5545 section 3.2.
     *
     * Parameters follow different rules from TEXT: backslash escaping does not
     * apply, and a value carrying ':', ';' or ',' — "Sanchez, Alejandro" — has
     * to be wrapped in double quotes instead. A double quote itself cannot be
     * represented even inside a quoted string, and control characters cannot
     * be represented at all, so both are dropped.
     */
    protected function escapeICSParameter(?string $value): string
    {
        $value = str_replace('"', '', preg_replace('/[\x00-\x1F\x7F]/', '', (string) $value));

        return preg_match('/[:;,]/', $value)
            ? '"'.$value.'"'
            : $value;
    }

    /**
     * Fold a content line to 75 octets per RFC 5545 section 3.1.
     *
     * Continuation lines are marked by a single leading space, which counts
     * toward the octet budget. Splitting is done per character rather than per
     * byte so a multi-byte UTF-8 sequence is never cut in half.
     */
    protected function foldICSLine(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }

        $lines = [];
        $current = '';
        $limit = 75;

        foreach (mb_str_split($line, 1, 'UTF-8') as $character) {
            if (strlen($current) + strlen($character) > $limit) {
                $lines[] = $current;
                $current = '';
                $limit = 74;
            }

            $current .= $character;
        }

        $lines[] = $current;

        return implode("\r\n ", $lines);
    }
}
