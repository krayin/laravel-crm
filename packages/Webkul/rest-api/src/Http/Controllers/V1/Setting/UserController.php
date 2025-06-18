<?php

namespace Webkul\RestApi\Http\Controllers\V1\Setting;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\Notifications\User\Create;
use Webkul\RestApi\Http\Controllers\V1\Controller;
use Webkul\RestApi\Http\Request\MassDestroyRequest;
use Webkul\RestApi\Http\Request\MassUpdateRequest;
use Webkul\RestApi\Http\Resources\V1\Setting\UserResource;
use Webkul\User\Repositories\UserRepository;
use Illuminate\Support\Arr;


class UserController extends Controller
{
    public function __construct(protected UserRepository $userRepository) {}

    public function index(): JsonResource
    {
        $users = $this->allResources($this->userRepository);
        return UserResource::collection($users);
    }

    public function show(int $id): UserResource
    {
        $user = $this->userRepository->find($id);
        return new UserResource($user);
    }

    public function search(): JsonResource
    {
        $users = $this->userRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->limit(request()->input('limit') ?? 10)
            ->all();

        return UserResource::collection($users);
    }

    public function store(): JsonResource
    {
        $data = $this->validate(request(), [
            'email'            => 'required|email|unique:users,email',
            'name'             => 'required',
            'multiatendedor_id'=> 'required',
            'password'         => 'nullable',
            'is_super'         => 'sometimes|boolean',
        ]);

        $data['status'] = $data['status'] ?? 1;

        $user = auth('sanctum')->user();
        if (!($user && $user->is_super)) {
            unset($data['is_super']); 
        }
        
        Event::dispatch('settings.user.create.before');

        $user = $this->userRepository->create($data);

        Event::dispatch('settings.user.create.after', $user);
        
        try {
            Mail::queue(new Create($user));
        } catch (\Exception $e) {
            report($e);
        }

        return new JsonResource([
            'data' => new UserResource($user),
            'message' => trans('rest-api::app.settings.users.create-success'),
        ]);
    }

    public function update(int $id): JsonResource
    {
        $data = $this->validate(request(), [
            'email'            => 'sometimes|required|email|unique:users,email,' . $id,
            'name'             => 'sometimes|required',
            'password'         => 'nullable',
            'multiatendedor_id'=> 'sometimes|required',
            'is_super'         => 'sometimes|required|boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $userFields = Arr::only($data, [
            'name', 'email', 'password', 'multiatendedor_id', 'is_super'
        ]);
        $user = $this->userRepository->update($userFields, $id);


        Event::dispatch('settings.user.update.after', $user);

        return new JsonResource([
            'data' => new UserResource($user),
            'message' => trans('rest-api::app.settings.users.updated-success'),
        ]);
    }

    public function destroy(int $id): JsonResource
    {
        if (auth()->guard()->user()->id == $id) {
            return new JsonResource(['message' => trans('rest-api::app.settings.users.delete-failed')], 400);
        }

        if ($this->userRepository->count() == 1) {
            return new JsonResource(['message' => trans('rest-api::app.settings.users.last-delete-error')], 400);
        }

        Event::dispatch('settings.user.delete.before', $id);

        try {
            $this->userRepository->delete($id);

            Event::dispatch('settings.user.delete.after', $id);

            return new JsonResource(['message' => trans('rest-api::app.settings.users.delete-success')]);
        } catch (\Exception $e) {
            return new JsonResource(['message' => $e->getMessage()], 500);
        }
    }

    public function massUpdate(MassUpdateRequest $request): JsonResource
    {
        $userIds = $request->input('indices');
        $count = 0;

        foreach ($userIds as $userId) {
            if (auth()->guard()->user()->id == $userId) {
                continue;
            }

            Event::dispatch('settings.user.update.before', $userId);

            $user = $this->userRepository->find($userId);

            $user?->update(['status' => $request->input('value')]);

            Event::dispatch('settings.user.update.after', $userId);

            $count++;
        }

        if (!$count) {
            return new JsonResource(['message' => trans('rest-api::app.settings.users.mass-update-failed')], 500);
        }

        return new JsonResource(['message' => trans('rest-api::app.settings.users.mass-update-success')]);
    }

    public function massDestroy(MassDestroyRequest $request): JsonResource
    {
        $userIds = $request->input('indices');
        $count = 0;

        foreach ($userIds as $userId) {
            if (auth()->guard()->user()->id == $userId) {
                continue;
            }

            Event::dispatch('settings.user.delete.before', $userId);

            $user = $this->userRepository->find($userId);

            $user?->delete();

            Event::dispatch('settings.user.delete.after', $userId);

            $count++;
        }

        if (!$count) {
            return new JsonResource(['message' => trans('rest-api::app.settings.users.mass-delete-failed')], 500);
        }

        return new JsonResource(['message' => trans('rest-api::app.settings.users.mass-delete-success')]);
    }
}