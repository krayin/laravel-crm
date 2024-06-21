<?php

namespace Webkul\Workflow\Helpers\Entity;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Webkul\Admin\Notifications\Common;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Tag\Repositories\TagRepository;

class Lead extends AbstractEntity
{
    /**
     * Define the entity type.
     * 
     * @var string $entityType
     */
    protected $entityType = 'leads';

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected EmailTemplateRepository $emailTemplateRepository,
        protected LeadRepository $leadRepository,
        protected ActivityRepository $activityRepository,
        protected PersonRepository $personRepository,
        protected TagRepository $tagRepository
    ) {
    }

    /**
     * Listing of the entities.
     * 
     * @param  \Webkul\Lead\Contracts\Lead  $entity
     * @return \Webkul\Lead\Contracts\Lead
     */
    public function getEntity($entity)
    {
        if (! $entity instanceof \Webkul\Lead\Contracts\Lead) {
            $entity = $this->leadRepository->find($entity);
        }

        return $entity;
    }

    /**
     * Returns attributes.
     *
     * @param  string  $entityType
     * @param  array  $skipAttributes
     * @return array
     */
    public function getAttributes($entityType, $skipAttributes = ['textarea', 'image', 'file', 'address'])
    {
        $attributes[] = [
            'id'          => 'lead_pipeline_stage_id',
            'type'        => 'select',
            'name'        => 'Stage',
            'lookup_type' => 'lead_pipeline_stages',
            'options'     => collect(),
        ];

        return array_merge(
            parent::getAttributes($entityType, $skipAttributes),
            $attributes
        );
    }

    /**
     * Returns workflow actions.
     * 
     * @return array
     */
    public function getActions()
    {
        $emailTemplates = $this->emailTemplateRepository->all(['id', 'name']);

        return [
            [
                'id'         => 'update_lead',
                'name'       => __('admin::app.settings.workflows.update-lead'),
                'attributes' => $this->getAttributes('leads'),
            ], [
                'id'         => 'update_person',
                'name'       => __('admin::app.settings.workflows.update-person'),
                'attributes' => $this->getAttributes('persons'),
            ], [
                'id'      => 'send_email_to_person',
                'name'    => __('admin::app.settings.workflows.send-email-to-person'),
                'options' => $emailTemplates,
            ], [
                'id'      => 'send_email_to_sales_owner',
                'name'    => __('admin::app.settings.workflows.send-email-to-sales-owner'),
                'options' => $emailTemplates,
            ], [
                'id'   => 'add_tag',
                'name' => __('admin::app.settings.workflows.add-tag'),
            ], [
                'id'   => 'add_note_as_activity',
                'name' => __('admin::app.settings.workflows.add-note-as-activity'),
            ], [
                'id'   => 'trigger_webhook',
                'name' => __('admin::app.settings.workflows.add-webhook'),
                'request_methods' => [
                    'get'    => __('admin::app.settings.workflows.get_method'),
                    'post'   => __('admin::app.settings.workflows.post_method'),
                    'put'    => __('admin::app.settings.workflows.put_method'),
                    'patch'  => __('admin::app.settings.workflows.patch_method'),
                    'delete' => __('admin::app.settings.workflows.delete_method'),
                ],
                'encodings' => [
                    'json'       => __('admin::app.settings.workflows.encoding_json'),
                    'http_query' => __('admin::app.settings.workflows.encoding_http_query')
                ]
            ],
        ];
    }

    /**
     * Execute workflow actions.
     * 
     * @param  \Webkul\Workflow\Contracts\Workflow  $workflow
     * @param  \Webkul\Lead\Contracts\Lead  $lead
     * @return array
     */
    public function executeActions($workflow, $lead)
    {
        foreach ($workflow->actions as $action) {
            switch ($action['id']) {
                case 'update_lead':
                    $this->leadRepository->update([
                        'entity_type'        => 'leads',
                        $action['attribute'] => $action['value'],
                    ], $lead->id);

                    break;

                case 'update_person':
                    $this->personRepository->update([
                        'entity_type'        => 'persons',
                        $action['attribute'] => $action['value'],
                    ], $lead->person_id);

                    break;

                case 'send_email_to_person':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        Mail::queue(new Common([
                            'to'      => data_get($lead->person->emails, '*.value'),
                            'subject' => $this->replacePlaceholders($lead, $emailTemplate->subject),
                            'body'    => $this->replacePlaceholders($lead, $emailTemplate->content),
                        ]));
                    } catch (\Exception $e) {}

                    break;

                case 'send_email_to_sales_owner':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        Mail::queue(new Common([
                            'to'      => $lead->user->email,
                            'subject' => $this->replacePlaceholders($lead, $emailTemplate->subject),
                            'body'    => $this->replacePlaceholders($lead, $emailTemplate->content),
                        ]));
                    } catch (\Exception $e) {}

                    break;

                case 'add_tag':
                    $colors = [
                        '#337CFF',
                        '#FEBF00',
                        '#E5549F',
                        '#27B6BB',
                        '#FB8A3F',
                        '#43AF52'
                    ];

                    if (! $tag = $this->tagRepository->findOneByField('name', $action['value'])) {
                        $tag = $this->tagRepository->create([
                            'name'    => $action['value'],
                            'color'   => $colors[rand(0, 5)],
                            'user_id' => auth()->guard('user')->user()->id,
                        ]);
                    }

                    if (! $lead->tags->contains($tag->id)) {
                        $lead->tags()->attach($tag->id);
                    }

                    break;

                case 'add_note_as_activity':
                    $activity = $this->activityRepository->create([
                        'type'    => 'note',
                        'comment' => $action['value'],
                        'is_done' => 1,
                        'user_id' => auth()->guard('user')->user()->id,
                    ]);

                    $lead->activities()->attach($activity->id);

                    break;

                case 'trigger_webhook':
                    if (isset($action['hook'])) {
                        try {
                            $this->triggerWebhook(
                                $action['hook'],
                                $lead
                            );
                        } catch (\Exception $e) {
                            report($e);
                        }
                    }

                    break;
            }
        }
    }

    /**
     * Trigger webhook.
     * 
     * @param array $hook
     * @param \Webkul\Lead\Contracts\Lead $lead
     * @return void
     */
    private function triggerWebhook($hook, $lead)
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
                $this->getRequestBody($hook, $lead)
            );
        }
    }

    /**
     * Format headers.
     * 
     * @param array $hook
     * @return array
     */
    private function formatHeaders($hook)
    {
        $results = $hook['encoding'] == 'json'
            ? ['Content-Type: application/json']
            : ['Content-Type: application/x-www-form-urlencoded'];

        if (isset($hook['headers'])) {
            foreach ($hook['headers'] as $header) {
                $results[$header['key']] = $header['value'];
            }
        }

        return $results;
    }

    /**
     * Prepare request body.
     * 
     * @param array $hook
     * @param \Webkul\Lead\Contracts\Lead $lead
     * @return array
     */
    private function getRequestBody($hook, $lead)
    {
        if (
            ! isset($hook['simple']) &&
            ! isset($hook['custom'])
        ) {
            return;
        }

        $leadResult = $personResult = $quoteResult = $activityResult = $customResults = [];

        if (isset($hook['simple'])) {
            $simpleFormatted = [];
            
            foreach ($hook['simple'] as $field) {
                if (strpos($field, 'lead_') === 0) {
                    $simpleFormatted['lead'][] = substr($field, 5);
                } else if (strpos($field, 'person_') === 0) {
                    $simpleFormatted['person'][] = substr($field, 7);
                } else if (strpos($field, 'quote_') === 0) {
                    $simpleFormatted['quote'][] = substr($field, 6);
                } else if (strpos($field, 'activity_') === 0) {
                    $simpleFormatted['activity'][] = substr($field, 9);
                }
            }

            foreach ($simpleFormatted as $entity => $fields) {
                if ($entity == 'lead') {
                    $leadResult = $this->leadRepository
                        ->find($lead->id)
                        ->get($fields)
                        ->first()
                        ->toArray();
                } else if ($entity == 'person') {
                    $personResult = $this->personRepository
                        ->find($lead->person_id)
                        ->get($fields)
                        ->first()
                        ->toArray();
                } else if ($entity == 'quote') {
                    $quoteResult = $lead
                        ->quotes()
                        ->where(
                            'lead_id',
                            $lead->id
                        )
                        ->get($fields)
                        ->toArray();
                } else if ($entity == 'activity') {
                    $activityResult = $lead
                        ->activities()
                        ->where(
                            'lead_id',
                            $lead->id
                        )
                        ->get($fields)
                        ->toArray();
                }
            }
        }

        if (isset($hook['custom'])) {
            $customUnformatted = preg_split("/[\r\n,]+/", $hook['custom']);

            $customResults = [];

            foreach ($customUnformatted as $raw) {
                [$key, $value] = explode('=', $raw);

                $customResults[$key] = $value;
            }
        }

        $results = array_merge(
            $leadResult,
            $personResult,
            $quoteResult ? ['quotes' => $quoteResult] : [],
            $activityResult ? ['activities' => $activityResult] : [],
            $customResults
        );

        return $hook['encoding'] == 'http_query'
            ? Arr::query($results)
            : json_encode($results);
    }
}
