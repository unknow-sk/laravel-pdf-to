<?php

use Spatie\PdfToText\Exceptions\PdfNotFound;
use Hubio\LaravelPdfTo\LaravelPdfTo;

it('can set and retrieve configuration', function () {
    $instance = new LaravelPdfTo;

    $instance->setConfig(['key' => 'value']);

    expect($instance->getConfig()->get('key'))->toBe('value');
});

it('throws exception for unreadable file', function () {
    $instance = new LaravelPdfTo;

    $this->expectException(PdfNotFound::class);

    $instance->setFile('nonexistent.pdf');
});

it('can set timeout', function () {
    $instance = new LaravelPdfTo;

    $instance->setTimeout(120);

    expect($instance->getTimeout())->toBe(120);
});

it('can extract table data from PDF to HTML', function () {
    $instance = new LaravelPdfTo;

    $pdfPath = __DIR__.'/dummy.pdf'; // Ensure this PDF contains a table for testing
    $outputDir = __DIR__.'/output';
    $outputName = 'output';

    $instance->setConfig([
        'output_dir' => $outputDir,
    ])->setFile($pdfPath)->saveAs($outputName)->result('html');

    $outputPath = $outputDir.'/'.$outputName.'.html';

    expect(file_exists($outputPath))->toBeTrue();

    $htmlContent = file_get_contents($outputPath);

    // Check if the table data is present in the HTML output
    expect($htmlContent)->toContain('<html')
        ->and($htmlContent)->toContain('</html>');
});
