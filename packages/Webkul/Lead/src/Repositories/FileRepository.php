<?php

namespace Webkul\Lead\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;

class FileRepository extends Repository
{
    /**
     * ActivityRepository object
     *
     * @var \Webkul\Lead\Repositories\ActivityRepository
     */
    protected $activityRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Lead\Repositories\ActivityRepository  $activityRepository
     * @return void
     */
    public function __construct(
        ActivityRepository $activityRepository,
        Container $container
    )
    {
        $this->activityRepository = $activityRepository;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Lead\Contracts\File';
    }

    /**
     * @param array  $data
     * @param int    $id
     * @return mixed|void
     */
    public function upload(array $data, $id)
    {
        if (! request()->hasFile('file')) {
            return;
        }
        
        $leadActivity = $this->activityRepository->create([
            'is_done' => 1,
            'type'    => 'file',
            'user_id' => auth()->guard('user')->user()->id,
            'lead_id' => $id,
        ]);

        return parent::create([
            'name'             => $data['name'] ?? request()->file('file')->getClientOriginalName(),
            'path'             => request()->file('file')->store('leads/' . $id),
            'lead_id'          => $id,
            'lead_activity_id' => $leadActivity->id,
        ]);
    }
}