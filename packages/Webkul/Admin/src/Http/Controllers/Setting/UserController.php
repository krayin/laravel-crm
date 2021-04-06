<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;

use Webkul\Admin\Http\Requests\UserForm;
use Webkul\User\Repositories\RoleRepository;
use Webkul\User\Repositories\UserRepository;
use Webkul\Admin\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * UserRepository object
     *
     * @var \Webkul\User\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * RoleRepository object
     *
     * @var \Webkul\User\Repositories\RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\User\Repositories\UserRepository  $userRepository
     * @param  \Webkul\User\Repositories\RoleRepository  $roleRepository
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository
    )
    {
        $this->userRepository = $userRepository;

        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::settings.users.index', [
            'tableClass' => '\Webkul\Admin\DataGrids\UserDataGrid'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = $this->roleRepository->all();

        return view('admin::settings.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), [
            'email'         => 'required|email|unique:users,email',
            'name'          => 'required',
            'password'      => 'required',
            'role_id'       => 'required',
        ]);
        
        $data = request()->all();

        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        }

        if (isset($data['status']) && $data['status'] == 'on') {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        $data['status'] = isset($data['status']) ? 1 : 0;

        Event::dispatch('settings.user.create.before');

        $admin = $this->userRepository->create($data);

        Event::dispatch('settings.user.create.after', $admin);

        session()->flash('success', trans('admin::app.settings.users.create-success'));

        return redirect()->route('admin.settings.users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $admin = $this->userRepository->findOrFail($id);

        $roles = $this->roleRepository->all();

        return view('admin::settings.users.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Webkul\Admin\Http\Requests\UserForm  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'email'         => 'required|email',
            'name'          => 'required',
            'role_id'       => 'required',
        ]);

        $data = request()->all();

        if (! $data['password']) {
            unset($data['password']);
            unset($data['confirm_password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $data['status'] = isset($data['status']) ? 1 : 0;

        Event::dispatch('settings.user.update.before', $id);

        $admin = $this->userRepository->update($data, $id);

        Event::dispatch('settings.user.update.after', $admin);

        session()->flash('success', trans('admin::app.settings.users.update-success'));

        return redirect()->route('admin.settings.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function destroy($id)
    {
        if ($this->userRepository->count() == 1) {
            $status = false;
            $responseCode = 400;
            $message = trans('admin::app.settings.users.last-delete-error');

            session()->flash('error', $message);
        } else {
            Event::dispatch('settings.user.delete.before', $id);

            try {
                $this->userRepository->delete($id);

                $status = true;
                $responseCode = 200;
                $message = trans('admin::app.settings.users.delete-success');

                session()->flash('success', $message);

                Event::dispatch('settings.user.delete.after', $id);
            } catch (\Exception $exception) {
                $status = false;
                $responseCode = 400;
                $message = $exception->getMessage();

                session()->flash('error', trans('admin::app.settings.users.delete-failed'));
            }
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message
        ], $responseCode);
    }
}
