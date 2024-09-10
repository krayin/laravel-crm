<?php

namespace Webkul\DataTransfer\Helpers\Importers\Leads;

use Webkul\Lead\Repositories\LeadRepository;

class Storage
{
    /**
     * Items contains identifier as key and product information as value.
     */
    protected array $items = [];

    /**
     * Columns which will be selected from database.
     */
    protected array $selectColumns = [
        'id',
        'title',
    ];

    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(protected LeadRepository $leadRepository) {}

    /**
     * Initialize storage.
     */
    public function init(): void
    {
        $this->items = [];

        $this->load();
    }

    /**
     * Load the leads.
     */
    public function load(array $titles = []): void
    {
        if (empty($titles)) {
            $leads = $this->leadRepository->all($this->selectColumns);
        } else {
            $leads = $this->leadRepository->findWhereIn('title', $titles, $this->selectColumns);
        }

        foreach ($leads as $lead) {
            $this->set($lead->title, $lead->id);
        }
    }

    /**
     * Get Ids and Unique Id.
     */
    public function set(string $uniqueId, int $id): self
    {
        $this->items[$uniqueId] = $id;

        return $this;
    }

    /**
     * Check if unique id exists.
     */
    public function has(string $uniqueId): bool
    {
        return isset($this->items[$uniqueId]);
    }

    /**
     * Get unique id information.
     */
    public function get(string $uniqueId): ?int
    {
        if (! $this->has($uniqueId)) {
            return null;
        }

        return $this->items[$uniqueId];
    }

    /**
     * Is storage is empty.
     */
    public function isEmpty(): int
    {
        return empty($this->items);
    }
}
