<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\UserForm;
use Webkul\User\Repositories\RoleRepository;
use Webkul\User\Repositories\UserRepository;

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
            'first_name'    => 'required',
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

        Event::dispatch('settings.user.create.before');

        $admin = $this->userRepository->create($data);

        Event::dispatch('settings.user.create.after', $admin);

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'User']));

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
    public function update(UserForm $request, $id)
    {
        $data = $request->all();

        if (! $data['password']) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $data['status'] = isset($data['status']) ? 1 : 0;

        Event::dispatch('settings.user.update.before', $id);

        $admin = $this->userRepository->update($data, $id);

        Event::dispatch('settings.user.update.after', $admin);

        session()->flash('success', trans('admin::app.response.update-success', ['name' => 'User']));

        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function destroy($id)
    {
        $admin = $this->userRepository->findOrFail($id);

        if ($this->userRepository->count() == 1) {
            session()->flash('error', trans('admin::app.response.last-delete-error', ['name' => 'Admin']));
        } else {
            Event::dispatch('settings.user.delete.before', $id);

            try {
                $this->userRepository->delete($id);

                session()->flash('success', trans('admin::app.response.delete-success', ['name' => 'Admin']));

                Event::dispatch('settings.user.delete.after', $id);

                return response()->json(['message' => true], 200);
            } catch (Exception $e) {
                session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Admin']));
            }
        }

        return response()->json(['message' => false], 400);
    }
}
