<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Contact\Models\Person;
use Webkul\Quote\Models\Quote;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

uses(DatabaseTransactions::class);

/**
 * Dompdf has no bidi or Arabic shaping support, so a quote whose customer or products are written
 * in Arabic, Persian or Hebrew printed reversed, unjoined text whenever the interface language was
 * not one of those locales. The engine has to follow the content, not only the interface locale.
 */
function quotePdfActor(): User
{
    $role = Role::create([
        'name' => 'quote-pdf-'.uniqid(),
        'description' => 'test',
        'permission_type' => 'all',
    ]);

    return User::create([
        'name' => 'Quote PDF Actor',
        'email' => uniqid().'@example.com',
        'password' => bcrypt('password'),
        'status' => 1,
        'view_permission' => 'global',
        'role_id' => $role->id,
    ]);
}

function quotePdfFor(User $user, string $personName, string $subject): Quote
{
    $person = Person::create([
        'name' => $personName,
        'emails' => [['value' => uniqid().'@example.com', 'label' => 'work']],
        'user_id' => $user->id,
    ]);

    return Quote::create([
        'subject' => $subject,
        'user_id' => $user->id,
        'person_id' => $person->id,
        'sub_total' => 100,
        'grand_total' => 100,
    ]);
}

function printedQuotePdf(User $user, Quote $quote): string
{
    $response = test()->actingAs($user, 'user')->get(route('admin.quotes.print', $quote->id));

    $response->assertOk();

    return $response->baseResponse instanceof StreamedResponse
        ? $response->streamedContent()
        : $response->getContent();
}

/**
 * The engine that rendered the PDF, read from its document info. Both engines write the
 * Producer entry as a UTF-16 string, so it is decoded before comparing.
 */
function pdfProducer(string $pdf): string
{
    preg_match('#/Producer\s*\((.*?)\)\s*[/>]#s', $pdf, $match);

    $producer = $match[1] ?? '';

    if (str_starts_with($producer, "\xFE\xFF")) {
        $producer = mb_convert_encoding(substr($producer, 2), 'UTF-8', 'UTF-16BE');
    }

    return $producer;
}

it('prints a quote with an Arabic customer through mPDF when the interface is English', function () {
    app()->setLocale('en');

    $user = quotePdfActor();

    $quote = quotePdfFor($user, 'أحمد سالم', 'Network installation');

    $pdf = printedQuotePdf($user, $quote);

    expect($pdf)->toStartWith('%PDF')
        ->and(pdfProducer($pdf))->toStartWith('mPDF');
});

it('prints a quote with a Hebrew product line through mPDF when the interface is English', function () {
    app()->setLocale('en');

    $user = quotePdfActor();

    $quote = quotePdfFor($user, 'Dana Levi', 'התקנת רשת');

    expect(pdfProducer(printedQuotePdf($user, $quote)))->toStartWith('mPDF');
});

it('keeps printing Latin-only quotes through Dompdf', function () {
    app()->setLocale('en');

    $user = quotePdfActor();

    $quote = quotePdfFor($user, 'John Smith', 'Network installation');

    $pdf = printedQuotePdf($user, $quote);

    expect($pdf)->toStartWith('%PDF')
        ->and(pdfProducer($pdf))->toStartWith('dompdf');
});
