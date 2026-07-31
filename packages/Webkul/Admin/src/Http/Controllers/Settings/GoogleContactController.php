<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Google\Service\Oauth2;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\GoogleContact\Repositories\GoogleContactAccountRepository;
use Webkul\GoogleContact\Services\GoogleClientFactory;

class GoogleContactController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected GoogleContactAccountRepository $googleContactAccountRepository,
        protected GoogleClientFactory $googleClientFactory,
    ) {}

    /**
     * Display the Google Contacts connection settings.
     */
    public function index(): View
    {
        $account = $this->googleContactAccountRepository->findOneByField('user_id', auth()->guard('user')->id());

        return view('admin::settings.google-contacts.index', compact('account'));
    }

    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function connect(): RedirectResponse
    {
        $client = $this->googleClientFactory->make();

        return redirect()->away($client->createAuthUrl());
    }

    /**
     * Handle the OAuth callback from Google, exchange the code for tokens,
     * and store the connection for the current user.
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            session()->flash('error', trans('admin::app.settings.google-contacts.index.connect-failed'));

            return redirect()->route('admin.settings.google_contacts.index');
        }

        $request->validate([
            'code' => 'required|string',
        ]);

        $client = $this->googleClientFactory->make();

        $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));

        if (isset($token['error'])) {
            session()->flash('error', trans('admin::app.settings.google-contacts.index.connect-failed'));

            return redirect()->route('admin.settings.google_contacts.index');
        }

        $client->setAccessToken($token);

        $googleEmail = (new Oauth2($client))->userinfo->get()->email;

        $this->googleContactAccountRepository->updateOrCreate(
            ['user_id' => auth()->guard('user')->id()],
            [
                'user_id' => auth()->guard('user')->id(),
                'google_email' => $googleEmail,
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'] ?? null,
                'expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
                'scopes' => explode(' ', $token['scope'] ?? ''),
            ]
        );

        session()->flash('success', trans('admin::app.settings.google-contacts.index.connect-success'));

        return redirect()->route('admin.settings.google_contacts.index');
    }

    /**
     * Disconnect the current user's Google account.
     */
    public function disconnect(): RedirectResponse
    {
        $account = $this->googleContactAccountRepository->findOneByField('user_id', auth()->guard('user')->id());

        if ($account) {
            try {
                $client = $this->googleClientFactory->make();

                $client->setAccessToken([
                    'access_token' => $account->access_token,
                    'refresh_token' => $account->refresh_token,
                ]);

                $client->revokeToken();
            } catch (\Exception $exception) {
                // Token may already be invalid/expired on Google's side; proceed with local cleanup regardless.
            }

            $this->googleContactAccountRepository->delete($account->id);
        }

        session()->flash('success', trans('admin::app.settings.google-contacts.index.disconnect-success'));

        return redirect()->route('admin.settings.google_contacts.index');
    }
}
