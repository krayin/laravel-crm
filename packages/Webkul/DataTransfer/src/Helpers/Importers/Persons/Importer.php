<?php

namespace Webkul\DataTransfer\Helpers\Importers\Persons;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Attribute\Repositories\AttributeValueRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\DataTransfer\Contracts\ImportBatch as ImportBatchContract;
use Webkul\DataTransfer\Helpers\Import;
use Webkul\DataTransfer\Helpers\Importers\AbstractImporter;
use Webkul\DataTransfer\Repositories\ImportBatchRepository;

class Importer extends AbstractImporter
{
    /**
     * Error code for non existing email.
     */
    const ERROR_EMAIL_NOT_FOUND_FOR_DELETE = 'email_not_found_to_delete';

    /**
     * Error code for duplicated email.
     */
    const ERROR_DUPLICATE_EMAIL = 'duplicated_email';

    /**
     * Error code for duplicated phone.
     */
    const ERROR_DUPLICATE_PHONE = 'duplicated_phone';

    /**
     * Permanent entity columns.
     */
    protected array $validColumnNames = [
        'contact_numbers',
        'emails',
        'job_title',
        'name',
        'organization_id',
        'user_id',
    ];

    /**
     * Error message templates.
     */
    protected array $messages = [
        self::ERROR_EMAIL_NOT_FOUND_FOR_DELETE  => 'data_transfer::app.importers.persons.validation.errors.email-not-found',
        self::ERROR_DUPLICATE_EMAIL             => 'data_transfer::app.importers.persons.validation.errors.duplicate-email',
        self::ERROR_DUPLICATE_PHONE             => 'data_transfer::app.importers.persons.validation.errors.duplicate-phone',
    ];

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $permanentAttributes = ['emails'];

    /**
     * Permanent entity column.
     */
    protected string $masterAttributeCode = 'unique_id';

    /**
     * Emails storage.
     */
    protected array $emails = [];

    /**
     * Phones storage.
     */
    protected array $phones = [];

    /**
     * Create a new helper instance.
     *
     * @return void
     */
    public function __construct(
        protected ImportBatchRepository $importBatchRepository,
        protected PersonRepository $personRepository,
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
        protected Storage $personStorage,
    ) {
        parent::__construct(
            $importBatchRepository,
            $attributeRepository,
            $attributeValueRepository,
        );
    }

    /**
     * Initialize Product error templates.
     */
    protected function initErrorMessages(): void
    {
        foreach ($this->messages as $errorCode => $message) {
            $this->errorHelper->addErrorMessage($errorCode, trans($message));
        }

        parent::initErrorMessages();
    }

    /**
     * Validate data.
     */
    public function validateData(): void
    {
        $this->personStorage->init();

        parent::validateData();
    }

    /**
     * Validates row.
     */
    public function validateRow(array $rowData, int $rowNumber): bool
    {
        $rowData = $this->parsedRowData($rowData);

        /**
         * If row is already validated than no need for further validation.
         */
        if (isset($this->validatedRows[$rowNumber])) {
            return ! $this->errorHelper->isRowInvalid($rowNumber);
        }

        $this->validatedRows[$rowNumber] = true;

        /**
         * If import action is delete than no need for further validation.
         */
        if ($this->import->action == Import::ACTION_DELETE) {
            foreach ($rowData['emails'] as $email) {
                if (! $this->isEmailExist($email['value'])) {
                    $this->skipRow($rowNumber, self::ERROR_EMAIL_NOT_FOUND_FOR_DELETE, 'email');

                    return false;
                }

                return true;
            }
        }

        /**
         * Validate row data.
         */
        $validator = Validator::make($rowData, [
            ...$this->getValidationRules('persons', $rowData),
            'organization_id'         => 'required|exists:organizations,id',
            'user_id'                 => 'required|exists:users,id',
            'contact_numbers'         => 'required|array',
            'contact_numbers.*.value' => 'required|numeric',
            'contact_numbers.*.label' => 'required|in:home,work',
            'emails'                  => 'required|array',
            'emails.*.value'          => 'required|email',
            'emails.*.label'          => 'required|in:home,work',
        ]);

        if ($validator->fails()) {
            $failedAttributes = $validator->failed();

            foreach ($validator->errors()->getMessages() as $attributeCode => $message) {
                $errorCode = array_key_first($failedAttributes[$attributeCode] ?? []);

                $this->skipRow($rowNumber, $errorCode, $attributeCode, current($message));
            }
        }

        /**
         * Check if email is unique.
         */
        if (! empty($emails = $rowData['emails'])) {
            foreach ($emails as $email) {
                if (! in_array($email['value'], $this->emails)) {
                    $this->emails[] = $email['value'];
                } else {
                    $message = sprintf(
                        trans($this->messages[self::ERROR_DUPLICATE_EMAIL]),
                        $email['value']
                    );

                    $this->skipRow($rowNumber, self::ERROR_DUPLICATE_EMAIL, 'email', $message);
                }
            }
        }

        /**
         * Check if phone(s) are unique.
         */
        if (! empty($rowData['contact_numbers'])) {
            foreach ($rowData['contact_numbers'] as $phone) {
                if (! in_array($phone['value'], $this->phones)) {
                    if (! empty($phone['value'])) {
                        $this->phones[] = $phone['value'];
                    }
                } else {
                    $message = sprintf(
                        trans($this->messages[self::ERROR_DUPLICATE_PHONE]),
                        $phone['value']
                    );

                    $this->skipRow($rowNumber, self::ERROR_DUPLICATE_PHONE, 'phone', $message);
                }
            }
        }

        return ! $this->errorHelper->isRowInvalid($rowNumber);
    }

