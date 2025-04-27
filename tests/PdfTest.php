<?php

use Spatie\PdfToText\Exceptions\BinaryNotFoundException;
use UnknowSk\LaravelPdfTo\Pdf;

it('throws exception for missing binary', function () {
    $pdf = new Pdf;

    if (stripos(PHP_OS_FAMILY, 'WIN') !== 0) {
        $this->expectException(BinaryNotFoundException::class);
        $pdf->findPdfTo('testing');
    }
});
