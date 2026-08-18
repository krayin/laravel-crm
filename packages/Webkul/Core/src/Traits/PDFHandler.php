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

            return response()->streamDownload(fn () => print ($mPDF->Output('', 'S')), $fileName.'.pdf');
        }

        return Pdf::loadHTML($this->adjustArabicAndPersianContent($html))
            ->setPaper('A4', 'portrait')
            ->set_option('defaultFont', 'Courier')
            ->download($fileName.'.pdf');
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
