<?php

namespace Webkul\Workflow\Helpers\Entity;

use Illuminate\Support\Facades\Mail;
use Webkul\Admin\Notifications\Common;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Contact\Repositories\PersonRepository;

class Person extends AbstractEntity
{
    /**
     * @var string  $code
     */
    protected $entityType = 'persons';

    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * EmailTemplateRepository object
     *
     * @var \Webkul\EmailTemplate\Repositories\EmailTemplateRepository
     */
    protected $emailTemplateRepository;

    /**
     * LeadRepository object
     *
     * @var \Webkul\Lead\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * PersonRepository object
     *
     * @var \Webkul\Contact\Repositories\PersonRepository
     */
    protected $personRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeRepository  $attributeRepository
     * @param  \Webkul\EmailTemplate\Repositories\EmailTemplateRepository  $emailTemplateRepository
     * @param  \Webkul\Lead\Repositories\LeadRepository  $leadRepository
     * @param \Webkul\Contact\Repositories\PersonRepository  $personRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        EmailTemplateRepository $emailTemplateRepository,
        LeadRepository $leadRepository,
        PersonRepository $personRepository
    )
    {
        $this->attributeRepository = $attributeRepository;

        $this->emailTemplateRepository = $emailTemplateRepository;

        $this->leadRepository = $leadRepository;

        $this->personRepository = $personRepository;
    }

    /**
     * Returns entity
     * 
     * @param  \Webkul\Contact\Contracts\Person|integer  $entity
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
     * Returns workflow actions
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
            ],
        ];
    }

    /**
     * Execute workflow actions
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
                    if (in_array($action['hook']['method'], ['get', 'delete'])) {
                        Http::withHeaders(
                            $this->formatHeaders($action['hook']['headers'])
                        )->{$action['hook']['method']}(
                            $action['hook']['url']
                        );
                    } else {
                        Http::withHeaders(
                            $this->formatHeaders($action['hook']['headers'])
                        )->{$action['hook']['method']}(
                            $action['hook']['url'],
                            $this->getRequestBody($action['hook'], $person)
                        );
                    }

                    break;
            }
        }
    }

    /**
     * format headers
     * 
     * @param  $headers
     * @return array
     */
    private function formatHeaders($headers)
    {
        array_walk($headers, function (&$arr, $key) use (&$results) {
            $results[$arr['key']] = $arr['value'];
        });

        return $results;
    }

    /**
     * format request body
     * 
     * @param  $hook
     * @param  $lead
     * @return array
     */
    private function getRequestBody($hook, $person)
    {
        $hook['simple'] = str_replace('person_', '', $hook['simple']);

        $person_result = $this->personRepository->find($person->id)->get($fields)->first()->toArray();

        if ($hook['custom']) {
            $custom_unformatted = preg_split("/[\r\n,]+/", $hook['custom']);

            array_walk($custom_unformatted, function (&$raw, $key) use (&$custom_results) {
                $arr = explode('=', $raw);

                $custom_results[$arr[0]] = $arr[1];
            });
        }

        $results = array_merge(
            $person_result,
            $custom_results
        );

        if ($hook['encoding'] == 'json') {
            return json_encode($results);
        } else if ($hook['encoding'] == 'http_query') {
            return Arr::query($results);
        }
    }
}