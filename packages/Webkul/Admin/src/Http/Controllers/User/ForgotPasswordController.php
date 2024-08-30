<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Notifications\User\UserResetPassword;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->guard('user')->check()) {
            return redirect()->route('admin.dashboard.index');
        } else {
            if (strpos(url()->previous(), 'user') !== false) {
                $intendedUrl = url()->previous();
            } else {
                $intendedUrl = route('admin.dashboard.index');
            }

            session()->put('url.intended', $intendedUrl);

            return view('admin::sessions.forgot-password');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        try {
            $this->validate(request(), [
                'email' => 'required|email',
            ]);

            $response = $this->broker()->sendResetLink(request(['email']), function ($user, $token) {
                $user->notify(new UserResetPassword($token));
            });

            if ($response == Password::RESET_LINK_SENT) {
                session()->flash('success', trans('admin::app.users.forget-password.create.reset-link-sent'));

                return back();
            }

            return back()
                ->withInput(request(['email']))
                ->withErrors([
                    'email' => trans('admin::app.users.forget-password.create.email-not-exist'),
                ]);
        } catch (\Exception $exception) {
            session()->flash('error', trans($exception->getMessage()));

            return redirect()->back();
        }
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('users');
    }
}
