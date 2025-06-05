<?php

namespace Webkul\RestApi\Http\Controllers\V1\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Resources\V1\Setting\UserResource;

class AccountController extends Controller
{
    /**
     * Get the details for current logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $user = $request->user();

        return new UserResource($user);
    }

    /**
     * Update the details for current logged in user.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $isPasswordChanged = false;

        $data = $request->validate([
            'name'             => 'required',
            'email'            => 'email|unique:users,email,'.$user->id,
            'password'         => 'nullable|min:6|confirmed',
            'current_password' => 'nullable|required|min:6',
            'image'            => 'mimes:jpeg,jpg,png,gif',
            'remove_image'     => 'sometimes|boolean',
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return response([
                'message' => trans('admin::app.user.account.password-match'),
            ], 400);
        }

        if (
            isset($data['role_id'])
            || isset($data['view_permission'])
        ) {
            return response([
                'message' => trans('admin::app.user.account.permission-denied'),
            ], 400);
        }

        if (isset($data['password'])) {
            $isPasswordChanged = true;

            $data['password'] = bcrypt($data['password']);
        }

        $this->handleProfileImageUpload($data, $user);

        $user->update($data);

        if ($isPasswordChanged) {
            Event::dispatch('user.account.update-password', $user);
        }

        return response([
            'data'    => new UserResource($user),
            'message' => trans('admin::app.user.account.account-save'),
        ]);
    }

    /**
     * Handle profile image upload.
     *
     * @param  array  $data
     * @param  object  $user
     * @return void
     */
    public function handleProfileImageUpload(&$data, $user)
    {
        $oldImage = $user->image;

        if (! isset($data['image'])) {
            $data['image'] = $user->image;
        }

        if (request()->hasFile('image')) {
            $data['image'] = request()->file('image')->store('users/'.$user->id);
        }

        if (
            isset($data['remove_image'])
            && $data['remove_image']
        ) {
            $data['image'] = null;
        }

        if (
            $oldImage
            && ($data['image'] !== $oldImage)
        ) {
            Storage::delete($oldImage);
        }
    }
}
