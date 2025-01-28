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
            $pdf = $parser->parseFile($pdfPath);
            $text = trim($pdf->getText());

            if (empty($text)) {
                throw new Exception('PDF content is empty or could not be extracted.');
            }

            return self::sendLLMRequest($text);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send a request to the LLM API.
     */
    private static function sendLLMRequest($prompt)
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $model = core()->getConfigData('general.magic_ai.settings.model');
        $apiKey = core()->getConfigData('general.magic_ai.settings.api_key');

        if (empty($apiKey) || empty($model)) {
            return ['error' => 'Missing API key or model configuration.'];
        }

        $data = [
            'model'    => $model,
            'messages' => [
                [
                    'role' => 'system', 'content' => 'You are an AI assistant. You have to extract the data from the PDF file. 
                    Example Output:
                    {
                        "status": 1,
                        "title": "Untitled Lead",
                        "person": {
                            "name": "Unknown",
                            "email": null,
                            "phone": null,
                            "organization_id": null
                        },
                        "lead_pipeline_stage_id": null,
                        "value": 0,
                        "source": "AI Extracted"
                    }
                        Note: Only return the output, Do not return or add any comments.',
                ],
                ['role' => 'user', 'content'   => 'PDF:\n'.$prompt],
            ],
        ];

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer '.$apiKey,
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
}
