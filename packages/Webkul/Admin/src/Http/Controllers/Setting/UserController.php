<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Webkul\User\Repositories\RoleRepository;
use Webkul\User\Repositories\GroupRepository;
use Webkul\User\Repositories\UserRepository;
use Webkul\Admin\Notifications\User\Create;
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
     * @param  \Webkul\User\Repositories\GroupRepository  $groupRepository
     * @param  \Webkul\User\Repositories\RoleRepository  $roleRepository
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepository;

        $this->groupRepository = $groupRepository;

        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Setting\UserDataGrid::class)->toJson();
        }

        return view('admin::settings.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = $this->roleRepository->all();

        $groups = $this->groupRepository->all();

        return view('admin::settings.users.create', compact('groups', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), [
            'email'            => 'required|email|unique:users,email',
            'name'             => 'required',
            'password'         => 'nullable',
            'confirm_password' => 'nullable|required_with:password|same:password',
            'role_id'          => 'required',
        ]);

        $data = request()->all();

        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        }

        $data['status'] = isset($data['status']) ? 1 : 0;

        Event::dispatch('settings.user.create.before');

        $admin = $this->userRepository->create($data);

        $admin->view_permission = $data['view_permission'];

        $admin->save();

        $admin->groups()->sync(request('groups') ?? []);

        try {
            Mail::queue(new Create($admin));
        } catch (\Exception $e) {
            report($e);
        }

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

        $groups = $this->groupRepository->all();

        return view('admin::settings.users.edit', compact('admin', 'groups', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'email'            => 'required|email|unique:users,email,' . $id,
            'name'             => 'required',
            'password'         => 'nullable',
            'confirm_password' => 'nullable|required_with:password|same:password',
            'role_id'          => 'required',
        ]);

        $data = request()->all();

        if (! $data['password']) {
            unset($data['password'], $data['confirm_password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        if (auth()->guard('user')->user()->id != $id) {
            $data['status'] = isset($data['status']) ? 1 : 0;
        }

        Event::dispatch('settings.user.update.before', $id);

        $admin = $this->userRepository->update($data, $id);

        $admin->view_permission = $data['view_permission'];

        $admin->save();

        $admin->groups()->sync(request('groups') ?? []);

        Event::dispatch('settings.user.update.after', $admin);

        session()->flash('success', trans('admin::app.settings.users.update-success'));

        return redirect()->route('admin.settings.users.index');
    }

    /**
     * Destroy specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (auth()->guard('user')->user()->id == $id) {
            return response()->json([
                'message' => trans('admin::app.settings.users.delete-failed'),
            ], 400);
        } else if ($this->userRepository->count() == 1) {
            return response()->json([
                'message' => trans('admin::app.settings.users.last-delete-error'),
            ], 400);
        } else {
            Event::dispatch('settings.user.delete.before', $id);

            try {
                $this->userRepository->delete($id);

                Event::dispatch('settings.user.delete.after', $id);

                return response()->json([
                    'message' => trans('admin::app.settings.users.delete-success'),
                ]);
            } catch (\Exception $exception) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 400);
            }
        }
    }

    /**
     * Mass Update the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate()
    {
        $count = 0;

        foreach (request('rows') as $userId) {
            if (auth()->guard('user')->user()->id == $userId) {
                continue;
            }

            Event::dispatch('settings.user.update.before', $userId);

            $this->userRepository->update([
                'status' => request('value'),
            ], $userId);

            Event::dispatch('settings.user.update.after', $userId);

            $count++;
        }

        if (! $count) {
            return response()->json([
                'message' => trans('admin::app.settings.users.mass-update-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.users.mass-update-success'),
        ]);
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $count = 0;

        foreach (request('rows') as $userId) {
            if (auth()->guard('user')->user()->id == $userId) {
                continue;
            }
            
            Event::dispatch('settings.user.delete.before', $userId);

            $this->userRepository->delete($userId);

            Event::dispatch('settings.user.delete.after', $userId);

            $count++;
        }

        if (! $count) {
            return response()->json([
                'message' => trans('admin::app.settings.users.mass-delete-failed'),
            ], 400);
        }

        return response()->json([
            'message' => trans('admin::app.settings.users.mass-delete-success'),
        ]);
    }
}
