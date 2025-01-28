<?php

namespace Webkul\Admin\Helpers;

use Smalot\PdfParser\Parser;

class Lead
{
    public static function extractDataFromPdf($pdfPath)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $text = $pdf->getText();

        return $text;
    }
}
