<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SSOController extends Controller
{
    public function redirectToSSO()
    {
        $query = http_build_query([
            'client_id' => env('SSO_CLIENT_ID'),
            'redirect_uri' => env('SSO_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => '',
        ]);

        return redirect(env('SSO_AUTH_URL') . '?' . $query);
    }


    public function handleCallback(Request $request)
    {
        // die($request);
        $response = Http::withoutVerifying()->asForm()->post(env('SSO_TOKEN_URL'), [
            'grant_type' => 'authorization_code',
            'client_id' => env('SSO_CLIENT_ID'),
            'client_secret' => env('SSO_CLIENT_SECRET'),
            'redirect_uri' => env('SSO_REDIRECT_URI'),
            'code' => $request->code,
        ]);

        $accessToken = $response->json()['access_token'];

        $user = Http::withoutVerifying()->withToken($accessToken)->get(env('SSO_USERINFO_URL'))->json();

        // var_dump('var_dump response', $response);
        // var_dump('var_dump accessToken', $accessToken);
        // var_dump('var_dump user', $user);
        // Đăng nhập hoặc tạo user trong hệ thống CRM
        // Auth::login(User::firstOrCreate([o
        //     'email' => $user['email'],
        // ], [
        //     'name' => $user['username'],
        // ]));
        if (!isset($user)) {
            return $this->redirectToLogin()
                ->withErrors([
                    'auth' => 'Account is not registered',
                ])
            ;
        }

        $userLogin = User::where('email', $user['email'])->first();
        if (!isset($userLogin)) {
            return $this->redirectToLogin()
                ->withErrors([
                    'auth' => 'Account is still not granted: ' . $user['email'],
                ])
            ;
        }
        Auth::login($userLogin);

        return redirect('/');
    }
}
