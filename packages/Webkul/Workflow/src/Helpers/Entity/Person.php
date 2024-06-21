<?php

namespace Webkul\Workflow\Helpers\Entity;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Webkul\Admin\Notifications\Common;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Contact\Repositories\PersonRepository;

class Person extends AbstractEntity
{
    /**
     * Define the entity type.
     * 
     * @var string $entityType
     */
    protected $entityType = 'persons';

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected EmailTemplateRepository $emailTemplateRepository,
        protected LeadRepository $leadRepository,
        protected PersonRepository $personRepository
    ) {
    }

    /**
     * Listing of the entities.
     * 
     * @param  \Webkul\Contact\Contracts\Person  $entity
     * @return \Webkul\Contact\Contracts\Person
     */
    public function getEntity($entity)
    {
        if (! $entity instanceof \Webkul\Contact\Contracts\Person) {
            $entity = $this->personRepository->find($entity);
        }

        return $entity;
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
                'id'         => 'update_person',
                'name'       => __('admin::app.settings.workflows.update-person'),
                'attributes' => $this->getAttributes('persons'),
            ], [
                'id'         => 'update_related_leads',
                'name'       => __('admin::app.settings.workflows.update-related-leads'),
                'attributes' => $this->getAttributes('leads'),
            ], [
                'id'      => 'send_email_to_person',
                'name'    => __('admin::app.settings.workflows.send-email-to-person'),
                'options' => $emailTemplates,
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
                ],
            ],
        ];
    }

    /**
     * Execute workflow actions.
     * 
     * @param  \Webkul\Workflow\Contracts\Workflow  $workflow
     * @param  \Webkul\Contact\Contracts\Person  $person
     * @return array
     */
    public function executeActions($workflow, $person)
    {
        foreach ($workflow->actions as $action) {
            switch ($action['id']) {
                case 'update_person':
                    $this->personRepository->update([
                        'entity_type'        => 'persons',
                        $action['attribute'] => $action['value'],
                    ], $person->id);

                    break;

                case 'update_related_leads':
                    $leads = $this->leadRepository->findByField('person_id', $person->id);

                    foreach ($leads as $lead) {
                        $this->leadRepository->update([
                            'entity_type'        => 'leads',
                            $action['attribute'] => $action['value'],
                        ], $lead->id);
                    }

                    break;

                case 'send_email_to_person':
                    $emailTemplate = $this->emailTemplateRepository->find($action['value']);

                    if (! $emailTemplate) {
                        break;
                    }

                    try {
                        Mail::queue(new Common([
                            'to'      => data_get($person->emails, '*.value'),
                            'subject' => $this->replacePlaceholders($person, $emailTemplate->subject),
                            'body'    => $this->replacePlaceholders($person, $emailTemplate->content),
                        ]));
                    } catch (\Exception $e) {}

                    break;

                case 'trigger_webhook':
                    if (isset($action['hook'])) {
                        try {
                            $this->triggerWebhook(
                                $action['hook'],
                                $person
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
     * @param \Webkul\Contact\Contracts\Person $person
     * @return void
     */
    private function triggerWebhook($hook, $person)
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
                $this->getRequestBody($hook, $person)
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
     * @param \Webkul\Contact\Contracts\Person $person
     * @return array
     */
    private function getRequestBody($hook, $person)
    {
        $hook['simple'] = str_replace('person_', '', $hook['simple']);

        $results = $this->personRepository->find($person->id)->get($hook['simple'])->first()->toArray();

        if (isset($hook['custom'])) {
            $customUnformatted = preg_split("/[\r\n,]+/", $hook['custom']);

            $customResults = [];

            foreach ($customUnformatted as $raw) {
                [$key, $value] = explode('=', $raw);

                $customResults[$key] = $value;
            }

            $results = array_merge(
                $results,
                $customResults
            );
        }

        return $hook['encoding'] == 'http_query'
            ? Arr::query($results)
            : json_encode($results);
    }
}
