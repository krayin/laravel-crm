<?php

namespace Webkul\Automation\Helpers\Entity;

use Illuminate\Support\Facades\Mail;
use Webkul\Admin\Notifications\Common;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Automation\Repositories\WebhookRepository;
use Webkul\Automation\Services\WebhookService;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Quote\Contracts\Quote as ContractsQuote;
use Webkul\Quote\Repositories\QuoteRepository;

class Quote extends AbstractEntity
{
    /**
     * Define the entity type.
     */
    protected string $entityType = 'quotes';

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected EmailTemplateRepository $emailTemplateRepository,
        protected QuoteRepository $quoteRepository,
        protected LeadRepository $leadRepository,
        protected PersonRepository $personRepository,
        protected WebhookRepository $webhookRepository,
        protected WebhookService $webhookService
    ) {}

    /**
     * Listing of the entities.
     */
    public function getEntity(mixed $entity): mixed
    {
        if (! $entity instanceof ContractsQuote) {
            $entity = $this->quoteRepository->find($entity);
        }

        return $entity;
    }

    /**
     * Returns workflow actions.
     */
    public function getActions(): array
    {
        $emailTemplates = $this->emailTemplateRepository->all(['id', 'name']);

        $webhookOptions = $this->webhookRepository->all(['id', 'name']);

        return [
            [
                'id'         => 'update_quote',
                'name'       => trans('admin::app.settings.workflows.helpers.update-quote'),
                'attributes' => $this->getAttributes('quotes'),
            ], [
                'id'         => 'update_person',
                'name'       => trans('admin::app.settings.workflows.helpers.update-person'),
                'attributes' => $this->getAttributes('persons'),
            ], [
                'id'         => 'update_related_leads',
                'name'       => trans('admin::app.settings.workflows.helpers.update-related-leads'),
                'attributes' => $this->getAttributes('leads'),
            ], [
                'id'      => 'send_email_to_person',
                'name'    => trans('admin::app.settings.workflows.helpers.send-email-to-person'),
                'options' => $emailTemplates,
            ], [
                'id'      => 'send_email_to_sales_owner',
                'name'    => trans('admin::app.settings.workflows.helpers.send-email-to-sales-owner'),
                'options' => $emailTemplates,
            ], [
                'id'      => 'trigger_webhook',
                'name'    => trans('admin::app.settings.workflows.helpers.add-webhook'),
                'options' => $webhookOptions,
            ],
        ];
    }

    /**
     * Execute workflow actions.
     */
    public function executeActions(mixed $workflow, mixed $quote): void
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
                        $this->leadRepository->update(
                            [
                                'entity_type'        => 'leads',
                                $action['attribute'] => $action['value'],
                            ],
                            $lead->id,
                            [$action['attribute']]
                        );
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
                    } catch (\Exception $e) {
                    }

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
                    } catch (\Exception $e) {
                    }

                    break;

                case 'trigger_webhook':
                    try {
                        $this->triggerWebhook($action['value'], $quote);
                    } catch (\Exception $e) {
                        report($e);
                    }

                    break;
            }
        }
    }
}
