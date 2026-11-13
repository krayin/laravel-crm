<?php

namespace Webkul\Core\Traits;

use ArPHP\I18N\Arabic;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

trait PDFHandler
{
    /**
     * Download PDF.
     *
     * @return Response
     */
    protected function downloadPDF(string $html, ?string $fileName = null)
    {
        if (is_null($fileName)) {
            $fileName = Str::random(32);
        }

        $isRtlLocale = in_array(app()->getLocale(), ['ar', 'fa', 'he']);

        /**
         * Dompdf has no bidi or Arabic shaping support, so any document carrying right-to-left
         * text has to go through mPDF. Checking the locale alone is not enough: an English
         * interface routinely prints Arabic, Persian or Hebrew customer and product names.
         */
        $useMpdf = $isRtlLocale || $this->containsRightToLeftText($html);

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        if ($useMpdf) {
            $mPDF = new Mpdf([
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);

            $mPDF->SetDirectionality($isRtlLocale ? 'rtl' : 'ltr');

            $mPDF->SetDisplayMode('fullpage');

            $mPDF->WriteHTML($this->adjustArabicAndPersianContent($html));

            return response()->streamDownload(fn () => print ($mPDF->Output('', 'S')), $fileName.'.pdf');
        }

        return Pdf::loadHTML($this->adjustArabicAndPersianContent($html))
            ->setPaper('A4', 'portrait')
            ->set_option('defaultFont', 'Courier')
            ->download($fileName.'.pdf');
    }

    /**
     * Whether the content contains Arabic-script (Arabic, Persian, Urdu) or Hebrew text.
     */
    protected function containsRightToLeftText(string $html): bool
    {
        return (bool) preg_match('/[\p{Arabic}\p{Hebrew}]/u', strip_tags($html));
    }

    /**
     * Adjust arabic and persian content.
     *
     * @return string
     */
    protected function adjustArabicAndPersianContent(string $html)
    {
        $arabic = new Arabic;

        $p = $arabic->arIdentify($html);

        for ($i = count($p) - 1; $i >= 0; $i -= 2) {
            $utf8ar = $arabic->utf8Glyphs(substr($html, $p[$i - 1], $p[$i] - $p[$i - 1]));
            $html = substr_replace($html, $utf8ar, $p[$i - 1], $p[$i] - $p[$i - 1]);
        }

        return $html;
    }
}
