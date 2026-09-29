<?php

namespace Webkul\GoogleContact\Services;

use Google\Service\PeopleService;
use Google\Service\PeopleService\BatchCreateContactsRequest;
use Google\Service\PeopleService\ContactToCreate;
use Google\Service\PeopleService\Person as GooglePerson;
use Webkul\Contact\Models\Person;
use Webkul\GoogleContact\Models\GoogleContactAccount;

class GoogleContactsService
{
    /**
     * Maximum contacts Google's `people:batchCreateContacts` accepts per call.
     */
    public const BATCH_LIMIT = 200;

    public function __construct(
        protected TokenRefresher $tokenRefresher,
    ) {}

    /**
     * Fetch the lowercased email addresses of every contact already in the
     * user's Google Contacts, used to skip duplicates before creating anything.
     */
    public function listExistingEmails(GoogleContactAccount $account): array
    {
        $service = new PeopleService($this->tokenRefresher->clientFor($account));

        $emails = [];
        $pageToken = null;

        do {
            $response = $service->people_connections->listPeopleConnections('people/me', array_filter([
                'personFields' => 'emailAddresses',
                'pageSize' => 1000,
                'pageToken' => $pageToken,
            ]));

            foreach ($response->getConnections() ?? [] as $person) {
                foreach ($person->getEmailAddresses() ?? [] as $emailAddress) {
                    if ($value = $emailAddress->getValue()) {
                        $emails[strtolower($value)] = true;
                    }
                }
            }

            $pageToken = $response->getNextPageToken();
        } while ($pageToken);

        return array_keys($emails);
    }

    /**
     * Create up to `BATCH_LIMIT` contacts in a single Google API call.
     *
     * @param  Person[]  $persons
     * @return array<int, array{success: bool, resource_name: ?string, error: ?string}> keyed
     *                                                                                  in the same order as $persons
     */
    public function batchCreateContacts(GoogleContactAccount $account, array $persons): array
    {
        $service = new PeopleService($this->tokenRefresher->clientFor($account));

        $request = new BatchCreateContactsRequest([
            'readMask' => 'emailAddresses',
            'contacts' => array_map(
                fn (Person $person) => new ContactToCreate([
                    'contactPerson' => $this->mapPersonToGooglePerson($person),
                ]),
                $persons
            ),
        ]);

        $response = $service->people->batchCreateContacts($request);

        $results = [];

        foreach ($response->getCreatedPeople() ?? [] as $personResponse) {
            $status = $personResponse->getStatus();

            if ($status && $status->getCode()) {
                $results[] = [
                    'success' => false,
                    'resource_name' => null,
                    'error' => $status->getMessage(),
                ];
            } else {
                $results[] = [
                    'success' => true,
                    'resource_name' => $personResponse->getPerson()?->getResourceName(),
                    'error' => null,
                ];
            }
        }

        return $results;
    }

    /**
     * Map a CRM Person to a Google People API `person` resource.
     */
    public function mapPersonToGooglePerson(Person $person): GooglePerson
    {
        [$givenName, $familyName] = $this->splitName($person->name);

        $data = [
            'names' => [[
                'givenName' => $givenName,
                'familyName' => $familyName,
                'displayName' => $person->name,
            ]],
        ];

        if ($emails = $person->emails) {
            $data['emailAddresses'] = array_values(array_filter(array_map(
                fn ($email) => ($value = is_array($email) ? ($email['value'] ?? null) : $email)
                    ? ['value' => $value, 'type' => 'work']
                    : null,
                $emails
            )));
        }

        if ($numbers = $person->contact_numbers) {
            $data['phoneNumbers'] = array_values(array_filter(array_map(
                fn ($number) => ($value = is_array($number) ? ($number['value'] ?? null) : $number)
                    ? ['value' => $value, 'type' => 'work']
                    : null,
                $numbers
            )));
        }

        if ($person->job_title || $person->organization) {
            $data['organizations'] = [[
                'title' => $person->job_title,
                'name' => $person->organization?->name,
            ]];
        }

        return new GooglePerson($data);
    }

    /**
     * Split a full name into given/family name, since Person only stores a single `name` field.
     */
    protected function splitName(string $name): array
    {
        $parts = explode(' ', trim($name), 2);

        return [$parts[0] ?: $name, $parts[1] ?? ''];
    }
}
