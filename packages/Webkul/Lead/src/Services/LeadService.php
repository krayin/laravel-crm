<?php

namespace Webkul\Lead\Services;

use Exception;
use Smalot\PdfParser\Parser;

class LeadService
{
    /**
     * Const variable
     */
    const GEMINI_MODEL = 'gemini-1.5-flash';

    const OPEN_AI_MODEL_URL = 'https://api.openai.com/v1/chat/completions';

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

        if (! $apiKey || ! $model) {
            return ['error' => 'Missing API key or model configuration.'];
        }

        if (str_contains($model, self::GEMINI_MODEL)) {
            return GeminiService::ask($prompt, $model, $apiKey);
        } else {
            return OpenAIService::ask($prompt, $model);
        }
    }
}
