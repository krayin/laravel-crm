<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Response;
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
     * @return Response
     */
    public function store()
    {
        try {
            $this->validate(request(), [
                'email' => 'required|email',
            ]);

            $this->broker()->sendResetLink(request(['email']), function ($user, $token) {
                $user->notify(new UserResetPassword($token));
            });

            /**
             * The same answer is returned whether or not the address belongs to an
             * account. Reporting "this email does not exist" would let anyone probe
             * the form to learn which addresses are registered users.
             */
            session()->flash('success', trans('admin::app.users.forget-password.create.reset-link-sent'));

            return back();
        } catch (\Exception $exception) {
            session()->flash('error', trans($exception->getMessage()));

            return redirect()->back();
        }
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return PasswordBroker
     */
    public function broker()
    {
        return Password::broker('users');
    }
}
