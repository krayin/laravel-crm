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
    protected array $selectColumns = ['id', 'title'];

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
            $this->set($lead->title, [
                'id'    => $lead->id,
                'title' => $lead->title,
            ]);
        }
    }

    /**
     * Get Ids and Unique Id.
     */
    public function set(string $title, array $data): self
    {
        $this->items[$title] = $data;

        return $this;
    }

    /**
     * Check if unique id exists.
     */
    public function has(string $title): bool
    {
        return isset($this->items[$title]);
    }

    /**
     * Get unique id information.
     */
    public function get(string $title): ?array
    {
        if (! $this->has($title)) {
            return null;
        }

        return $this->items[$title];
    }

    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Is storage is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}
