<?php

namespace Webkul\Lead\Services;

use Exception;
use Webkul\Lead\Helpers\Lead;

class OpenAIService
{
    /**
     * Send a request to the LLM API.
     */
    public static function ask($prompt, $model)
    {
        $apiUrl = self::getApiUrlForModel($model);

        return self::sendHttpRequest($apiUrl, self::prepareLeadExtractionRequestData($model, $prompt));
    }

    /**
     * Request data to AI using Curl API.
     */
    private static function sendHttpRequest($url, array $data)
    {
        try {
            $response = \Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer '.core()->getConfigData('general.magic_ai.settings.api_key'),
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
    private static function prepareLeadExtractionRequestData($model, $prompt)
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

    /**
     * Get API Url for the model.
     */
    private static function getApiUrlForModel($model)
    {
        $apiDomain = core()->getConfigData('general.magic_ai.settings.api_domain');

        $apiUrlMap = [
            'gpt-4o'          => Lead::OPEN_AI_MODEL_URL,
            'gpt-4o-mini'     => Lead::OPEN_AI_MODEL_URL,
            'llama3.2:latest' => "$apiDomain/v1/chat/completions",
            'deepseek-r1:8b'  => "$apiDomain/v1/chat/completions",
        ];

        return $apiUrlMap[$model] ?? 'https://api.groq.com/openai/v1/chat/completions';
    }
}
