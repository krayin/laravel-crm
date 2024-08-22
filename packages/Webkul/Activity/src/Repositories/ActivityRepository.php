<?php

namespace Webkul\Activity\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;

class ActivityRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected FileRepository $fileRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Activity\Contracts\Activity';
    }

    /**
     * Create pipeline.
     *
     * @return \Webkul\Activity\Contracts\Activity
     */
    public function create(array $data)
    {
        $activity = parent::create($data);

        if (isset($data['file'])) {
            $this->fileRepository->create([
                'name'        => $data['name'] ?? $data['file']->getClientOriginalName(),
                'path'        => $data['file']->store('activities/'.$activity->id),
                'activity_id' => $activity->id,
            ]);
        }

        if (! isset($data['participants'])) {
            return $activity;
        }

        foreach ($data['participants']['users'] ?? [] as $userId) {
            $activity->participants()->create([
                'user_id' => $userId,
            ]);
        }

        foreach ($data['participants']['persons'] ?? [] as $personId) {
            $activity->participants()->create([
                'person_id' => $personId,
            ]);
        }

        return $activity;
    }

    /**
     * Update pipeline.
     *
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Activity\Contracts\Activity
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        $activity = parent::update($data, $id);

        if (isset($data['participants'])) {
            $activity->participants()->delete();

            foreach ($data['participants']['users'] ?? [] as $userId) {
                $activity->participants()->create([
                    'user_id' => $userId,
                ]);
            }

            foreach ($data['participants']['persons'] ?? [] as $personId) {
                $activity->participants()->create([
                    'person_id' => $personId,
                ]);
            }
        }

        return $activity;
    }

    /**
     * @param  string  $dateRange
     * @return mixed
     */
    public function getActivities($dateRange)
    {
        return $this->select(
            'activities.id',
            'activities.created_at',
            'activities.title',
            'activities.schedule_from as start',
            'activities.schedule_to as end',
            'users.name as user_name',
        )
            ->addSelect(\DB::raw('IF(activities.is_done, "done", "") as class'))
            ->leftJoin('activity_participants', 'activities.id', '=', 'activity_participants.activity_id')
            ->leftJoin('users', 'activities.user_id', '=', 'users.id')
            ->whereIn('type', ['call', 'meeting', 'lunch'])
            ->whereBetween('activities.schedule_from', $dateRange)
            ->where(function ($query) {
                if ($userIds = bouncer()->getAuthorizedUserIds()) {
                    $query->whereIn('activities.user_id', $userIds)
                        ->orWhereIn('activity_participants.user_id', $userIds);
                }
            })
            ->distinct()
            ->get();
    }

    /**
     * @param  string  $startFrom
     * @param  string  $endFrom
     * @param  array  $participants
     * @param  int  $id
     * @return bool
     */
    public function isDurationOverlapping($startFrom, $endFrom, $participants, $id)
    {
        $queryBuilder = $this->leftJoin('activity_participants', 'activities.id', '=', 'activity_participants.activity_id')
            ->where(function ($query) use ($startFrom, $endFrom) {
                $query->where([
                    ['activities.schedule_from', '<=', $startFrom],
                    ['activities.schedule_to', '>=', $startFrom],
                ])->orWhere([
                    ['activities.schedule_from', '>=', $startFrom],
                    ['activities.schedule_from', '<=', $endFrom],
                ]);
            })
            ->where(function ($query) use ($participants) {
                if (is_null($participants)) {
                    return;
                }

                if (isset($participants['users'])) {
                    $query->orWhereIn('activity_participants.user_id', $participants['users']);
                }

                if (isset($participants['persons'])) {
                    $query->orWhereIn('activity_participants.person_id', $participants['persons']);
                }
            })
            ->groupBy('activities.id');

        if (! is_null($id)) {
            $queryBuilder->where('activities.id', '!=', $id);
        }

        return $queryBuilder->count() ? true : false;
    }
}
