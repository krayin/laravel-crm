<?php

namespace Webkul\Workflow\Listeners;

use Webkul\Workflow\Repositories\WorkflowRepository;
use Webkul\Workflow\Helpers\Validator;

class Entity
{
    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected WorkflowRepository $workflowRepository,
        protected Validator $validator
    ) {
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