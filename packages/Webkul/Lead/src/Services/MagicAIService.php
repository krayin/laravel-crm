<?php

namespace Webkul\Lead\Services;

use Exception;
use Smalot\PdfParser\Parser;

class MagicAIService
{
    /**
     * API endpoint for OpenRouter AI service.
     */
    const OPEN_ROUTER_URL = 'https://openrouter.ai/api/v1/chat/completions';

    /**
     * Maximum token limit for AI prompt.
     */
    const MAX_TOKENS = 100000;

    /**
     * Flag to prevent re-entrant calls.
     */
    private static $isExtracting = false;

    /**
     * Extract data from base64-encoded file.
     */
    public static function extractDataFromFile($base64File)
    {
        if (self::$isExtracting) {
            throw new Exception(trans('admin::app.leads.file.recursive-call'));
        }

        self::$isExtracting = true;

        try {
            $text = self::extractTextFromBase64File($base64File);

            if (empty($text)) {
                throw new Exception(trans('admin::app.leads.file.failed-extract'));
            }

            return self::processPromptWithAI($text);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        } finally {
            self::$isExtracting = false;
        }
    }

    /**
     * Extract text from base64-encoded file.
     */
    private static function extractTextFromBase64File($base64File)
    {
        if (
            empty($base64File)
            || ! base64_decode($base64File, true)
        ) {
            throw new Exception(trans('admin::app.leads.file.invalid-base64'));
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'file_');

        file_put_contents($tempFile, self::handleBase64($base64File, 'decode'));

        $mimeType = mime_content_type($tempFile);

        $data = [];

        try {
            if ($mimeType === 'application/pdf') {
                $pdfParser = (new Parser)->parseFile($tempFile);

                $data['text'] = $pdfParser->getText();

                $data['images'][] = '';

                $images = $pdfParser->getObjectsByType('XObject', 'Image');

                foreach ($images as $image) {
                    $data['images'][] = self::handleBase64($image->getContent());
                }
            } else {
                $data['text'] = '';

                $data['images'][] = self::handleBase64(self::handleBase64($base64File, 'decode'));
            }

            if (empty($data)) {
                throw new Exception(trans('admin::app.leads.file.data-extraction-failed'));
            }

            return $data;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        } finally {
            @unlink($tempFile);
        }
    }

    /**
     * Send extracted data to AI for processing.
     */
    private static function processPromptWithAI($prompt)
    {
        $model = core()->getConfigData('general.magic_ai.settings.other_model') ?: core()->getConfigData('general.magic_ai.settings.model');

        $apiKey = core()->getConfigData('general.magic_ai.settings.api_key');

        if (! $apiKey || ! $model) {
            return ['error' => trans('admin::app.leads.file.missing-api-key')];
        }

        $promptText = self::truncatePrompt($prompt['text'] ?? '');

        $promptImages = $prompt['images'] ?? [];

        $prompt = array_filter(array_merge([$promptText], $promptImages), function ($value) {
            return ! empty($value);
        });

        return self::ask(array_values($prompt), $model, $apiKey);
    }

    /**
     * Truncate prompt to fit within token limit.
     */
    private static function truncatePrompt($prompt)
    {
        if (strlen($prompt) > self::MAX_TOKENS) {
            $start = mb_substr($prompt, 0, self::MAX_TOKENS * 0.4);

            $end = mb_substr($prompt, -self::MAX_TOKENS * 0.4);

            return $start."\n...\n".$end;
        }

        return $prompt;
    }

    /**
     * Send prompt request to AI for processing.
     */
    private static function ask($prompt, $model, $apiKey)
    {
        try {
            $response = \Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.$apiKey,
            ])->post(self::OPEN_ROUTER_URL, [
                'model'    => $model,
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => self::getSystemPrompt(),
                    ], [
                        'role'    => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt[0],
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->failed()) {
                throw new Exception($response->body());
            }

            $data = $response->json();

            if (isset($data['error'])) {
                throw new Exception($data['error']['message']);
            }

            return $data;
        } catch (Exception $e) {
            return ['error' => trans('admin::app.leads.file.insufficient-info')];
        }
    }

    /**
     * Define system prompt for AI processing.
     *
     * @return string System prompt for AI model.
     */
    private static function getSystemPrompt()
    {
        return <<<'PROMPT'
            You are an AI assistant specialized in extracting structured data from text.  
            The user will provide text extracted from a file (in Base64 or plain text).  
            Your task is to accurately extract the following fields. If the value is not available, use the default values provided:  

            ### **Output Format:** 
            ```json
            {
                "status": 1,
                "title": "Untitled Lead",
                "lead_value": 0,
                "person": {
                    "name": "Unknown",
                    "emails": {
                        "value": null,
                        "label": null
                    },
                    "contact_numbers": {
                        "value": null,
                        "label": null
                    }
                }
            }
            ```
            ### **Fields to Extract:**
            - **Title:** Title of the lead. Default: "Untitled Lead"
            - **Lead Value:** Value of the lead. Default: 0
            - **Person Name:** Name of the person. Default: "Unknown"
            - **Person Email:** Email of the person. Default: null
            - **Person Email Label:** Label for the email. Default: null
            - **Person Contact Number:** Contact number of the person. Default: null
            - **Person Contact Number Label:** Label for the contact number. Default: null
        PROMPT;
    }

    /**
     * process for encoding and decoding base64 data.
     */
    private static function handleBase64($base64, string $type = 'encode')
    {
        if ($type === 'encode') {
            return base64_encode($base64);
        }

        return base64_decode($base64);
    }
}
