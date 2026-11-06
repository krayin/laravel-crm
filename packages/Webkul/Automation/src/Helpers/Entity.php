<?php

namespace Webkul\Automation\Helpers;

use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;

class Entity
{
    /**
     * Placeholders built for the current request.
     */
    protected ?array $emailTemplatePlaceholders = null;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected EmailTemplateRepository $emailTemplateRepository
    ) {}

    /**
     * Returns events to match for the entity
     *
     * @return array
     */
    public function getEvents()
    {
        $entities = config('workflows.trigger_entities');

        $events = [];

        foreach ($entities as $key => $entity) {
            $object = app($entity['class']);

            $events[$key] = [
                'id' => $key,
                'name' => $entity['name'],
                'events' => $entity['events'],
            ];
        }

        return $events;
    }

    /**
     * Returns conditions to match for the entity
     *
     * @return array
     */
    public function getConditions()
    {
        $entities = config('workflows.trigger_entities');

        $conditions = [];

        foreach ($entities as $key => $entity) {
            $object = app($entity['class']);

            $conditions[$key] = $object->getConditions();
        }

        return $conditions;
    }

    /**
     * Returns workflow actions
     *
     * @return array
     */
    public function getActions()
    {
        $entities = config('workflows.trigger_entities');

        $conditions = [];

        foreach ($entities as $key => $entity) {
            $object = app($entity['class']);

            $conditions[$key] = $object->getActions();
        }

        return $conditions;
    }

    /**
     * Returns placeholders for email templates
     *
     * @return array
     */
    public function getEmailTemplatePlaceholders()
    {
        if ($this->emailTemplatePlaceholders !== null) {
            return $this->emailTemplatePlaceholders;
        }

        $entities = config('workflows.trigger_entities');

        $placeholders = [];

        foreach ($entities as $key => $entity) {
            $object = app($entity['class']);

            $placeholders[] = $object->getEmailTemplatePlaceholders($entity);
        }

        return $this->emailTemplatePlaceholders = $placeholders;
    }
}
