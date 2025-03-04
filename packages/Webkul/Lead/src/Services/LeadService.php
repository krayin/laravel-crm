<?php

namespace Webkul\Lead\Services;

use Exception;
use Smalot\PdfParser\Parser;
use Webkul\Lead\Helpers\Lead;

class LeadService
{
    /**
     * Const variable
     */
    const OPEN_AI_MODEL_URL = 'https://api.openai.com/v1/chat/completions';

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

        if (str_contains($model, Lead::GEMINI_MODEL)) {
            return GeminiService::ask($prompt, $model, $apiKey);
        } else {
            return OpenAIService::ask($prompt, $model);
        }
    }
}
