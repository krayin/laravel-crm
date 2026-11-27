<?php

namespace Webkul\Firma\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Webkul\Core\Repositories\CoreConfigRepository;

/**
 * The Firma connection, kept in Krayin's `core_config` store.
 *
 * The API key and the webhook signing secret are encrypted with the application key, never
 * rendered back into a page, and never logged.
 */
class FirmaSettings
{
    public const API_KEY = 'firma.settings.api_key';

    public const WEBHOOK_SECRET = 'firma.settings.webhook_secret';

    public const WEBHOOK_ID = 'firma.settings.webhook_id';

    /**
     * Create a new settings instance.
     */
    public function __construct(protected CoreConfigRepository $coreConfigRepository) {}

    /**
     * The API key, or null when it is not set.
     */
    public function apiKey(): ?string
    {
        return $this->readEncrypted(self::API_KEY);
    }

    /**
     * The webhook signing secret, or null when it is not set.
     */
    public function webhookSecret(): ?string
    {
        return $this->readEncrypted(self::WEBHOOK_SECRET);
    }

    /**
     * The id of the webhook this CRM registered in Firma.
     */
    public function webhookId(): ?string
    {
        $value = $this->read(self::WEBHOOK_ID);

        return $value === '' ? null : $value;
    }

    /**
     * Stores (or clears, with null) the API key.
     */
    public function setApiKey(?string $apiKey): void
    {
        $this->writeEncrypted(self::API_KEY, $apiKey);
    }

    /**
     * Stores (or clears, with null) the webhook signing secret.
     */
    public function setWebhookSecret(?string $secret): void
    {
        $this->writeEncrypted(self::WEBHOOK_SECRET, $secret);
    }

    /**
     * Stores the id of the registered webhook.
     */
    public function setWebhookId(?string $webhookId): void
    {
        $this->write(self::WEBHOOK_ID, $webhookId);
    }

    /**
     * The last four characters of the API key, for display.
     */
    public function apiKeyHint(): ?string
    {
        $apiKey = $this->apiKey();

        return $apiKey ? mb_substr($apiKey, -4) : null;
    }

    /**
     * A client for the stored API key.
     *
     * @throws FirmaApiException when no API key is set.
     */
    public function client(): FirmaClient
    {
        return new FirmaClient($this->apiKey());
    }

    /**
     * Reads and decrypts a value. A value that does not decrypt counts as not set.
     */
    protected function readEncrypted(string $code): ?string
    {
        $value = $this->read($code);

        if ($value === null || $value === '') {
            return null;
        }

        try {
            $decrypted = Crypt::decryptString($value);
        } catch (DecryptException) {
            return null;
        }

        return $decrypted === '' ? null : $decrypted;
    }

    /**
     * Encrypts and stores a value, or removes it.
     */
    protected function writeEncrypted(string $code, ?string $value): void
    {
        $value = is_null($value) ? null : trim($value);

        $this->write($code, $value === null || $value === '' ? null : Crypt::encryptString($value));
    }

    /**
     * Reads a raw value.
     */
    protected function read(string $code): ?string
    {
        $value = $this->coreConfigRepository->getModel()->newQuery()->where('code', $code)->value('value');

        return is_null($value) ? null : (string) $value;
    }

    /**
     * Writes a raw value, or removes the row for null.
     */
    protected function write(string $code, ?string $value): void
    {
        $query = $this->coreConfigRepository->getModel()->newQuery();

        if (is_null($value)) {
            $query->where('code', $code)->delete();

            return;
        }

        $query->updateOrCreate(['code' => $code], ['value' => $value]);
    }
}
