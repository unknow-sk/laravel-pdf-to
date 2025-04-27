<?php

use Spatie\PdfToText\Exceptions\BinaryNotFoundException;
use UnknowSk\LaravelPdfTo\Pdf;

it('throws exception for missing binary', function () {
    $pdf = new Pdf;

    $this->expectException(BinaryNotFoundException::class);

    $pdf->findPdfTo(['/nonexistent/path']);
});
