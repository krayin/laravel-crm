<?php

namespace Webkul\Lead\Services;

use Exception;
use Smalot\PdfParser\Parser;

class MagicAIService
{
    /**
     * Const variable
     */
    const OPEN_ROUTER_URL = 'https://openrouter.ai/api/v1/chat/completions';

    /**
     * Extract data from a PDF and process it via LLM API.
     */
    public static function extractDataFromPdf($pdfPath)
    {
        try {
            $parser = new Parser;

            if (empty($pdfText = trim($parser->parseFile($pdfPath)->getText()))) {
                throw new Exception(trans('admin::app.leads.file.empty-content'));
            }

            return self::processPromptWithAI($pdfText);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send a request to the LLM API.
     */
    private static function processPromptWithAI($prompt)
    {
        $model = core()->getConfigData('general.magic_ai.settings.model');
        $apiKey = core()->getConfigData('general.magic_ai.settings.api_key');

        if (! $apiKey || ! $model) {
            return ['error' => trans('admin::app.leads.file.missing-api-key')];
        }

        return self::ask($prompt, $model, $apiKey);
    }

    /**
     * Send a request to the Open Router API.
     */
    private static function ask($prompt, $model, $apiKey)
    {
        $response = \Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer '.$apiKey,
        ])->post(self::OPEN_ROUTER_URL, [
            'model'    => $model,
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are an AI assistant. You have to extract the data from the PDF file. 
                    Example Output:
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
                    }',
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        return $response->json();
    }
}
