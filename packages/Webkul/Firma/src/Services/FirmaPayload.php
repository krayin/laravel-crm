<?php

namespace Webkul\Firma\Services;

/**
 * Request bodies for Firma's create-and-send, and the template rules they follow.
 */
class FirmaPayload
{
    public const MAX_NAME_LENGTH = 255;

    /**
     * Default signature box for document sends, in percent of the page: bottom right.
     */
    public const DEFAULT_SIGNATURE_BOX = ['x' => 58.0, 'y' => 84.0, 'width' => 32.0, 'height' => 7.0];

    /**
     * A template with its Signer and Approver roles (CC rows dropped), by order.
     *
     * `supported` is true when one recipient fills every role, which is what this integration
     * sends.
     */
    public static function parseTemplate(array $row): array
    {
        $users = [];

        foreach ($row['recipients'] ?? [] as $recipient) {
            if (! is_array($recipient) || empty($recipient['id'])) {
                continue;
            }

            $designation = is_string($recipient['designation'] ?? null) ? $recipient['designation'] : 'Signer';

            if (strtoupper($designation) === 'CC') {
                continue;
            }

            $users[] = [
                'id' => (string) $recipient['id'],
                'designation' => $designation,
                'order' => is_int($recipient['order'] ?? null) ? $recipient['order'] : 1,
            ];
        }

        usort($users, fn ($a, $b) => $a['order'] <=> $b['order']);

        return [
            'id' => (string) $row['id'],
            'name' => is_string($row['name'] ?? null) && $row['name'] !== '' ? $row['name'] : (string) $row['id'],
            'users' => $users,
            'supported' => count($users) === 1,
        ];
    }

    /**
     * The single template role a one recipient send fills.
     *
     * Firma's create-and-send needs a recipient for every template role and silently drops a
     * recipient that matches no role. This integration sends to one recipient, so a template
     * with more than one Signer or Approver, or with none, is refused before anything is sent.
     */
    public static function pickTemplateUser(array $template): array
    {
        $count = count($template['users']);

        if ($count === 1) {
            return $template['users'][0];
        }

        if ($count === 0) {
            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.template-no-roles', ['name' => $template['name']]));
        }

        throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.template-many-roles', [
            'name' => $template['name'],
            'count' => $count,
        ]));
    }

    /**
     * Splits a full name into first and last name.
     *
     * @return array{0: string, 1: string}
     */
    public static function splitName(?string $fullName): array
    {
        $parts = preg_split('/\s+/', trim((string) $fullName), 2) ?: [];

        return [mb_substr($parts[0] ?? '', 0, 100), mb_substr($parts[1] ?? '', 0, 100)];
    }

    /**
     * Create-and-send body for a template with exactly one role, filled by the recipient and
     * matched by `template_user_id`, keeping the role's designation and order.
     */
    public static function buildTemplateBody(
        string $templateId,
        array $templateUser,
        string $name,
        string $firstName,
        ?string $lastName,
        string $email,
        ?int $expirationHours
    ): array {
        if (trim($templateId) === '') {
            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.choose-template'));
        }

        $body = static::baseBody($name, $expirationHours);

        $recipient = static::recipient($firstName, $lastName, $email);

        $recipient['template_user_id'] = $templateUser['id'];

        $recipient['designation'] = $templateUser['designation'];

        $recipient['order'] = $templateUser['order'];

        $body['template_id'] = trim($templateId);

        $body['recipients'] = [$recipient];

        return $body;
    }

    /**
     * Create-and-send body for a PDF with one Signer and one signature box on the last page.
     */
    public static function buildDocumentBody(
        string $name,
        string $firstName,
        ?string $lastName,
        string $email,
        int $pageCount,
        ?string $documentBase64,
        ?string $documentId,
        ?int $expirationHours
    ): array {
        if (empty($documentBase64) === empty($documentId)) {
            throw FirmaApiException::invalidInput('Provide the document either inline or as an uploaded document id.');
        }

        if ($pageCount < 1) {
            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.unreadable-pdf'));
        }

        $body = static::baseBody($name, $expirationHours);

        $recipient = static::recipient($firstName, $lastName, $email);

        $recipient += ['id' => 'temp_1', 'designation' => 'Signer', 'order' => 1];

        if ($documentBase64) {
            $body['document'] = $documentBase64;
        } else {
            $body['document_id'] = $documentId;
        }

        $body['recipients'] = [$recipient];

        $body['fields'] = [[
            'type' => 'signature',
            'recipient_id' => 'temp_1',
            'page_number' => $pageCount,
            'position' => self::DEFAULT_SIGNATURE_BOX,
            'required' => true,
        ]];

        return $body;
    }

    /**
     * The name and optional expiration every body carries.
     */
    protected static function baseBody(string $name, ?int $expirationHours): array
    {
        $name = mb_substr(trim($name), 0, self::MAX_NAME_LENGTH);

        if ($name === '') {
            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.name-required'));
        }

        $body = ['name' => $name];

        if (! is_null($expirationHours)) {
            if ($expirationHours < 1) {
                throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.expiration'));
            }

            $body['expiration_hours'] = $expirationHours;
        }

        return $body;
    }

    /**
     * A recipient with a name and a valid email address.
     */
    protected static function recipient(string $firstName, ?string $lastName, string $email): array
    {
        $firstName = trim($firstName);

        $email = trim($email);

        if ($firstName === '') {
            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.recipient-name'));
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw FirmaApiException::invalidInput(trans('admin::app.firma.errors.recipient-email'));
        }

        $recipient = ['first_name' => mb_substr($firstName, 0, 100), 'email' => $email];

        if (! is_null($lastName) && trim($lastName) !== '') {
            $recipient['last_name'] = mb_substr(trim($lastName), 0, 100);
        }

        return $recipient;
    }
}
