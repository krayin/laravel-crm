<?php

namespace Webkul\Admin\Helpers;

use Smalot\PdfParser\Parser;
use Exception;

class Lead
{
    /**
     * Extract data from a PDF and process it via LLM API.
     */
    public static function extractDataFromPdf($pdfPath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = trim($pdf->getText());

            if (empty($text)) {
                throw new Exception("PDF content is empty or could not be extracted.");
            }

            return self::sendLLMRequest($text);
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /**
     * Send a request to the LLM API.
     */
    private static function sendLLMRequest($prompt)
    {
        $url = "https://api.openai.com/v1/chat/completions";

        $model = core()->getConfigData('general.magic_ai.settings.model');
        $apiKey = core()->getConfigData('general.magic_ai.settings.api_key');

        if (empty($apiKey) || empty($model)) {
            return ["error" => "Missing API key or model configuration."];
        }

        $data = [
            "model" => $model,
            "messages" => [
                ["role" => "system", "content" => "You are an AI assistant."],
                ["role" => "user", "content" => $prompt]
            ],
            "max_tokens" => 200
        ];

        try {
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer " . $apiKey
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new Exception("cURL Error: " . curl_error($ch));
            }

            curl_close($ch);

            $decodedResponse = json_decode($response, true);

            if ($httpCode !== 200 || isset($decodedResponse['error'])) {
                throw new Exception("LLM API Error: " . ($decodedResponse['error']['message'] ?? 'Unknown error'));
            }

            return $decodedResponse;
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
}
