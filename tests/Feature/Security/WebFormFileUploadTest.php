<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeValue;
use Webkul\WebForm\Models\WebForm;
use Webkul\WebForm\Models\WebFormAttribute;

uses(DatabaseTransactions::class);

/**
 * The public lead-capture form accepts files from anonymous visitors.
 *
 * Whatever arrives is stored on a disk the web server hands back verbatim, so a file carrying
 * markup becomes same-origin content that runs when a colleague opens the attachment. The form
 * applied no rule at all to file and image attributes — any type, any size.
 */
/**
 * Point the public disk at a directory of this test's own.
 *
 * `Storage::fake()` hands every caller the same directory and empties it on the way in, so tests
 * sharing a process clear each other's uploads; the suite also runs several processes at once.
 */
function isolatePublicDisk(): void
{
    $root = sys_get_temp_dir().'/krayin-upload-'.bin2hex(random_bytes(8));

    config([
        'filesystems.disks.public.root' => $root,
        'filesystems.disks.public.driver' => 'local',
    ]);

    Storage::forgetDisk('public');
}

function uploadForm(string $attributeType = 'file'): array
{
    /**
     * A submission that creates a lead needs a default pipeline, a source and a type: the parallel
     * test databases are migrated but not seeded, so none of them are there by default.
     */
    securityTestPipeline();

    $upload = Attribute::create([
        'code' => 'wf_upload_'.uniqid(),
        'name' => 'Attachment',
        'type' => $attributeType,
        'entity_type' => 'leads',
        'is_required' => 0,
        'is_unique' => 0,
        'quick_add' => 0,
        'sort_order' => 99,
    ]);

    $exposed = [
        webFormAttributeFor('leads', 'title'),
        $upload,
        webFormAttributeFor('persons', 'name'),
        webFormAttributeFor('persons', 'emails'),
    ];

    $form = WebForm::create([
        'form_id' => bin2hex(random_bytes(8)),
        'title' => 'Contact us',
        'submit_button_label' => 'Send',
        'submit_success_action' => 'message',
        'submit_success_content' => 'Thanks.',
        'create_lead' => 1,
    ]);

    foreach ($exposed as $sortOrder => $attribute) {
        WebFormAttribute::create([
            'name' => $attribute->code,
            'is_required' => 0,
            'is_hidden' => 0,
            'sort_order' => $sortOrder + 1,
            'attribute_id' => $attribute->id,
            'web_form_id' => $form->id,
        ]);
    }

    return [$form, $upload];
}

function submitUpload(WebForm $form, Attribute $upload, UploadedFile $file)
{
    return test()->post('/web-forms/forms/'.$form->id, [
        'leads' => ['title' => 'Upload lead '.uniqid(), $upload->code => $file],
        'persons' => [
            'name' => 'Web Visitor',
            'emails' => [['value' => uniqid().'@example.invalid', 'label' => 'work']],
        ],
    ]);
}

it('strips script from an uploaded svg', function () {
    isolatePublicDisk();

    [$form, $upload] = uploadForm();

    submitUpload($form, $upload, UploadedFile::fake()->createWithContent(
        'payload.svg',
        '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(document.domain)</script></svg>'
    ));

    /**
     * The path is read back from the attribute value rather than by scanning the disk: the fake
     * disk is shared between the suite's parallel workers, so a listing of it describes whatever
     * else happens to be running.
     */
    $path = AttributeValue::query()
        ->where('attribute_id', $upload->id)
        ->value('text_value');

    expect($path, 'the svg should still be stored')->not->toBeNull();

    $contents = Storage::disk('public')->get($path);

    expect($contents)->not->toContain('<script')
        ->and($contents)->not->toContain('alert(');
});

it('refuses a file whose type is not permitted', function () {
    isolatePublicDisk();

    [$form, $upload] = uploadForm();

    $response = submitUpload($form, $upload, UploadedFile::fake()->createWithContent(
        'payload.html',
        '<html><body><script>alert(1)</script></body></html>'
    ));

    expect($response->status())->toBe(302);

    expect(
        AttributeValue::query()
            ->where('attribute_id', $upload->id)
            ->value('text_value')
    )->toBeNull();
});

it('refuses a file beyond the permitted size', function () {
    isolatePublicDisk();

    [$form, $upload] = uploadForm();

    /**
     * The endpoint is anonymous and unthrottled by size, so without a ceiling a visitor decides
     * how much of the disk a submission takes.
     */
    $response = submitUpload($form, $upload, UploadedFile::fake()->create('large.pdf', 4096));

    expect($response->status())->toBe(302);

    expect(
        AttributeValue::query()
            ->where('attribute_id', $upload->id)
            ->value('text_value')
    )->toBeNull();
});

it('still accepts an ordinary attachment', function () {
    isolatePublicDisk();

    [$form, $upload] = uploadForm();

    $response = submitUpload($form, $upload, UploadedFile::fake()->create('quote.pdf', 64));

    expect($response->status())->toBe(200);

    expect(
        AttributeValue::query()
            ->where('attribute_id', $upload->id)
            ->value('text_value')
    )->not->toBeNull();
});

/**
 * An image attribute is narrower than a file one: a document is not an image, whatever the
 * visitor names it.
 */
it('refuses a document submitted for an image attribute', function () {
    isolatePublicDisk();

    [$form, $upload] = uploadForm('image');

    $response = submitUpload($form, $upload, UploadedFile::fake()->create('quote.pdf', 64));

    expect($response->status())->toBe(302);

    expect(
        AttributeValue::query()
            ->where('attribute_id', $upload->id)
            ->value('text_value')
    )->toBeNull();
});
