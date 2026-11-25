<?php

namespace Webkul\Firma\Services;

use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;
use Smalot\PdfParser\Parser;
use Throwable;
use Webkul\Quote\Contracts\Quote;

/**
 * Renders a quote to PDF the same way the quote print and quote mail actions do.
 */
class QuotePdfRenderer
{
    /**
     * The quote PDF bytes.
     */
    public function render(Quote $quote): string
    {
        $html = view('admin::quotes.pdf', compact('quote'))->render();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        if (in_array($direction = app()->getLocale(), ['ar', 'he'])) {
            $mPDF = new Mpdf([
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);

            $mPDF->SetDirectionality($direction);

            $mPDF->SetDisplayMode('fullpage');

            $mPDF->WriteHTML($this->adjustArabicAndPersianContent($html));

            return $mPDF->Output('', 'S');
        }

        return Pdf::loadHTML($this->adjustArabicAndPersianContent($html))
            ->setPaper('A4', 'portrait')
            ->setOption('defaultFont', 'Courier')
            ->output();
    }

    /**
     * Shapes Arabic and Persian text for the PDF engines, as the quote print action does
     * (Webkul\Core\Traits\PDFHandler).
     */
    protected function adjustArabicAndPersianContent(string $html): string
    {
        $arabic = new Arabic;

        $p = $arabic->arIdentify($html);

        for ($i = count($p) - 1; $i >= 0; $i -= 2) {
            $utf8ar = $arabic->utf8Glyphs(substr($html, $p[$i - 1], $p[$i] - $p[$i - 1]));

            $html = substr_replace($html, $utf8ar, $p[$i - 1], $p[$i] - $p[$i - 1]);
        }

        return $html;
    }

    /**
     * The page count of a PDF, or 0 when it cannot be read.
     */
    public function pageCount(string $content): int
    {
        try {
            return count((new Parser)->parseContent($content)->getPages());
        } catch (Throwable) {
            return 0;
        }
    }
}
