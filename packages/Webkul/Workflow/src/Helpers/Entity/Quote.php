<?php

namespace Webkul\Workflow\Helpers\Entity;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Webkul\Admin\Notifications\Common;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Quote\Repositories\QuoteRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Contact\Repositories\PersonRepository;

class Quote extends AbstractEntity
{
    /**
     * @var string  $code
     */
    protected $entityType = 'quotes';

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
     * QuoteRepository object
     *
     * @var \Webkul\Quote\Repositories\QuoteRepository
     */
    protected $quoteRepository;

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
     * @param  \Webkul\Quote\Repositories\QuoteRepository  $quoteRepository
     * @param \Webkul\Contact\Repositories\PersonRepository  $personRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        EmailTemplateRepository $emailTemplateRepository,
        QuoteRepository $quoteRepository,
        LeadRepository $leadRepository,
        PersonRepository $personRepository
    )
    {
        $this->attributeRepository = $attributeRepository;

        $this->emailTemplateRepository = $emailTemplateRepository;

        $this->quoteRepository = $quoteRepository;

        $this->leadRepository = $leadRepository;

        $this->personRepository = $personRepository;
    }

    /**
     * Returns entity
     * 
     * @param  \Webkul\Quote\Contracts\Quote|integer  $entity
     * @return \Webkul\Quote\Contracts\Quote
     */
    public function getEntity($entity)
    {
        if (! $entity instanceof \Webkul\Quote\Contracts\Quote) {
            $entity = $this->quoteRepository->find($entity);
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
                'id'         => 'update_quote',
                'name'       => __('admin::app.settings.workflows.update-quote'),
                'attributes' => $this->getAttributes('quotes'),
            ], [
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
                'id'      => 'send_email_to_sales_owner',
                'name'    => __('admin::app.settings.workflows.send-email-to-sales-owner'),
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
     * @param  \Webkul\Quote\Contracts\Quote  $quote
     * @return array
     */
    public function executeActions($workflow, $quote)
    {
        foreach ($workflow->actions as $action) {
            switch ($action['id']) {
                case 'update_quote':
                    $this->quoteRepository->update([
                        'entity_type'        => 'quotes',
                        $action['attribute'] => $action['value'],
                    ], $quote->id);

                    break;
                
                case 'update_person':
                    $this->personRepository->update([
                        'entity_type'        => 'persons',
                        $action['attribute'] => $action['value'],
                    ], $quote->person_id);

                    break;
                
                case 'update_related_leads':
                    foreach ($quote->leads as $lead) {
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
                            'to'      => data_get($quote->person->emails, '*.value'),
                            'subject' => $this->replacePlaceholders($quote, $emailTemplate->subject),
                            'body'    => $this->replacePlaceholders($quote, $emailTemplate->content),
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
                            'to'      => $quote->user->email,
                            'subject' => $this->replacePlaceholders($quote, $emailTemplate->subject),
                            'body'    => $this->replacePlaceholders($quote, $emailTemplate->content),
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
                            $this->getRequestBody($action['hook'], $quote)
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
     * @param  $quote
     * @return array
     */
    private function getRequestBody($hook, $quote)
    {
        $hook['simple'] = str_replace('quote_', '', $hook['simple']);

        $results = $this->quoteRepository->find($quote->id)->get($hook['simple'])->first()->toArray();

        if (isset($hook['custom'])) {
            $custom_unformatted = preg_split("/[\r\n,]+/", $hook['custom']);

            array_walk($custom_unformatted, function (&$raw, $key) use (&$custom_results) {
                $arr = explode('=', $raw);

                $custom_results[$arr[0]] = $arr[1];
            });

            $results = array_merge(
                $quote_result,
                $custom_results
            );
        }

        if ($hook['encoding'] == 'json') {
            return json_encode($results);
        } else if ($hook['encoding'] == 'http_query') {
            return Arr::query($results);
        }
    }
}