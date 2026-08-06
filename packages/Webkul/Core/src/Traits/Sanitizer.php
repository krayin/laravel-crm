<?php

namespace Webkul\Core\Traits;

use enshrined\svgSanitize\data\AllowedAttributes;
use enshrined\svgSanitize\data\AllowedTags;
use enshrined\svgSanitize\Sanitizer as MainSanitizer;
use Exception;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Trait for sanitizing SVG uploads to prevent security vulnerabilities.
 */
trait Sanitizer
{
    /**
     * Sanitize rich-text (TinyMCE/editor) HTML with an allowlist, removing scripts, event handlers,
     * `javascript:` URIs and other active content while keeping safe formatting and links.
     *
     * This provides the server-side boundary that client-side TinyMCE filtering cannot, so a crafted
     * request submitted directly to an endpoint can never store an executable payload.
     */
    public function sanitizeHtml(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $config = HTMLPurifier_Config::createDefault();

        /**
         * Disable the on-disk definition cache so the vendor directory is never written to; config
         * and rich-text saves are infrequent, so the small extra cost is irrelevant.
         */
        $config->set('Cache.DefinitionImpl', null);

        /**
         * Preserve `target="_blank"` links (used by the default footer) while neutralising tab-nabbing.
         */
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('HTML.TargetBlank', true);

        return (new HTMLPurifier($config))->purify($html);
    }

    /**
     * Sanitize an SVG file to remove potentially malicious content.
     */
    public function sanitizeSvg(string $path, UploadedFile $file): void
    {
        if (! $this->isSvgFile($file)) {
            return;
        }

        try {
            $svgContent = Storage::get($path);

            if (! $svgContent) {
                return;
            }

            $sanitizer = new MainSanitizer;
            $sanitizer->setAllowedAttrs(new AllowedAttributes);
            $sanitizer->setAllowedTags(new AllowedTags);

            $sanitizer->minify(true);
            $sanitizer->removeRemoteReferences(true);
            $sanitizer->removeXMLTag(true);

            $sanitizer->setXMLOptions(LIBXML_NONET | LIBXML_NOBLANKS);

            $sanitizedContent = $sanitizer->sanitize($svgContent);

            if ($sanitizedContent === false) {
                $patterns = [
                    '/<script\b[^>]*>(.*?)<\/script>/is',
                    '/\bon\w+\s*=\s*["\'][^"\']*["\']/i',
                    '/javascript\s*:/i',
                    '/data\s*:[^,]*base64/i',
                ];

                $sanitizedContent = $svgContent;

                foreach ($patterns as $pattern) {
                    $sanitizedContent = preg_replace($pattern, '', $sanitizedContent);
                }

                Storage::put($path, $sanitizedContent);

                return;
            }

            $sanitizedContent = preg_replace('/(<script.*?>.*?<\/script>)|(\son\w+\s*=\s*["\'][^"\']*["\'])/is', '', $sanitizedContent);

            Storage::put($path, $sanitizedContent);
        } catch (Exception $e) {
            report($e->getMessage());

            Storage::delete($path);
        }
    }

    /**
     * Check if the uploaded file is an SVG based on both extension and mime type.
     */
    public function isSvgFile(UploadedFile $file): bool
    {
        return str_contains(strtolower($file->getClientOriginalExtension()), 'svg');
    }
}
