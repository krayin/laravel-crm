<?php

namespace Webkul\Automation\Helpers\Entity;

use Illuminate\Support\Facades\Mail;
use Webkul\Admin\Notifications\Common;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Automation\Contracts\Workflow;
use Webkul\Automation\Repositories\WebhookRepository;
use Webkul\Automation\Services\WebhookService;
use Webkul\Contact\Contracts\Person as PersonContract;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;
use Webkul\Lead\Repositories\LeadRepository;

class Person extends AbstractEntity
{
    /**
     * Define the entity type.
     */
    protected string $entityType = 'persons';

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
        protected WebhookRepository $webhookRepository,
        protected WebhookService $webhookService
    ) {}

    /**
     * Listing of the entities.
     */
    public function getEntity(mixed $entity): mixed
    {
        if (! $entity instanceof PersonContract) {
            $entity = $this->personRepository->find($entity);
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
                'id'      => 'trigger_webhook',
                'name'    => trans('admin::app.settings.workflows.helpers.add-webhook'),
                'options' => $webhooksOptions,
            ],
        ];
    }

    /**
     * Execute workflow actions.
     */
    public function executeActions(mixed $workflow, mixed $person): void
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
                    } catch (\Exception $e) {
                        report($e);
                    }

                    break;

                case 'trigger_webhook':
                    try {
                        $this->triggerWebhook($action['value'], $person);
                    } catch (\Exception $e) {
                        report($e);
                    }

                    break;
            }
        }
    }
}
