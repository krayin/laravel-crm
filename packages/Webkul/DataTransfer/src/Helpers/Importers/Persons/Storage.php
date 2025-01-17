<?php

namespace Webkul\DataTransfer\Helpers\Importers\Persons;

use Webkul\Contact\Repositories\PersonRepository;

class Storage
{
    /**
     * Items contains email as key and product information as value.
     */
    protected array $items = [];

    /**
     * Columns which will be selected from database.
     */
    protected array $selectColumns = [
        'id',
        'emails',
    ];

    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(protected PersonRepository $personRepository) {}

    /**
     * Initialize storage.
     */
    public function init(): void
    {
        $this->items = [];

        $this->load();
    }

    /**
     * Load the Emails.
     */
    public function load(array $emails = []): void
    {
        if (empty($emails)) {
            $persons = $this->personRepository->all($this->selectColumns);
        } else {
            $persons = $this->personRepository->scopeQuery(function ($query) use ($emails) {
                return $query->where(function ($subQuery) use ($emails) {
                    foreach ($emails as $email) {
                        $subQuery->orWhereJsonContains('emails', ['value' => $email]);
                    }
                });
            })->all($this->selectColumns);
        }

        $persons->each(function ($person) {
            collect($person->emails)
                ->each(fn ($email) => $this->set($email['value'], $person->id));
        });
    }

    /**
     * Get email information.
     */
    public function set(string $email, int $id): self
    {
        $this->items[$email] = $id;

        return $this;
    }

    /**
     * Check if email exists.
     */
    public function has(string $email): bool
    {
        return isset($this->items[$email]);
    }

    /**
     * Get email information.
     */
    public function get(string $email): ?int
    {
        if (! $this->has($email)) {
            return null;
        }

        return $this->items[$email];
    }

    /**
     * Is storage is empty.
     */
    public function isEmpty(): int
    {
        return empty($this->items);
    }
}
