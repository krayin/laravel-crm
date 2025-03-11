<?php

namespace Webkul\Lead\Services;

use Exception;
use Smalot\PdfParser\Parser;

class MagicAIService
{
    /**
     * API endpoint for OpenRouter AI service.
     */
    const OPEN_ROUTER_URL = 'https://openrouter.ai/api/v1/chat/completions';

    /**
     * Maximum token limit for AI prompt.
     */
    const MAX_TOKENS = 100000;

    /**
     * Flag to prevent re-entrant calls.
     */
    private static $isExtracting = false;

    /**
     * Extract data from base64-encoded file.
     */
    public static function extractDataFromFile($base64File)
    {
        if (self::$isExtracting) {
            throw new Exception('admin::app.leads.file.recursive-call');
        }

        self::$isExtracting = true;

        try {
            $text = self::extractTextFromBase64File($base64File);

            if (empty($text)) {
                throw new Exception('admin::app.leads.file.failed-extract');
            }

            return self::processPromptWithAI($text);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        } finally {
            self::$isExtracting = false;
        }
    }

    /**
     * Extract text from base64-encoded file.
     */
    private static function extractTextFromBase64File($base64File)
    {
        if (
            empty($base64File)
            || ! base64_decode($base64File, true)
        ) {
            throw new Exception('admin::app.leads.file.invalid-base64');
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'file_');

        file_put_contents($tempFile, base64_decode($base64File));

        $mimeType = mime_content_type($tempFile);

        try {
            if ($mimeType === 'application/pdf') {
                $pdf = new Parser();
                $pdfData = $pdf->parseFile($tempFile);
                $data['text'] = $pdfData->getText();

                $images = $pdfData->getObjectsByType('XObject', 'Image');

                foreach ($images as $image) {
                    $data['images'][] = base64_encode($image->getContent());
                }

                $data['image'] = self::extractTextFromImage($base64File);
            } else {
                $data['image'] = self::extractTextFromImage($base64File);
            }

            if (empty($data)) {
                throw new Exception('admin::app.leads.file.data-extraction-failed');
            }

            if (! isset($data['text'])) {
                $data['text'] = '';
            }

            return $data;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        } finally {
            @unlink($tempFile);
        }
    }

    /**
     * Send extracted data to AI for processing.
     */
    private static function processPromptWithAI($prompt)
    {
        $model = core()->getConfigData('general.magic_ai.settings.other_model') ?: core()->getConfigData('general.magic_ai.settings.model');

        $apiKey = core()->getConfigData('general.magic_ai.settings.api_key');

        if (! $apiKey || ! $model) {
            return ['error' => trans('admin::app.leads.file.missing-api-key')];
        }

        $prompt['text'] = self::truncatePrompt($prompt['text']);

        return self::ask($prompt['text'], $prompt['image'], $model, $apiKey);
    }

    /**
     * Truncate prompt to fit within token limit.
     */
    private static function truncatePrompt($prompt)
    {
        if (strlen($prompt) > self::MAX_TOKENS) {
            $start = mb_substr($prompt, 0, self::MAX_TOKENS * 0.4);

            $end = mb_substr($prompt, -self::MAX_TOKENS * 0.4);

            return $start."\n...\n".$end;
        }

        return $prompt;
    }

    /**
     * Send request to AI for processing.
     */
    private static function ask($extractedText, $base64Image, $model, $apiKey)
    {
        try {
            $payload = [
                'model'    => $model,
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => self::getSystemPrompt(),
                    ],
                    [
                        'role'    => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $extractedText],
                            ['type' => 'image', 'image' => $base64Image],
                        ],
                    ],
                ],
            ];

            $response = \Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post(self::OPEN_ROUTER_URL, $payload);

            if ($response->failed()) {
                throw new Exception($response->body());
            }

            $data = $response->json();

            if (isset($data['error'])) {
                throw new Exception($data['error']['message']);
            }

            return $data;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Define system prompt for AI processing.
     *
     * @return string System prompt for AI model.
     */
    private static function getSystemPrompt()
    {
        return <<<'PROMPT'
            You are an AI assistant. The user will provide text extracted from a file. 
            Extract the following data:

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
            }
            PROMPT;
    }

    /**
     * Extract text from a PDF using Smalot PDF Parser.
     */
    private static function extractTextFromPdf($filePath)
    {
        try {
            $parser = new Parser;

            return $parser->parseFile($filePath)->getText();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Extract text from an image by sending base64 data to AI.
     */
    private static function extractTextFromImage($base64File)
    {
        return base64_encode(base64_decode($base64File));
    }
}
