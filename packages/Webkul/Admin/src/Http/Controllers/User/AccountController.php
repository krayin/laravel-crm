<?php

namespace Webkul\Admin\Http\Controllers\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;

class AccountController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = auth()->guard('user')->user();

        return view('admin::user.account.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $isPasswordChanged = false;

        $user = auth()->guard('user')->user();

        $this->validate(request(), [
            'name'             => 'required',
            'email'            => 'email|unique:users,email,' . $user->id,
            'password'         => 'nullable|min:6|confirmed',
            'current_password' => 'nullable|required|min:6',
        ]);

        $data = request()->input();

        if (! Hash::check($data['current_password'], auth()->guard('user')->user()->password)) {
            session()->flash('warning', trans('admin::app.user.account.password-match'));

            return redirect()->back();
        }

        if( isset($data['role_id']) || isset($data['view_permission']) ) {
            session()->flash('warning', trans('admin::app.user.account.permission-denied'));

            return redirect()->back();
        }

        if (! $data['password']) {
            unset($data['password']);
        } else {
            $isPasswordChanged = true;

            $data['password'] = bcrypt($data['password']);
        }

        if (request()->hasFile('image')) {
            $data['image'] = request()->file('image')->store('users/' . $user->id);
        }
        
        if (isset($data['remove_image']) && $data['remove_image'] !== '') {
            $data['image'] = null;
        }

        $user->update($data);

        if ($isPasswordChanged) {
            Event::dispatch('user.account.update-password', $user);
        }

        session()->flash('success', trans('admin::app.user.account.account-save'));

        return redirect()->route('admin.dashboard.index');
    }
}
