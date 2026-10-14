<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Contact\Models\Person;
use Webkul\WebForm\Models\WebForm;

/**
 * A web form submission is attached to an existing person when its email belongs to them. The
 * endpoint is public, so the lookup has to be an exact match: a part of an address (or a bare
 * domain) used to match the first contact containing it, and an empty email matched everybody.
 */
uses(DatabaseTransactions::class);

function makeContactWebForm(): WebForm
{
    return WebForm::create([
        'form_id' => bin2hex(random_bytes(8)),
        'title' => 'Contact Us',
        'submit_button_label' => 'Submit',
        'submit_success_action' => 'message',
        'submit_success_content' => 'Thanks!',
        'create_lead' => 0,
    ]);
}

function makeWebFormPerson(string $email): Person
{
    return Person::create([
        'name' => 'Existing Contact',
        'emails' => [['value' => $email, 'label' => 'work']],
    ]);
}

function submitWebForm(WebForm $webForm, array $persons)
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

    submitWebForm(makeContactWebForm(), [
        'name' => 'Ana',
        'emails' => [['value' => "ana-{$suffix}@mail.invalid", 'label' => 'work']],
    ])->assertOk();

    expect(personsWithEmail("ana-{$suffix}@mail.invalid"))->toBe(1);
});

it('reuses the existing contact on an exact match, ignoring the case', function () {
    $email = 'exact-'.bin2hex(random_bytes(4)).'@mail.invalid';

    makeWebFormPerson($email);

    submitWebForm(makeContactWebForm(), [
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
    submitWebForm(makeContactWebForm(), [
        'name' => 'Domain Only',
        'emails' => [['value' => '@gmail.com', 'label' => 'work']],
    ])->assertStatus(422)->assertJsonValidationErrors(['persons.emails.0.value']);
});
