<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Contact\Models\Person;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\Lead\Repositories\PipelineRepository;
use Webkul\Lead\Repositories\SourceRepository;
use Webkul\Lead\Repositories\TypeRepository;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;
use Webkul\WebForm\Http\Controllers\WebFormController;
use Webkul\WebForm\Models\WebForm;
use Webkul\WebForm\Models\WebFormAttribute;
use Webkul\WebForm\Repositories\WebFormRepository;

/**
 * `POST /web-forms/forms/{id}` is unauthenticated: anyone on the internet can submit it. The
 * controller took `request('leads')` and `request('persons')` wholesale, so the submitter — not the
 * admin who built the form — decided which columns were written. That allowed an anonymous caller
 * to set the lead's sales owner (`user_id`), its `lost_reason`/`closed_at` state, and any custom
 * attribute that existed in the database but was never placed on the form; to attach the lead to
 * someone else's contact by supplying `persons[id]`; and, because the contact lookup used an
 * unanchored `LIKE '%...%'` on the emails JSON column, to be merged into an arbitrary contact by
 * submitting a bare SQL wildcard.
 *
 * These tests pin the allow-list: a submission may only write the attributes the form exposes.
 */
uses(DatabaseTransactions::class);

function webFormUser(): User
{
    $role = Role::create([
        'name' => 'Web Form Role '.bin2hex(random_bytes(6)),
        'description' => 'Created by the web form submission tests.',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Web Form Owner',
        'email' => 'web-form-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt('correct-horse-battery-staple'),
        'status' => 1,
        'role_id' => $role->id,
        'view_permission' => 'global',
    ]);
}

/**
 * A form exposing exactly the attribute codes given — the set an admin dragged onto the builder.
 */
function makeWebForm(array $exposedCodes = ['leads.title', 'persons.name', 'persons.emails']): WebForm
{
    /**
     * A submission that creates a lead needs a default pipeline with a stage to put it in, and the
     * parallel databases are migrated but not seeded.
     */
    securityTestPipeline();

    $webForm = WebForm::create([
        'form_id' => bin2hex(random_bytes(8)),
        'title' => 'Contact us',
        'description' => 'Tell us about your project.',
        'submit_button_label' => 'Send',
        'submit_success_action' => 'message',
        'submit_success_content' => 'Thanks.',
        'create_lead' => 1,
    ]);

    foreach ($exposedCodes as $sortOrder => $qualified) {
        [$entityType, $code] = explode('.', $qualified, 2);

        $attribute = webFormAttributeFor($entityType, $code);

        WebFormAttribute::create([
            'name' => $code,
            'is_required' => 0,
            'is_hidden' => 0,
            'sort_order' => $sortOrder + 1,
            'attribute_id' => $attribute->id,
            'web_form_id' => $webForm->id,
        ]);
    }

    return $webForm;
}

function submitWebForm(WebForm $webForm, array $payload)
{
    return test()->post('/web-forms/forms/'.$webForm->id, $payload);
}

