<?php

# src/Services/PdfGenerator.php
namespace App\Services;

use Knp\Snappy\Pdf;

class PdfGenerator
{
    private $pdf;

    public function __construct($pdf)
    {
        $this->pdf = $pdf;
    }

    public function generateFromHtml(string $html)
    {
        return $this->pdf->getOutputFromHtml($html);
    }
}