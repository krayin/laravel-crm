<?php

namespace Webkul\WebForm\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\SourceRepository;
use Webkul\Lead\Repositories\TypeRepository;
use Webkul\WebForm\Http\Requests\WebForm;
use Webkul\WebForm\Repositories\WebFormRepository;

class WebFormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected WebFormRepository $webFormRepository,
        protected PersonRepository $personRepository,
        protected LeadRepository $leadRepository,
        protected PipelineRepository $pipelineRepository,
        protected SourceRepository $sourceRepository,
        protected TypeRepository $typeRepository,
    ) {}

    /**
     * Remove the specified email template from storage.
     */
    public function formJS(string $formId): Response
    {
        $webForm = $this->webFormRepository->findOneByField('form_id', $formId);

        return response()->view('web_form::settings.web-forms.embed', compact('webForm'))
            ->header('Content-Type', 'text/javascript');
    }

    /**
     * The attributes the admin placed on this form, keyed by entity type, as the codes a submission
     * is allowed to write.
     *
     * The codes in `$restrictedWebFormAttributeCodes` can never be exposed on a form, so they are
     * already absent from this relation; the controller sets those values itself.
     */
    protected function exposedAttributes($webForm): array
    {
        $exposed = ['leads' => [], 'persons' => []];

        foreach ($webForm->attributes as $webFormAttribute) {
            $attribute = $webFormAttribute->attribute;

            if (! $attribute) {
                continue;
            }

            if (! array_key_exists($attribute->entity_type, $exposed)) {
                continue;
            }

            $exposed[$attribute->entity_type][] = $attribute->code;
        }

        return $exposed;
    }

    /**
     * Reduce the `leads` and `persons` payloads to the codes this form exposes.
     *
     * The form request derives its rules from the request keys, so trimming the request here also
     * stops an unexposed attribute from pulling in a validation rule of its own — including the
     * uniqueness check that otherwise answers whether a given email or phone is already a contact.
     */
    protected function restrictSubmissionToExposedAttributes(array $exposedAttributes): void
    {
        foreach ($exposedAttributes as $entityType => $codes) {
            $submitted = request($entityType);

            if (! is_array($submitted)) {
                continue;
            }

            request()->request->set($entityType, Arr::only($submitted, $codes));
        }
    }

    /**
     * Resolve the contact a submission belongs to by its email address.
     *
     * The address is matched exactly. A `LIKE '%...%'` match let a submitter hand over a bare SQL
     * wildcard (or any substring) and be merged into an unrelated contact's record, and made the
     * endpoint an oracle for which addresses exist.
     */
    protected function findPersonBySubmittedEmail()
    {
        $email = request('persons.emails.0.value');

        if (
            ! is_string($email)
            || ! filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            return null;
        }

        /**
         * Matched without regard to case: an address is the same address however it is typed, and
         * `whereJsonContains` compares the stored json byte for byte. A submission differing only
         * in case therefore found nobody and went on to create a second contact for the same
         * person — which the unique id built from that address then rejected outright.
         *
         * The comparison is narrowed by the json search first so the lower-casing is applied to
         * the candidates rather than to every contact in the table.
         */
        $needle = mb_strtolower($email);

        /**
         * The candidates are narrowed in the database and then compared in PHP.
         *
         * The comparison is not left to SQL because the obvious way to write it, `JSON_SEARCH`,
         * takes its argument as a LIKE pattern: an address containing `%` would match every
         * contact sharing the rest of it, letting an unauthenticated caller merge their submission
         * into someone else's record and learn that the record exists. Comparing the decoded
         * values here keeps the match exact, whatever the address contains.
         */
        return $this->personRepository
            ->getModel()
            ->whereRaw('LOWER(CAST(emails AS CHAR)) LIKE ?', ['%'.addcslashes($needle, '%_\\').'%'])
            ->get()
            ->first(fn ($person) => collect($person->emails)->contains(
                fn ($personEmail) => mb_strtolower($personEmail['value'] ?? '') === $needle
            ));
    }

    /**
     * Remove the specified email template from storage.
     */
    public function formStore(int $id): JsonResponse
    {
        $webForm = $this->webFormRepository->findOrFail($id);

        /**
         * A submission may only write the attributes the admin actually placed on this form. The
         * request body is attacker-controlled on this public route, so anything outside that
         * allow-list — `user_id`, `status`, `lost_reason`, `closed_at`, a `persons[id]` pointing at
         * someone else's contact, or an unexposed custom attribute — is dropped before it reaches
         * the repositories.
         */
        $exposedAttributes = $this->exposedAttributes($webForm);

        $this->restrictSubmissionToExposedAttributes($exposedAttributes);

        $person = $this->findPersonBySubmittedEmail();

        if ($person) {
            request()->request->add(['persons' => array_merge(request('persons') ?? [], ['id' => $person->id])]);
        }

        app(WebForm::class);

        if ($webForm->create_lead) {
            request()->request->add(['entity_type' => 'leads']);

            Event::dispatch('lead.create.before');

            $data = request('leads');

            $data['entity_type'] = 'leads';

            $data['person'] = request('persons');

            $data['status'] = 1;

            /**
             * The pipeline is configured on the web form by the admin, so the default pipeline is
             * only used when the web form does not point to one.
             */
            $pipeline = $webForm->lead_pipeline_id
                ? $this->pipelineRepository->find($webForm->lead_pipeline_id)
                : null;

            if (! $pipeline) {
                $pipeline = $this->pipelineRepository->getDefaultPipeline();
            }

            $stage = $pipeline->stages()->first();

            $data['lead_pipeline_id'] = $pipeline->id;

            $data['lead_pipeline_stage_id'] = $stage->id;

            $data['title'] = request('leads.title') ?: 'Lead From Web Form';

            $data['lead_value'] = request('leads.lead_value') ?: 0;

            if (! request('leads.lead_source_id')) {
                $source = $this->sourceRepository->findOneByField('name', 'Web Form');

                if (! $source) {
                    $source = $this->sourceRepository->first();
                }

                $data['lead_source_id'] = $source->id;
            }

            $data['lead_type_id'] = request('leads.lead_type_id') ?: $this->typeRepository->first()->id;

            $lead = $this->leadRepository->create($data);

            Event::dispatch('lead.create.after', $lead);
        } else {
            if (! $person) {
                Event::dispatch('contacts.person.create.before');

                $data = request('persons');

                request()->request->add(['entity_type' => 'persons']);

                $data['entity_type'] = 'persons';

                $person = $this->personRepository->create($data);

                Event::dispatch('contacts.person.create.after', $person);
            }
        }

        if ($webForm->submit_success_action == 'message') {
            return response()->json([
                'message' => $webForm->submit_success_content,
            ], 200);
        } else {
            return response()->json([
                'redirect' => $webForm->submit_success_content,
            ], 301);
        }
    }

    /**
     * Remove the specified email template from storage.
     */
    public function preview(string $id): View
    {
        $webForm = $this->webFormRepository->findOneByField('form_id', $id);

        if (is_null($webForm)) {
            abort(404);
        }

        return view('web_form::settings.web-forms.preview', compact('webForm'));
    }

    /**
     * Preview the web form from datagrid.
     */
    public function view(int $id): View
    {
        $webForm = $this->webFormRepository->findOneByField('id', $id);

        request()->merge(['id' => $webForm->form_id]);

        if (is_null($webForm)) {
            abort(404);
        }

        return view('web_form::settings.web-forms.preview', compact('webForm'));
    }

    /**
     * Find the person having exactly the given email, ignoring the case.
     *
     * The query only narrows down the candidates, the exact comparison is done on the decoded
     * emails so a part of an address (or a domain) never matches somebody else's contact.
     */
    protected function findPersonByEmail(string $email)
    {
        $email = mb_strtolower(trim($email));

        return $this->personRepository
            ->getModel()
            ->whereRaw('LOWER(emails) LIKE ?', ['%'.addcslashes($email, '\\%_').'%'])
            ->get()
            ->first(fn ($person) => collect($person->emails)->contains(
                fn ($personEmail) => mb_strtolower(trim($personEmail['value'] ?? '')) === $email
            ));
    }
}
