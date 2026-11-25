<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Firma\Services\FirmaApiException;
use Webkul\Firma\Services\FirmaClient;
use Webkul\Firma\Services\FirmaSettings;

class FirmaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected FirmaSettings $firmaSettings) {}

    /**
     * Display the Firma connection settings. Stored secrets are never rendered.
     */
    public function index(): View
    {
        return view('admin::settings.firma.index', [
            'apiKeyHint' => $this->firmaSettings->apiKeyHint(),
            'hasWebhookSecret' => ! is_null($this->firmaSettings->webhookSecret()),
            'webhookUrl' => route('firma.webhook'),
            'webhookId' => $this->firmaSettings->webhookId(),
        ]);
    }

    /**
     * Save the API key and the webhook signing secret. A blank field keeps the stored value.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'api_key' => 'nullable|string|max:255',
            'webhook_secret' => 'nullable|string|max:255',
            'remove_api_key' => 'nullable|boolean',
            'remove_webhook_secret' => 'nullable|boolean',
        ]);

        if (! empty($data['remove_api_key'])) {
            $this->firmaSettings->setApiKey(null);
        } elseif (filled($data['api_key'] ?? null)) {
            $this->firmaSettings->setApiKey($data['api_key']);
        }

        if (! empty($data['remove_webhook_secret'])) {
            $this->firmaSettings->setWebhookSecret(null);
        } elseif (filled($data['webhook_secret'] ?? null)) {
            $this->firmaSettings->setWebhookSecret($data['webhook_secret']);
        }

        session()->flash('success', trans('admin::app.firma.settings.save-success'));

        return redirect()->route('admin.settings.firma.index');
    }

    /**
     * Check the stored API key against Firma.
     */
    public function test(): RedirectResponse
    {
        try {
            $this->firmaSettings->client()->testConnection();

            session()->flash('success', trans('admin::app.firma.settings.test-success'));
        } catch (FirmaApiException $exception) {
            session()->flash('error', $exception->status === 401
                ? trans('admin::app.firma.settings.test-refused')
                : trans('admin::app.firma.settings.test-failed', ['message' => $exception->getMessage()]));
        }

        return redirect()->route('admin.settings.firma.index');
    }

    /**
     * Register this CRM's webhook in Firma, or repair the one already registered for its URL.
     */
    public function registerWebhook(): RedirectResponse
    {
        $url = route('firma.webhook');

        if (! str_starts_with($url, 'https://')) {
            session()->flash('error', trans('admin::app.firma.settings.https-required', ['url' => $url]));

            return redirect()->route('admin.settings.firma.index');
        }

        /**
         * Without the secret this CRM refuses every delivery with 401, and Firma retries each
         * one and in the end disables the webhook, losing the events in between.
         */
        if (is_null($this->firmaSettings->webhookSecret())) {
            session()->flash('error', trans('admin::app.firma.settings.secret-required'));

            return redirect()->route('admin.settings.firma.index');
        }

        try {
            $client = $this->firmaSettings->client();

            if ($existing = $client->findWebhooks($url)) {
                return $this->reuseWebhook($client, $existing[0], $url);
            }

            $this->firmaSettings->setWebhookId($client->createWebhook($url));
        } catch (FirmaApiException $exception) {
            session()->flash('error', trans('admin::app.firma.settings.register-failed', ['message' => $exception->getMessage()]));

            return redirect()->route('admin.settings.firma.index');
        }

        session()->flash('success', trans('admin::app.firma.settings.webhook-registered').' '.trans('admin::app.firma.settings.enable-webhooks-short'));

        return redirect()->route('admin.settings.firma.index');
    }

    /**
     * An existing webhook for this URL: re-enable it and add missing events when needed.
     *
     * Firma disables a webhook by itself after repeated failed deliveries (`auto_disabled_at`),
     * and a webhook created elsewhere may lack events.
     */
    protected function reuseWebhook(FirmaClient $client, array $webhook, string $url): RedirectResponse
    {
        $events = array_values(array_filter(is_array($webhook['events'] ?? null) ? $webhook['events'] : [], 'is_string'));

        $missing = array_values(array_diff(FirmaClient::WEBHOOK_EVENTS, $events));

        $disabled = ($webhook['enabled'] ?? null) !== true || ! empty($webhook['auto_disabled_at']);

        $this->firmaSettings->setWebhookId((string) $webhook['id']);

        if (! $missing && ! $disabled) {
            session()->flash('success', trans('admin::app.firma.settings.webhook-exists', ['url' => $url]).' '.trans('admin::app.firma.settings.enable-webhooks-short'));

            return redirect()->route('admin.settings.firma.index');
        }

        $client->updateWebhook((string) $webhook['id'], [
            'enabled' => true,
            'events' => array_merge($events, $missing),
        ]);

        $message = $disabled
            ? trans('admin::app.firma.settings.webhook-reenabled', ['url' => $url])
            : trans('admin::app.firma.settings.webhook-updated', ['url' => $url]);

        if ($missing) {
            $message .= ' '.trans('admin::app.firma.settings.events-added', ['events' => implode(', ', $missing)]);
        }

        session()->flash('warning', $message.' '.trans('admin::app.firma.settings.enable-webhooks-short'));

        return redirect()->route('admin.settings.firma.index');
    }
}
