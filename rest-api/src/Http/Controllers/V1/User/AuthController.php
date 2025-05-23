<?php

namespace Webkul\RestApi\Http\Controllers\V1\User;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Webkul\Admin\Notifications\User\UserResetPassword;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\UserResource;
use Webkul\User\Repositories\UserRepository;

class AuthController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Login user.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request, UserRepository $userRepository)
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required',
            'device_name' => 'required',
        ]);

        $user = $userRepository->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /**
         * Preventing multiple token creation.
         */
        $user->tokens()->delete();

        return response([
            'data'    => new UserResource($user),
            'message' => trans('rest-api::app.common.auth.login.success'),
            'token'   => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }

    /**
     * Logout user.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $customer = $request->user();

        $customer->tokens()->delete();

        return response([
            'message' => trans('rest-api::app.common.auth.login.logout'),
        ]);
    }

    /**
     * Send forgot password link.
     *
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $response = Password::broker('users')->sendResetLink($request->only('email'), function ($user, $token) {
            $user->notify(new UserResetPassword($token));
        });

        if ($response == Password::RESET_LINK_SENT) {
            return response([
                'message' => trans('admin::app.sessions.forgot-password.reset-link-sent'),
            ]);
        }

        return response([
            'message' => trans('admin::app.sessions.forgot-password.email-not-exist'),
        ], 400);
    }
}
