<?php

namespace Webkul\Lead\Services;

use Exception;

class GeminiService
{
    /**
     * Send Request to Gemini AI.
     */
    public static function ask($prompt, $model, $apiKey)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        return self::sendHttpRequest($url, self::prepareLeadExtractionRequestData($prompt));
    }

    /**
     * Request data to AI using Curl API.
     */
    private static function sendHttpRequest($url, array $data)
    {
        try {
            $response = \Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            if ($response->failed()) {
                throw new Exception($response->json('error.message'));
            }

            return $response->json();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Prepare request data for AI.
     */
    private static function prepareLeadExtractionRequestData($prompt)
    {
        return [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => "You are an AI assistant. Extract data from the provided PDF text.
        
                            Example Output:
                            {
                                \"status\": 1,
                                \"title\": \"Untitled Lead\",
                                \"person\": {
                                    \"name\": \"Unknown\",
                                    \"emails\": {
                                        \"value\": null,
                                        \"label\": null
                                    },
                                    \"contact_numbers\": {
                                        \"value\": null,
                                        \"label\": null
                                    }
                                },
                                \"lead_pipeline_stage_id\": null,
                                \"lead_value\": 0,
                                \"source\": \"AI Extracted\"
                            }
        
                            Note: Only return the output. Do not return or add any comments.
        
                            PDF Content:
                            $prompt",
                        ],
                    ],
                    'role' => 'user',
                ],
            ],
            'generationConfig' => [
                'temperature'     => 0.2,
                'topK'            => 30,
                'topP'            => 0.8,
                'maxOutputTokens' => 512,
            ],
        ];
    }
}