    /**
     * Start the import process.
     */
    public function importBatch(ImportBatchContract $batch): bool
    {
        Event::dispatch('data_transfer.imports.batch.import.before', $batch);

        if ($batch->import->action == Import::ACTION_DELETE) {
            $this->deletePersons($batch);
        } else {
            $this->savePersonData($batch);
        }

        /**
         * Update import batch summary.
         */
        $batch = $this->importBatchRepository->update([
            'state' => Import::STATE_PROCESSED,

            'summary'      => [
                'created' => $this->getCreatedItemsCount(),
                'updated' => $this->getUpdatedItemsCount(),
                'deleted' => $this->getDeletedItemsCount(),
            ],
        ], $batch->id);

        Event::dispatch('data_transfer.imports.batch.import.after', $batch);

        return true;
    }

    /**
     * Delete persons from current batch.
     */
    protected function deletePersons(ImportBatchContract $batch): bool
    {
        /**
         * Load person storage with batch emails.
         */
        $emails = collect(Arr::pluck($batch->data, 'emails'))
            ->map(function ($emails) {
                $emails = json_decode($emails, true);

                foreach ($emails as $email) {
                    return $email['value'];
                }
            });

        $this->personStorage->load($emails->toArray());

        $idsToDelete = [];

        foreach ($batch->data as $rowData) {
            $rowData = $this->parsedRowData($rowData);

            foreach ($rowData['emails'] as $email) {
                if (! $this->isEmailExist($email['value'])) {
                    continue;
                }

                $idsToDelete[] = $this->personStorage->get($email['value']);
            }
        }

        $idsToDelete = array_unique($idsToDelete);

        $this->deletedItemsCount = count($idsToDelete);

        $this->personRepository->deleteWhere([['id', 'IN', $idsToDelete]]);

        return true;
    }

    /**
     * Save person from current batch.
     */
    protected function savePersonData(ImportBatchContract $batch): bool
    {
        /**
         * Load person storage with batch email.
         */
        $emails = collect(Arr::pluck($batch->data, 'emails'))
            ->map(function ($emails) {
                $emails = json_decode($emails, true);

                foreach ($emails as $email) {
                    return $email['value'];
                }
            });

        $this->personStorage->load($emails->toArray());

        $persons = [];

        /**
         * Prepare persons for import.
         */
        foreach ($batch->data as $rowData) {
            $this->preparePersons($rowData, $persons);
        }

        $this->savePersons($persons);

        return true;
    }

    /**
     * Prepare persons from current batch.
     */
    public function preparePersons(array $rowData, array &$persons): void
    {
        $emails = collect($rowData['emails'])
            ->map(function ($emails) {
                $emails = json_decode($emails, true);

                foreach ($emails as $email) {
                    return $email['value'];
                }
            });

        foreach ($emails as $email) {
            $contactNumber = json_decode($rowData['contact_numbers'], true);

            $rowData['unique_id'] = "{$rowData['user_id']}|{$rowData['organization_id']}|{$email}|{$contactNumber[0]['value']}";

            if ($this->isEmailExist($email)) {
                $persons['update'][$email] = $rowData;
            } else {
                $persons['insert'][$email] = [
                    ...$rowData,
                    'created_at' => $rowData['created_at'] ?? now(),
                    'updated_at' => $rowData['updated_at'] ?? now(),
                ];
            }
        }
    }

    /**
     * Save persons from current batch.
     */
    public function savePersons(array $persons): void
    {
        if (! empty($persons['update'])) {
            $this->updatedItemsCount += count($persons['update']);

            $this->personRepository->upsert(
                $persons['update'],
                $this->masterAttributeCode,
            );
        }

        if (! empty($persons['insert'])) {
            $this->createdItemsCount += count($persons['insert']);

            $this->personRepository->insert($persons['insert']);
        }
    }

    /**
     * Check if email exists.
     */
    public function isEmailExist(string $email): bool
    {
        return $this->personStorage->has($email);
    }

    /**
     * Get parsed email and phone.
     */
    private function parsedRowData(array $rowData): array
    {
        $rowData['emails'] = json_decode($rowData['emails'], true);

        $rowData['contact_numbers'] = json_decode($rowData['contact_numbers'], true);

        return $rowData;
    }
}
