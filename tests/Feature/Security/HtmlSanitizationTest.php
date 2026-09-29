<?php

use Webkul\Core\Traits\Sanitizer;

/**
 * Stored XSS via the footer configuration (TinyMCE sanitization bypass).
 *
 * The footer label is rendered with `{!! !!}`, so the server-side sanitizer is the only thing
 * standing between a crafted POST to the configuration endpoint and script running in every
 * admin's browser. Client-side TinyMCE filtering is bypassed by posting directly.
 */
beforeEach(function () {
    $this->sanitizer = new class
    {
        use Sanitizer;
    };
});

it('strips the advisory payload from footer html', function () {
    $output = $this->sanitizer->sanitizeHtml('<img src=x onerror=alert(document.domain)>');

    expect($output)->not->toContain('onerror')
        ->and($output)->not->toContain('alert(');
});

it('strips script elements and javascript uris', function (string $payload, string $forbidden) {
    expect($this->sanitizer->sanitizeHtml($payload))->not->toContain($forbidden);
})->with([
    ['<script>alert(1)</script>', '<script'],
    ['<a href="javascript:alert(1)">x</a>', 'javascript:'],
    ['<svg onload=alert(1)></svg>', 'onload'],
    ['<iframe src="//evil.test"></iframe>', '<iframe'],
    ['<body onload=alert(1)>', 'onload'],
]);

it('keeps legitimate formatting and target blank links intact', function () {
    $output = $this->sanitizer->sanitizeHtml(
        '<p>Powered by <a href="https://krayincrm.com" target="_blank">Krayin</a></p>'
    );

    expect($output)->toContain('<p>')
        ->and($output)->toContain('href="https://krayincrm.com"')
        ->and($output)->toContain('target="_blank"')
        ->and($output)->toContain('Krayin');
});

it('returns an empty string for null and blank input', function () {
    expect($this->sanitizer->sanitizeHtml(null))->toBe('')
        ->and($this->sanitizer->sanitizeHtml('   '))->toBe('');
});
