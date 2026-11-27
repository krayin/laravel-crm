<?php

use Illuminate\Support\Str;
use Webkul\Contact\Models\Person;
use Webkul\Firma\Models\SignatureRequest;
use Webkul\Firma\Services\FirmaSettings;
use Webkul\Lead\Models\Lead;
use Webkul\Lead\Models\Source;
use Webkul\Lead\Models\Type;
use Webkul\User\Models\Role;
use Webkul\User\Models\User;

/**
 * Shared fixtures for the Firma feature tests (required by each Firma test file).
 */
function firmaConfigure(?string $apiKey = 'firma_test_key', ?string $secret = 'whsec_test'): FirmaSettings
{
    $settings = app(FirmaSettings::class);

    $settings->setApiKey($apiKey);

    $settings->setWebhookSecret($secret);

    return $settings;
}

function firmaPerson(?string $email = 'ada@firma.dev', ?int $userId = null): Person
{
    return Person::create([
        'name' => 'Ada Lovelace',
        'emails' => $email ? [['value' => $email, 'label' => 'work']] : [],
        'user_id' => $userId ?? getDefaultAdmin()->id,
    ]);
}

function firmaLead(?int $userId = null, ?Person $person = null): Lead
{
    $pipeline = securityTestPipeline();

    return Lead::create([
        'title' => 'Firma lead '.uniqid(),
        'description' => 'Lead used by the Firma tests.',
        'lead_value' => 1000,
        'status' => 1,
        'user_id' => $userId ?? getDefaultAdmin()->id,
        'person_id' => ($person ?? firmaPerson(userId: $userId))->id,
        'lead_pipeline_id' => $pipeline->id,
        'lead_pipeline_stage_id' => $pipeline->stages()->first()->id,
        'lead_source_id' => Source::query()->first()->id,
        'lead_type_id' => Type::query()->first()->id,
    ]);
}

function firmaSignatureRequest(Lead $lead, array $attributes = []): SignatureRequest
{
    return SignatureRequest::create(array_merge([
        'firma_id' => 'sr_'.uniqid(),
        'lead_id' => $lead->id,
        'user_id' => $lead->user_id,
        'name' => 'Contract',
        'source' => 'template',
        'recipient_name' => 'Ada Lovelace',
        'recipient_email' => 'ada@firma.dev',
        'status' => 'sent',
        'idempotency_key' => (string) Str::uuid(),
        'sent_at' => now(),
    ], $attributes));
}

/**
 * A user whose role holds exactly the given permissions.
 */
function firmaProbeUser(array $permissions, string $viewPermission = 'global'): User
{
    $role = Role::create([
        'name' => 'Firma probe '.uniqid(),
        'description' => 'Firma probe role',
        'permission_type' => 'custom',
        'permissions' => $permissions,
    ]);

    return User::create([
        'name' => 'Firma probe',
        'email' => 'firma-probe-'.uniqid().'@example.invalid',
        'password' => bcrypt('secret123'),
        'status' => 1,
        'view_permission' => $viewPermission,
        'role_id' => $role->id,
    ]);
}
