<?php

namespace Webkul\Lead\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Webkul\Core\Eloquent\Repository;

class PipelineRepository extends Repository
{
    /**
     * StageRepository object
     *
     * @var \Webkul\Lead\Repositories\StageRepository
     */
    protected $stageRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Lead\Repositories\StageRepository  $stageRepository
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        StageRepository $stageRepository,
        Container $container
    )
    {
        $this->stageRepository = $stageRepository;

        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Lead\Contracts\Pipeline';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Lead\Contracts\Pipeline
     */
    public function create(array $data)
    {
        $this->model->query()->update(['is_default' => 0]);

        $pipeline = $this->model->create($data);

        foreach ($data['stages'] as $stageData) {
            $this->stageRepository->create(array_merge([
                'lead_pipeline_id' => $pipeline->id,
            ], $stageData));
        }

        return $pipeline;
    }

    /**
     * @param  array  $data
     * @param  int $id
     * @param  string  $pipeline
     * @return \Webkul\Lead\Contracts\Pipeline
     */
    public function update(array $data, $id, $pipeline = "id")
    {
        $pipeline = $this->find($id);

        $this->model->query()->where('id', '<>', $id)->update(['is_default' => 0]);

        $pipeline->update($data);

        $previousStageIds = $pipeline->stages()->pluck('id');

        foreach ($data['stages'] as $stageId => $stageData) {
            if (Str::contains($stageId, 'stage_')) {
                $this->stageRepository->create(array_merge([
                    'lead_pipeline_id' => $pipeline->id,
                ], $stageData));
            } else {
                if (is_numeric($index = $previousStageIds->search($stageId))) {
                    $previousStageIds->forget($index);
                }

                $this->stageRepository->update($stageData, $stageId);
            }
        }

        foreach ($previousStageIds as $stageId) {
            $this->stageRepository->delete($stageId);
        }

        return $pipeline;
    }

    /**
     * Return the default pipeline
     * 
     * @return \Webkul\Lead\Contracts\Pipeline
     */
    public function getDefaultPipeline()
    {
        $pipeline = $this->pipelineRepository->findOneByField('is_default', 1);

        if (! $pipeline) {
            $pipeline = $this->pipelineRepository->first();
        }

        return $pipeline;
    }
}