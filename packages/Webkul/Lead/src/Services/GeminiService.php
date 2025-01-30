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

        return self::curlRequest($url, self::prepareRequestData($prompt));
    }

    /**
     * Request data to AI using Curl API.
     */
    private static function curlRequest($url, array $data)
    {
        try {
            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new Exception('cURL Error: '.curl_error($ch));
            }

            curl_close($ch);

            $decodedResponse = json_decode($response, true);

            if ($httpCode !== 200 || isset($decodedResponse['error'])) {
                throw new Exception('LLM API Error: '.($decodedResponse['error']['message'] ?? 'Unknown error'));
            }

            return $decodedResponse;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Prepare request data for AI.
     */
    private static function prepareRequestData($prompt)
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
