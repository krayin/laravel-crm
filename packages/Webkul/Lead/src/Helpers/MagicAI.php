<?php

namespace Webkul\Lead\Helpers;

use Illuminate\Support\Facades\Validator;
use Webkul\Admin\Http\Requests\LeadForm;

class MagicAI
{
    /**
     * Const Variable of LEAD_ENTITY.
     */
    const LEAD_ENTITY = 'leads';

    /**
     * Const Variable of PERSON_ENTITY.
     */
    const PERSON_ENTITY = 'persons';

    /**
     * Mapped the receive Extracted AI data.
     */
    public static function mapAIDataToLead($aiData)
    {
        if (! empty($aiData['error'])) {
            return self::errorHandler($aiData['error']);
        }

        $content = strip_tags($aiData['choices'][0]['message']['content']);

        if (empty($content)) {
            return self::errorHandler(trans('admin::app.leads.file.data-not-found'));
        }

        preg_match('/\{.*\}/s', $content, $matches);

        if (isset($matches[0])) {
            $jsonString = $matches[0];
        } else {
            return self::errorHandler(trans('admin::app.leads.file.invalid-response'));
        }

        $finalData = json_decode($jsonString);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return self::errorHandler(trans('admin::app.leads.file.invalid-format'));
        }

        try {
            self::validateLeadData($finalData);

            $validatedData = app(LeadForm::class)->validated();

            return array_merge($validatedData, self::prepareLeadData($finalData));
        } catch (\Exception $e) {
            return self::errorHandler($e->getMessage());
        }
    }

    /**
     * Validate the lead data.
     */
    private static function validateLeadData($data)
    {
        $dataArray = json_decode(json_encode($data), true);

        $validator = Validator::make($dataArray, [
            'title'                         => 'required|string|max:255',
            'lead_value'                    => 'required|numeric|min:0',
            'person.name'                   => 'required|string|max:255',
            'person.emails.value'           => 'required|email',
            'person.contact_numbers.value'  => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException(
                $validator,
                response()->json(self::errorHandler($validator->errors()->getMessages()), 400)
            );
        }

        return $data;
    }

    /**
     * Prepares the lead prompt data.
     */
    private static function prepareLeadData($finalData)
    {
        return [
            'status'              => 1,
            'title'               => $finalData->title ?? 'N/A',
            'description'         => $finalData->description ?? null,
            'lead_source_id'      => 1,
            'lead_type_id'        => 1,
            'lead_value'          => $finalData->lead_value ?? 0,
            'person'              => [
                'name'            => $finalData->person->name ?? 'Unknown',
                'emails'          => [
                    [
                        'value' => $finalData->person->emails->value ?? null,
                        'label' => $finalData->person->emails->label ?? 'work',
                    ],
                ],
                'contact_numbers' => [
                    [
                        'value' => $finalData->person->contact_numbers->value ?? null,
                        'label' => $finalData->person->contact_numbers->label ?? 'work',
                    ],
                ],
                'entity_type'     => self::PERSON_ENTITY,
            ],
            'entity_type'         => self::LEAD_ENTITY,
        ];
    }

    /**
     * Prepares method for error handler.
     */
    public static function errorHandler($message)
    {
        return [
            'status'  => 'error',
            'message' => $message,
        ];
    }
}
