<?php

namespace Webkul\Lead\Services;

use Exception;

class OpenAIService
{
    const OPEN_AI_MODEL_URL = 'https://api.openai.com/v1/chat/completions';

    /**
     * Send a request to the LLM API.
     */
    public static function ask($prompt, $model)
    {
        $apiDomain = core()->getConfigData('general.magic_ai.settings.api_domain');

        $apiUrlMap = [
            'gpt-4o'          => self::OPEN_AI_MODEL_URL,
            'gpt-4o-mini'     => self::OPEN_AI_MODEL_URL,
            'llama3.2:latest' => "$apiDomain/v1/chat/completions",
            'deepseek-r1:8b'  => "$apiDomain/v1/chat/completions",
        ];

        $apiUrl = $apiUrlMap[$model] ?? 'https://api.groq.com/openai/v1/chat/completions';

        return self::curlRequest($apiUrl, self::prepareRequestData($model, $prompt));
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
                    'Authorization: Bearer '.core()->getConfigData('general.magic_ai.settings.api_key'),
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
    private static function prepareRequestData($model, $prompt)
    {
        return [
            'model'    => $model,
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are an AI assistant. You have to extract the data from the PDF file. 
                    Example Output:
                    {
                        "status": 1,
                        "title": "Untitled Lead",
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
                        },
                        "lead_pipeline_stage_id": null,
                        "lead_value": 0,
                        "source": "AI Extracted"
                    }
                    Note: Only return the output, Do not return or add any comments.',
                ],
                ['role' => 'user', 'content' => "PDF:\n$prompt"],
            ],
        ];
    }
}