it('ignores a lead user_id the form does not expose', function () {
    $owner = webFormUser();

    $webForm = makeWebForm();

    submitWebForm($webForm, [
        'leads' => [
            'title' => 'Anonymous enquiry',
            'user_id' => $owner->id,
        ],
        'persons' => [
            'name' => 'Visitor',
            'emails' => [['value' => 'visitor-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
        ],
    ]);

    $lead = Lead::where('title', 'Anonymous enquiry')->first();

    expect($lead)->not->toBeNull();

    expect($lead->user_id)->not->toBe($owner->id);
});

it('ignores lead columns the form does not expose', function () {
    $webForm = makeWebForm();

    submitWebForm($webForm, [
        'leads' => [
            'title' => 'Closed on arrival',
            'lost_reason' => 'Injected by an anonymous submitter',
        ],
        'persons' => [
            'name' => 'Visitor',
            'emails' => [['value' => 'visitor-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
        ],
    ]);

    $lead = Lead::where('title', 'Closed on arrival')->first();

    expect($lead)->not->toBeNull();

    expect($lead->lost_reason)->toBeNull();
});

it('refuses to attach a submission to a contact chosen by id', function () {
    $owner = webFormUser();

    $victim = Person::create([
        'name' => 'Existing Customer',
        'user_id' => $owner->id,
        'emails' => [['value' => 'customer-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);

    $webForm = makeWebForm();

    submitWebForm($webForm, [
        'leads' => ['title' => 'Hijack attempt'],
        'persons' => [
            'id' => $victim->id,
            'name' => 'Visitor',
            'emails' => [['value' => 'visitor-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
        ],
    ]);

    $lead = Lead::where('title', 'Hijack attempt')->first();

    expect($lead)->not->toBeNull();

    expect($lead->person_id)->not->toBe($victim->id);
});

/**
 * The lookup used `LIKE '%<submitted>%'`, so `%` matched the first contact row in the table.
 */
it('does not merge a submission into a contact matched by a sql wildcard', function () {
    $owner = webFormUser();

    $victim = Person::create([
        'name' => 'Existing Customer',
        'user_id' => $owner->id,
        'emails' => [['value' => 'customer-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);

    $webForm = makeWebForm();

    submitWebForm($webForm, [
        'leads' => ['title' => 'Wildcard attempt'],
        'persons' => [
            'name' => 'Visitor',
            'emails' => [['value' => '%', 'label' => 'work']],
        ],
    ]);

    /**
     * A `LIKE '%%%'` pattern matches whichever contact happens to sort first, not necessarily the
     * one this test created, so the assertion is on the lookup itself: a bare wildcard must resolve
     * to no contact at all. Asserting only against `$victim` would pass for the wrong reason on a
     * database that already holds other contacts.
     */
    $resolved = (new WebFormController(
        app(AttributeRepository::class),
        app(WebFormRepository::class),
        app(PersonRepository::class),
        app(LeadRepository::class),
        app(PipelineRepository::class),
        app(SourceRepository::class),
        app(TypeRepository::class),
    ));

    request()->replace(['persons' => ['emails' => [['value' => '%', 'label' => 'work']]]]);

    $method = new ReflectionMethod(WebFormController::class, 'findPersonBySubmittedEmail');
    $method->setAccessible(true);

    expect($method->invoke($resolved))->toBeNull();

    $lead = Lead::where('title', 'Wildcard attempt')->first();

    expect($lead?->person_id)->not->toBe($victim->id);
});

/**
 * A substring of a real address must not match it either.
 */
it('does not merge a submission into a contact matched by an email substring', function () {
    $owner = webFormUser();

    $suffix = bin2hex(random_bytes(4));

    $victim = Person::create([
        'name' => 'Existing Customer',
        'user_id' => $owner->id,
        'emails' => [['value' => 'customer-'.$suffix.'@example.invalid', 'label' => 'work']],
    ]);

    $webForm = makeWebForm();

    submitWebForm($webForm, [
        'leads' => ['title' => 'Substring attempt'],
        'persons' => [
            'name' => 'Visitor',
            'emails' => [['value' => $suffix.'@example.invalid', 'label' => 'work']],
        ],
    ]);

    $lead = Lead::where('title', 'Substring attempt')->first();

    expect($lead)->not->toBeNull();

    expect($lead->person_id)->not->toBe($victim->id);
});

/**
 * The exact address must still resolve to the existing contact, or every repeat submission would
 * create a duplicate record.
 */
it('still merges a submission into the contact whose email matches exactly', function () {
    $owner = webFormUser();

    $email = 'customer-'.bin2hex(random_bytes(4)).'@example.invalid';

    $existing = Person::create([
        'name' => 'Existing Customer',
        'user_id' => $owner->id,
        'emails' => [['value' => $email, 'label' => 'work']],
    ]);

    $webForm = makeWebForm();

    submitWebForm($webForm, [
        'leads' => ['title' => 'Returning customer'],
        'persons' => [
            'name' => 'Existing Customer',
            'emails' => [['value' => $email, 'label' => 'work']],
        ],
    ]);

    $lead = Lead::where('title', 'Returning customer')->first();

    expect($lead)->not->toBeNull();

    expect($lead->person_id)->toBe($existing->id);
});

it('still writes the attributes the form does expose', function () {
    $webForm = makeWebForm();

    $email = 'visitor-'.bin2hex(random_bytes(4)).'@example.invalid';

    submitWebForm($webForm, [
        'leads' => ['title' => 'Legitimate enquiry'],
        'persons' => [
            'name' => 'Jane Visitor',
            'emails' => [['value' => $email, 'label' => 'work']],
        ],
    ]);

    $lead = Lead::where('title', 'Legitimate enquiry')->first();

    expect($lead)->not->toBeNull();

    expect($lead->person)->not->toBeNull();

    expect($lead->person->name)->toBe('Jane Visitor');
});
