<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Models\Person;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\WebForm\Models\WebForm;
use Webkul\WebForm\Models\WebFormAttribute;

/**
 * A web form submission is attached to an existing person when its email belongs to them. The
 * endpoint is public, so the lookup has to be an exact match: a part of an address (or a bare
 * domain) used to match the first contact containing it, and an empty email matched everybody.
 */
uses(DatabaseTransactions::class);

function makeContactWebForm(): WebForm
{
    $webForm = WebForm::create([
        'form_id' => bin2hex(random_bytes(8)),
        'title' => 'Contact Us',
        'submit_button_label' => 'Submit',
        'submit_success_action' => 'message',
        'submit_success_content' => 'Thanks!',
        'create_lead' => 0,
    ]);

    /**
     * The fields an admin placed on the form. A submission may only write the attributes the form
     * exposes, and the rules are built from them, so a form carrying none accepted anything and
     * stored nothing — the parallel test databases are migrated but not seeded, so the attributes
     * themselves are created here too.
     */
    foreach (['name', 'emails'] as $sortOrder => $code) {
        WebFormAttribute::create([
            'name' => $code,
            'is_required' => 0,
            'is_hidden' => 0,
            'sort_order' => $sortOrder + 1,
            'attribute_id' => webFormAttributeFor('persons', $code)->id,
            'web_form_id' => $webForm->id,
        ]);
    }

    return $webForm;
}

function makeWebFormPerson(string $email): Person
{
    /**
     * Created through the repository rather than the model: the lookup searches the attribute
     * values, and only the repository writes those, so a person made with `Person::create` carried
     * no email to be matched against.
     */
    foreach (['name', 'emails'] as $code) {
        webFormAttributeFor('persons', $code);
    }

    return app(PersonRepository::class)->create([
        'name' => 'Existing Contact',
        'emails' => [['value' => $email, 'label' => 'work']],
        'entity_type' => 'persons',
    ]);
}

/**
 * Named for the payload it carries: a plain `submitWebForm` collided with the helper of the same
 * name in the security suite, and Pest shares one function namespace across every test file.
 */
function submitWebFormPersons(WebForm $webForm, array $persons)
{
    return test()->postJson(route('admin.settings.web_forms.form_store', $webForm->id), [
        'persons' => $persons,
    ]);
}

function personsWithEmail(string $email): int
{
    return Person::all()->filter(
        fn ($person) => collect($person->emails)->contains(fn ($personEmail) => strcasecmp($personEmail['value'], $email) === 0)
    )->count();
}

it('does not attach a submission to a contact whose email only contains the submitted one', function () {
    $suffix = bin2hex(random_bytes(4));

    makeWebFormPerson("mariana-{$suffix}@mail.invalid");

    submitWebFormPersons(makeContactWebForm(), [
        'name' => 'Ana',
        'emails' => [['value' => "ana-{$suffix}@mail.invalid", 'label' => 'work']],
    ])->assertOk();

    expect(personsWithEmail("ana-{$suffix}@mail.invalid"))->toBe(1);
});

it('reuses the existing contact on an exact match, ignoring the case', function () {
    $email = 'exact-'.bin2hex(random_bytes(4)).'@mail.invalid';

    makeWebFormPerson($email);

    submitWebFormPersons(makeContactWebForm(), [
        'name' => 'Exact Match',
        'emails' => [['value' => strtoupper($email), 'label' => 'work']],
    ])->assertOk();

    expect(personsWithEmail($email))->toBe(1);
});

it('answers a validation error instead of a server error on an empty submission', function () {
    test()->postJson(route('admin.settings.web_forms.form_store', makeContactWebForm()->id), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['persons', 'persons.emails.0.value']);
});

it('answers a validation error when the email is not an email address', function () {
    submitWebFormPersons(makeContactWebForm(), [
        'name' => 'Domain Only',
        'emails' => [['value' => '@gmail.com', 'label' => 'work']],
    ])->assertStatus(422)->assertJsonValidationErrors(['persons.emails.0.value']);
});
