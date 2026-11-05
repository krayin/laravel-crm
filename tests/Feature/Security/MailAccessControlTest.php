<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Webkul\Contact\Models\Person;
use Webkul\Email\Models\Attachment;
use Webkul\Email\Models\Email;
use Webkul\Email\Repositories\AttachmentRepository;
use Webkul\Lead\Models\Lead;
use Webkul\Tag\Models\Tag;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

/**
 * Access control for the mail section, complementing MailObjectLevelAuthorizationTest (data scope):
 *
 * - every mail route requires a signed-in user;
 * - every action requires its own ACL permission — `mass_update` and tagging fell back to "any mail
 *   permission", so a read-only user could move mail to Trash;
 * - attachments are stored privately under random names and only downloaded, in scope, as files;
 * - tag names are not rendered as markup in the mail list.
 */
uses(DatabaseTransactions::class);

const MAIL_PERMISSIONS = [
    'mail',
    'mail.inbox',
    'mail.draft',
    'mail.outbox',
    'mail.sent',
    'mail.trash',
    'mail.compose',
    'mail.compose.quick-create',
    'mail.view',
    'mail.edit',
    'mail.delete',
];

beforeEach(function () {
    $this->diskRoot = sys_get_temp_dir().'/krayin-mail-'.bin2hex(random_bytes(8));

    config([
        'filesystems.disks.local.root' => $this->diskRoot.'/app',
        'filesystems.disks.public.root' => $this->diskRoot.'/app/public',
    ]);

    Storage::forgetDisk(['local', 'public']);
});

afterEach(function () {
    File::deleteDirectory($this->diskRoot);
});

function mailRoleUser(array $permissions, string $viewPermission = 'global'): User
{
    $role = Role::create([
        'name' => 'Mail ACL Role '.bin2hex(random_bytes(6)),
        'description' => 'Created by the mail access control tests.',
        'permission_type' => 'custom',
        'permissions' => array_values($permissions),
    ]);

    return User::create([
        'name' => 'Mail ACL Probe',
        'email' => 'mail-acl-'.bin2hex(random_bytes(6)).'@example.invalid',
        'password' => bcrypt('correct-horse-battery-staple'),
        'status' => 1,
        'role_id' => $role->id,
        'view_permission' => $viewPermission,
    ]);
}

function sharedMail(array $attributes = []): Email
{
    return Email::create(array_merge([
        'subject' => 'Shared mailbox enquiry',
        'reply' => 'Sent to the general address.',
        'source' => 'email',
        'user_type' => 'person',
        'folders' => ['inbox'],
        'name' => 'Sender',
        'unique_id' => bin2hex(random_bytes(8)),
        'message_id' => bin2hex(random_bytes(8)).'@example.invalid',
    ], $attributes));
}

function attachmentOn(Email $email, string $content = 'attachment body'): Attachment
{
    $path = 'emails/'.$email->id.'/'.bin2hex(random_bytes(20));

    Storage::disk(AttachmentRepository::DISK)->put($path, $content);

    return Attachment::create([
        'name' => 'contract.pdf',
        'path' => $path,
        'size' => strlen($content),
        'content_type' => 'application/pdf',
        'email_id' => $email->id,
    ]);
}

/**
 * Each mail action and the permission it requires. The closure builds the request against fresh
 * records and returns the response.
 */
function mailActions(): array
{
    $tag = fn () => Tag::create(['name' => 'ACL '.bin2hex(random_bytes(4)), 'color' => '#FEE2E2', 'user_id' => getDefaultAdmin()->id]);

    return [
        'view a mail' => [['mail.view'], fn () => test()->get('/admin/mail/draft/'.sharedMail()->id)],
        'download an attachment' => [['mail.view'], fn () => test()->get(route('admin.mail.attachment_download', attachmentOn(sharedMail())->id))],
        'compose a mail' => [['mail.compose', 'mail.compose.quick-create'], fn () => test()->post(route('admin.mail.store'), [
            'is_draft' => 1, 'reply' => 'Body', 'reply_to' => ['someone@example.invalid'],
        ])],
        'update a mail' => [['mail.edit'], fn () => test()->putJson(route('admin.mail.update', sharedMail()->id), ['is_read' => 1])],
        'move mail between folders' => [['mail.edit'], fn () => test()->postJson(route('admin.mail.mass_update'), [
            'indices' => [sharedMail()->id], 'value' => 'trash', 'folders' => ['trash'],
        ])],
        'tag a mail' => [['mail.edit'], fn () => test()->postJson(route('admin.mail.tags.attach', sharedMail()->id), ['tag_id' => $tag()->id])],
        'untag a mail' => [['mail.edit'], fn () => test()->deleteJson(route('admin.mail.tags.detach', sharedMail()->id), ['tag_id' => $tag()->id])],
        'delete a mail' => [['mail.delete'], fn () => test()->deleteJson(route('admin.mail.delete', sharedMail()->id), ['type' => 'trash'])],
        'delete mail in bulk' => [['mail.delete'], fn () => test()->postJson(route('admin.mail.mass_delete'), [
            'indices' => [sharedMail()->id], 'type' => 'trash',
        ])],
    ];
}

