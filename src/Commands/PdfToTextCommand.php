<?php

namespace UnknowSk\LaravelPdfTo\Commands;

use Illuminate\Console\Command;
use Throwable;
use UnknowSk\LaravelPdfTo\Facades\LaravelPdfTo;

class PdfToTextCommand extends Command
{
    public $signature = 'pdf-to-text
                        {pdf : Path to the PDF file}
                        {--output-dir= : Directory path to save the output file}
                        {--name= : File name to save the output file}
                        {--timeout= : Timeout in seconds}';

    public $description = 'Convert a PDF to Text';

    public function handle(): int
    {
        $pdfPath = $this->argument('pdf');
        $outputDir = $this->option('output-dir');
        $outputName = $this->option('name');
        $timeout = $this->option('timeout') ?: 60;

        try {
            $this->info("Processing PDF: $pdfPath");
            $this->info("Output Directory: $outputDir");
            $this->info("Output Name: $outputName");

            if (! empty($outputName) && empty($outputDir)) {
                $outputDir = rtrim(config('pdf-to.output_dir'), '/').'/';
            }

            $result = LaravelPdfTo::setConfig([
                'output_dir' => $outputDir ?: null,
            ])
                ->setFile($pdfPath)
                ->saveAs($outputName)
                ->setTimeout($timeout)
                ->result();

            if ($outputDir) {
                $this->info("Conversion complete! Output saved to: $result");
            } else {
                $this->info('Conversion complete! Output is:');
                $this->line($result);
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
