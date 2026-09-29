<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Traits\Sanitizer;

/**
 * Stored XSS via the SVG sanitizer being skipped for a mis-named upload.
 *
 * The TinyMCE media upload derives the *stored* extension from the file's own content
 * (`UploadedFile::extension()`), so SVG bytes named `payload.png` are stored — and later served —
 * as an SVG. A sanitizer that decides what to clean from the client-supplied name therefore never
 * runs on exactly the file that needs it.
 */
const MALICIOUS_SVG = <<<'SVG'
<?xml version="1.0"?>
<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
  <script>alert(document.domain)</script>
  <a xlink:href="javascript:alert(1)"><text x="0" y="20">click</text></a>
  <rect width="100" height="100" onload="alert(1)" />
</svg>
SVG;

beforeEach(function () {
    Storage::fake();

    $this->sanitizer = new class
    {
        use Sanitizer;
    };
});

/**
 * Store the payload the way the upload handlers do, then sanitize it.
 */
function storeThenSanitize(object $sanitizer, string $storedPath, string $clientName): string
{
    Storage::put($storedPath, MALICIOUS_SVG);

    $sanitizer->sanitizeSvg(
        $storedPath,
        UploadedFile::fake()->createWithContent($clientName, MALICIOUS_SVG)
    );

    return (string) Storage::get($storedPath);
}

it('sanitizes an svg uploaded under a non-svg name', function () {
    $result = storeThenSanitize($this->sanitizer, 'tinymce/abc123.svg', 'payload.png');

    expect($result)->not->toContain('<script')
        ->and($result)->not->toContain('onload')
        ->and($result)->not->toContain('javascript:');
});

it('still sanitizes an svg named honestly', function () {
    $result = storeThenSanitize($this->sanitizer, 'tinymce/abc123.svg', 'logo.svg');

    expect($result)->not->toContain('<script')
        ->and($result)->not->toContain('onload');
});

it('sanitizes svg markup even when neither the name nor the stored path says svg', function () {
    $result = storeThenSanitize($this->sanitizer, 'configuration/abc123.png', 'payload.png');

    expect($result)->not->toContain('<script')
        ->and($result)->not->toContain('onload');
});

it('detects svg by stored path and by markup, not only by client name', function () {
    expect($this->sanitizer->isSvgPath('tinymce/abc.svg'))->toBeTrue()
        ->and($this->sanitizer->isSvgPath('tinymce/abc.png'))->toBeFalse()
        ->and($this->sanitizer->hasSvgMarkup(MALICIOUS_SVG))->toBeTrue()
        ->and($this->sanitizer->hasSvgMarkup('just plain text'))->toBeFalse();
});

it('leaves a genuine non-svg upload untouched', function () {
    $png = "\x89PNG\r\n\x1a\n".str_repeat("\x00", 32);

    Storage::put('tinymce/real.png', $png);

    $this->sanitizer->sanitizeSvg(
        'tinymce/real.png',
        UploadedFile::fake()->createWithContent('real.png', $png)
    );

    expect(Storage::get('tinymce/real.png'))->toBe($png);
});
