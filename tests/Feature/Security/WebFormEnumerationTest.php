<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;
use Webkul\WebForm\Http\Controllers\WebFormController;
use Webkul\WebForm\Http\Requests\WebForm as WebFormRequest;

/**
 * `emails` and `contact_numbers` are seeded `is_unique`, and both are legitimately placed on public
 * web forms. The submission's uniqueness rule therefore answered a question an anonymous caller
 * should not be able to ask: a 422 carrying "The value has already been taken." meant the address
 * or number is already a contact, anything else meant it is not. The route is unauthenticated and
 * unthrottled, so the whole contact list could be probed one value at a time.
 *
 * A repeat value on a public form is a returning visitor rather than an error — the controller
 * resolves them by email and merges — so the check is dropped on this path. The admin forms, which
 * are authenticated and where a duplicate genuinely is a mistake, keep it.
 */
uses(DatabaseTransactions::class);

function enumerationUser(): User
{
    $role = Role::create([
        'name' => 'Enumeration Role '.bin2hex(random_bytes(6)),
        'description' => 'Created by the web form enumeration tests.',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Enumeration Owner',
        'email' => 'enumeration-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt('correct-horse-battery-staple'),
        'status' => 1,
        'role_id' => $role->id,
        'view_permission' => 'global',
    ]);
}

/**
 * Create a contact the way the application does. Creating the model directly leaves
 * `attribute_values` empty, and that table — not `persons` — is what the uniqueness rule queries,
 * so a directly-created contact does not reproduce the oracle at all.
 */
function makeEnumerationContact(User $owner, string $name, string $email, ?string $phone = null)
{
    /**
     * The parallel databases are migrated but not seeded, so the person attributes a contact is
     * saved against have to be in place before one is created.
     */
    foreach (['name', 'emails', 'contact_numbers'] as $code) {
        webFormAttributeFor('persons', $code);
    }

    $data = [
        'name' => $name,
        'user_id' => $owner->id,
        'entity_type' => 'persons',
        'emails' => [['value' => $email, 'label' => 'work']],
    ];

    if ($phone) {
        $data['contact_numbers'] = [['value' => $phone, 'label' => 'work']];
    }

    return app(PersonRepository::class)->create($data);
}

/**
 * Run the public submission's validation over a payload, returning the messages it produced.
 *
 * The request is exercised directly because the controller resolves a matching contact and puts
 * its id in the request before validating, which exempts that one record — the probe below is for
 * every value that does NOT get that exemption, which is where the oracle lived.
 */
function validationErrorsFor(array $payload): array
{
    /**
     * The rules are built from the attributes named in the payload, so those have to exist: the
     * parallel databases are migrated but not seeded, and without the `emails` attribute no email
     * rule is produced and a malformed address passes unchallenged.
     */
    foreach (array_keys($payload['persons'] ?? []) as $code) {
        webFormAttributeFor('persons', $code);
    }

    foreach (array_keys($payload['leads'] ?? []) as $code) {
        webFormAttributeFor('leads', $code);
    }

    request()->replace($payload);

    try {
        app(WebFormRequest::class);
    } catch (ValidationException $e) {
        return $e->errors();
    }

    return [];
}

it('does not report an existing email as already taken', function () {
    $owner = enumerationUser();

    $email = 'existing-'.bin2hex(random_bytes(4)).'@example.invalid';

    makeEnumerationContact($owner, 'Existing Customer', $email);

    $errors = validationErrorsFor([
        'leads' => ['title' => 'Enquiry'],
        'persons' => [
            'name' => 'Visitor',
            'emails' => [['value' => $email, 'label' => 'work']],
        ],
    ]);

    expect($errors)->toBe([]);
});

it('does not report an existing phone number as already taken', function () {
    $owner = enumerationUser();

    $phone = '+1555'.random_int(1000000, 9999999);

    makeEnumerationContact(
        $owner,
        'Existing Customer',
        'phone-owner-'.bin2hex(random_bytes(4)).'@example.invalid',
        $phone,
    );

    $errors = validationErrorsFor([
        'leads' => ['title' => 'Enquiry'],
        'persons' => [
            'name' => 'Visitor',
            'contact_numbers' => [['value' => $phone, 'label' => 'work']],
        ],
    ]);

    expect($errors)->toBe([]);
});

/**
 * The response for a value that exists must be indistinguishable from one that does not — that
 * equivalence is the property the oracle broke.
 */
it('answers identically whether or not the email already exists', function () {
    $owner = enumerationUser();

    $existing = 'existing-'.bin2hex(random_bytes(4)).'@example.invalid';

    makeEnumerationContact($owner, 'Existing Customer', $existing);

    $known = validationErrorsFor([
        'leads' => ['title' => 'Enquiry'],
        'persons' => ['name' => 'Visitor', 'emails' => [['value' => $existing, 'label' => 'work']]],
    ]);

    $absent = validationErrorsFor([
        'leads' => ['title' => 'Enquiry'],
        'persons' => ['name' => 'Visitor', 'emails' => [['value' => 'absent-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']]],
    ]);

    expect($known)->toBe($absent);
});

/**
 * Dropping the uniqueness rule must not drop the rest of the validation with it.
 */
/**
 * The lookup must not treat the submitted address as a pattern.
 *
 * Matching it with `JSON_SEARCH` took it as a LIKE pattern, so an address carrying `%` matched
 * every contact sharing the rest of it: an unauthenticated caller could merge their submission
 * into someone else's record, and learn from the merge that the record exists.
 */
it('does not let a wildcard address match another contact', function () {
    $owner = enumerationUser();

    $email = 'wildcard-'.bin2hex(random_bytes(4)).'@mail.invalid';

    makeEnumerationContact($owner, 'Wildcard Victim', $email);

    $controller = app(WebFormController::class);

    $lookup = new ReflectionMethod($controller, 'findPersonBySubmittedEmail');

    $lookup->setAccessible(true);

    foreach (['%@mail.invalid', '_'.substr($email, 1), '%'] as $pattern) {
        request()->replace(['persons' => ['emails' => [['value' => $pattern]]]]);

        expect($lookup->invoke($controller))->toBeNull();
    }

    /**
     * The address itself still matches however it is typed, which is what the pattern was
     * reached for in the first place.
     */
    request()->replace(['persons' => ['emails' => [['value' => mb_strtoupper($email)]]]]);

    expect($lookup->invoke($controller))->not->toBeNull();
});

it('still rejects a malformed email', function () {
    $errors = validationErrorsFor([
        'leads' => ['title' => 'Enquiry'],
        'persons' => [
            'name' => 'Visitor',
            'emails' => [['value' => 'not-an-email', 'label' => 'work']],
        ],
    ]);

    expect($errors)->toHaveKey('persons.emails.0.value');
});

/**
 * The repository check itself is untouched — it is the public form that stops consulting it, not
 * the uniqueness logic that stops working.
 */
it('leaves the underlying uniqueness check intact for the admin forms', function () {
    $owner = enumerationUser();

    $email = 'admin-side-'.bin2hex(random_bytes(4)).'@example.invalid';

    makeEnumerationContact($owner, 'Existing Customer', $email);

    $attribute = webFormAttributeFor('persons', 'emails');

    expect(app(AttributeValueRepository::class)->isValueUnique(null, 'persons', $attribute, $email))
        ->toBeFalse();
});
