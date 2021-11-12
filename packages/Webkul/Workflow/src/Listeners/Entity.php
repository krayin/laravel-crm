<?php

namespace Webkul\Workflow\Listeners;

use Webkul\Workflow\Repositories\WorkflowRepository;
use Webkul\Workflow\Helpers\Validator;

class Entity
{
    /**
     * WorkflowRepository object
     *
     * @var \Webkul\Workflow\Repositories\WorkflowRepository
     */
    protected $workflowRepository;

    /**
     * Validator object
     *
     * @var \Webkul\Workflow\Helpers\Validator
     */
    protected $validator;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Workflow\Repositories\WorkflowRepository  $workflowRepository
     * @param  \Webkul\Workflow\Helpers\Validator  $validator
     * @return void
     */
    public function __construct(
        WorkflowRepository $workflowRepository,
        Validator $validator
    )
    {
        $this->workflowRepository = $workflowRepository;

        $this->validator = $validator;
    }

    /**
     * @param  string  $eventName
     * @param  mixed  $entity
     * @return void
     */
    public function process($eventName, $entity)
    {
        $workflows = $this->workflowRepository->findByField('event', $eventName);

        foreach ($workflows as $workflow) {
            $workflowEntity = app(config('workflows.trigger_entities.' . $workflow->entity_type . '.class'));

            $entity = $workflowEntity->getEntity($entity);

            if (! $this->validator->validate($workflow, $entity)) {
                continue;
            }

            try {
                $workflowEntity->executeActions($workflow, $entity);
            } catch (\Exception $e) {
                // Skip exception for now
            }
        }
    }
}