it('sends a signed-out visitor to the login page from every mail route', function (string $method, string $uri) {
    test()->call($method, $uri)->assertRedirect(route('admin.session.create'));
})->with([
    'mailbox' => ['GET', '/admin/mail/inbox'],
    'view' => ['GET', '/admin/mail/inbox/1'],
    'compose' => ['POST', '/admin/mail/create'],
    'update' => ['PUT', '/admin/mail/edit/1'],
    'attachment' => ['GET', '/admin/mail/attachment-download/1'],
    'delete' => ['DELETE', '/admin/mail/1'],
    'mass update' => ['POST', '/admin/mail/mass-update'],
    'mass delete' => ['POST', '/admin/mail/mass-destroy'],
    'tag' => ['POST', '/admin/mail/1/tags'],
]);

it('refuses a mail action without its permission', function (array $required, Closure $request) {
    test()->actingAs(mailRoleUser(array_diff(MAIL_PERMISSIONS, $required)), 'user');

    expect($request()->getStatusCode())->toBe(401);
})->with(mailActions());

it('allows a mail action with its permission', function (array $required, Closure $request) {
    test()->actingAs(mailRoleUser(['mail', ...$required]), 'user');

    expect($request()->getStatusCode())->not->toBe(401);
})->with(mailActions());

it('does not let a read-only user move mail to the trash', function () {
    $email = sharedMail();

    test()->actingAs(mailRoleUser(['mail', 'mail.inbox', 'mail.view']), 'user')
        ->postJson(route('admin.mail.mass_update'), [
            'indices' => [$email->id],
            'value' => 'trash',
            'folders' => ['trash'],
        ])
        ->assertStatus(401);

    expect($email->fresh()->folders)->toBe(['inbox']);
});

/**
 * Attachments.
 */
it('stores a composed attachment on the private disk under a random name', function () {
    test()->actingAs(getDefaultAdmin(), 'user')
        ->post(route('admin.mail.store'), [
            'is_draft' => 1,
            'subject' => 'With attachment',
            'reply' => 'See attached.',
            'reply_to' => ['someone@example.invalid'],
            'attachments' => [UploadedFile::fake()->createWithContent('../../public/evil.html', '<script>alert(1)</script>')],
        ])
        ->assertSessionHasNoErrors();

    $attachment = Attachment::query()->latest('id')->firstOrFail();

    expect($attachment->name)->toBe('evil.html')
        ->and($attachment->path)->toMatch('/^emails\/\d+\/[A-Za-z0-9]{40}$/')
        ->and(Storage::disk('public')->allFiles())->toBeEmpty();

    Storage::disk(AttachmentRepository::DISK)->assertExists($attachment->path);
});

it('downloads an attachment as a file, never inline', function () {
    $attachment = attachmentOn(sharedMail(), '<script>alert(1)</script>');

    $response = test()->actingAs(getDefaultAdmin(), 'user')
        ->get(route('admin.mail.attachment_download', $attachment->id))
        ->assertOk();

    expect($response->headers->get('Content-Disposition'))->toStartWith('attachment;')
        ->toContain('contract.pdf');
});

it('refuses an attachment on a mail outside the caller scope', function () {
    $owner = mailRoleUser(MAIL_PERMISSIONS);
    $agent = mailRoleUser(MAIL_PERMISSIONS, 'individual');

    $person = Person::create([
        'name' => 'Owned Contact',
        'user_id' => $owner->id,
        'emails' => [['value' => 'owned-'.bin2hex(random_bytes(4)).'@example.invalid', 'label' => 'work']],
    ]);

    $attachment = attachmentOn(sharedMail(['person_id' => $person->id]));

    test()->actingAs($agent, 'user')
        ->get(route('admin.mail.attachment_download', $attachment->id))
        ->assertStatus(401);
});

it('refuses an attachment on a reply in another user thread', function () {
    $owner = mailRoleUser(MAIL_PERMISSIONS);
    $agent = mailRoleUser(MAIL_PERMISSIONS, 'individual');

    $pipeline = securityTestPipeline();

    $lead = Lead::create([
        'title' => 'Owned Lead',
        'user_id' => $owner->id,
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->value('id'),
    ]);

    $reply = sharedMail(['parent_id' => sharedMail(['lead_id' => $lead->id])->id]);

    test()->actingAs($agent, 'user')
        ->get(route('admin.mail.attachment_download', attachmentOn($reply)->id))
        ->assertStatus(401);
});

it('still lets a restricted user download an attachment on shared mail', function () {
    $agent = mailRoleUser(MAIL_PERMISSIONS, 'individual');

    test()->actingAs($agent, 'user')
        ->get(route('admin.mail.attachment_download', attachmentOn(sharedMail())->id))
        ->assertOk();
});

/**
 * The mail list rendered tag names as HTML, and grid closures run after markup is stripped.
 */
it('does not hand a tag name to the mail list as markup', function () {
    $token = 'tagged-'.bin2hex(random_bytes(4));

    $email = sharedMail(['subject' => $token]);

    $tag = Tag::create(['name' => 'VIP<img src=x onerror=alert(1)>', 'color' => '#FEE2E2', 'user_id' => getDefaultAdmin()->id]);

    $email->tags()->attach($tag->id);

    $tags = test()->actingAs(getDefaultAdmin(), 'user')
        ->getJson('/admin/mail/inbox?'.http_build_query(['filters' => ['all' => [$token]]]), ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()
        ->json('records.0.tags');

    expect(collect($tags)->pluck('name')->all())->toBe(['VIP']);

    expect(test()->actingAs(getDefaultAdmin(), 'user')->get('/admin/mail/inbox')->getContent())
        ->not->toContain('v-html="tag.name"');
});
