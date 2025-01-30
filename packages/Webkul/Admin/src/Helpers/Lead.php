<?php

namespace Webkul\Admin\Helpers;

use Exception;
use Smalot\PdfParser\Parser;

class Lead
{
    /**
     * Extract data from a PDF and process it via LLM API.
     */
    public static function extractDataFromPdf($pdfPath)
    {
        try {
            $parser = new Parser;

            $pdfText = trim($parser->parseFile($pdfPath)->getText());

            if (empty($pdfText)) {
                throw new Exception('PDF content is empty or could not be extracted.');
            }

            return self::sendLLMRequest($pdfText);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send a request to the LLM API.
     */
    private static function sendLLMRequest($prompt)
    {
        $model = core()->getConfigData('general.magic_ai.settings.model');
        $apiKey = core()->getConfigData('general.magic_ai.settings.api_key');
        $apiDomain = core()->getConfigData('general.magic_ai.settings.api_domain');

        if (! $apiKey || ! $model) {
            return ['error' => 'Missing API key or model configuration.'];
        }

        if (str_contains($model, 'gemini-2.0-flash')) {
            return self::sendGeminiRequest($prompt, $model);
        }

        $apiUrlMap = [
            'gpt-4o'          => 'https://api.openai.com/v1/chat/completions',
            'gpt-4o-mini'     => 'https://api.openai.com/v1/chat/completions',
            'llama3.2:latest' => "$apiDomain/v1/chat/completions",
            'deepseek-r1:8b'  => "$apiDomain/v1/chat/completions",
        ];

        $apiUrl = $apiUrlMap[$model] ?? 'https://api.groq.com/openai/v1/chat/completions';

        return self::makeCurlRequest($apiUrl, $model, self::prepareRequestData($model, $prompt));
    }

    /**
     * Send Request to Gemini AI.
     */
    private static function sendGeminiRequest($prompt, $model)
    {
        $apiKey = core()->getConfigData('general.magic_ai.settings.api_key');

        if (empty($apiKey)) {
            return ['error' => 'Missing Google API key.'];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $data = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => 'You are an AI assistant. You have to extract the data from the PDF file.
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
                            Note: Only return the output, Do not return or add any comments.'
                        ]
                    ],
                    "role" => "system"
                ],
                [
                    "parts" => [
                        ["text" => "PDF:\n$prompt"]
                    ],
                    "role" => "user"
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.2,
                "topK" => 30,
                "topP" => 0.8,
                "maxOutputTokens" => 512
            ]
        ];

        return self::makeCurlRequest($url, $model, $data);
    }

    /**
     * Request data to AI using Curl API.
     */
    private static function makeCurlRequest($url, $model, array $data)
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
