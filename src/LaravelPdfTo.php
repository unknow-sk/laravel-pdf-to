<?php

namespace UnknowSk\LaravelPdfTo;

use Closure;
use Illuminate\Config\Repository;
use RuntimeException;
use Spatie\PdfToImage\Exceptions\PdfDoesNotExist;
use Spatie\PdfToText\Exceptions\BinaryNotFoundException;
use Spatie\PdfToText\Exceptions\CouldNotExtractText;
use Spatie\PdfToText\Exceptions\PdfNotFound;

class LaravelPdfTo
{
    /**
     * The config repository.
     */
    protected Repository $config;

    /**
     * The PDF file to convert.
     */
    protected string $pdfFile;

    /**
     * The output file name.
     */
    protected ?string $outputFile = null;

    /**
     * The timeout in seconds for the process to run.
     */
    protected int $timeout = 60;

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(Repository|array|null $config = null)
    {
        if (is_null($config)) {
            $config = (array) config('pdf-to', []);
        }

        $this->config = ! $config instanceof Repository ? new Repository($config) : $config;
        $this->config->set('options', (array) $this->config->get('options'));
    }

    /**
     * Override the default config.
     *
     * @return $this
     */
    public function setConfig(array $attributes = []): static
    {
        collect($attributes)->each(fn ($value, $key) => $this->config->set($key, $value));

        return $this;
    }

    /**
     * Get the config.
     */
    public function getConfig(): Repository
    {
        return $this->config;
    }

    /**
     * Set the PDF File.
     *
     * @return $this
     *
     * @throws PdfNotFound
     */
    public function setFile(string $file): static
    {
        if (! is_readable($file)) {
            throw new PdfNotFound("Could not read `$file`");
        }

        $this->pdfFile = $file;

        return $this;
    }

    /**
     * Get the PDF file.
     */
    public function getFile(): string
    {
        return $this->pdfFile;
    }

    /**
     * Set the output file name.
     *
     * @return $this
     *
     * @throws RuntimeException
     */
    public function saveAs(string $file): static
    {
        if (! preg_match('/^[a-zA-Z0-9-_]+$/', $file)) {
            throw new RuntimeException("Invalid filename `$file`: only a-z, 0-9, -, _ allowed");
        }

        $this->outputFile = $file;

        return $this;
    }

    /**
     * Get the output file name.
     */
    public function getOutputFile(): ?string
    {
        return $this->outputFile;
    }

    /**
     * Set the timeout
     */
    public function setTimeout(int $timeout): static
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the timeout
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Convert PDF to TXT or HTML and get the output file path
     *
     * @throws CouldNotExtractText|BinaryNotFoundException|PdfNotFound|PdfDoesNotExist
     */
    public function result(?string $extension = null, ?Closure $callback = null): string
    {
        $pdfTo = new Pdf(
            binPath: $this->config->get('pdftotext_bin'),
            htmlBinPath: $this->config->get('pdftohtml_bin'),
            ppmBinPath: $this->config->get('pdftoppm_bin'),
            cairoBinPath: $this->config->get('pdftocairo_bin'),
        );

        $pdfTo->setOptions((array) $this->config->get('options', []))
            ->setTimeout($this->timeout)
            ->setPdf($this->pdfFile);

        $outputDir = rtrim($this->config->get('output_dir'), DIRECTORY_SEPARATOR);

        if (! file_exists($outputDir) && ! mkdir($outputDir, 0755, true) && ! is_dir($outputDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $outputDir));
        }
        if (! is_writable($outputDir)) {
            throw new RuntimeException(sprintf('Directory "%s" is not writable', $outputDir));
        }

        $extension = strtolower(trim($extension, '.'));
        $resultFile = $outputDir.DIRECTORY_SEPARATOR.(
            $this->outputFile ?: basename($this->pdfFile, '.pdf')
        );

        $returnFilePath = true;
        if ($extension === 'png' || $extension === 'img' || $extension === 'image') {
            $text = $pdfTo->png($callback, $resultFile);
            $resultFile .= '.png';
        } elseif ($extension === 'jpg' || $extension === 'jpeg') {
            $text = $pdfTo->jpg($callback, $resultFile);
            $resultFile .= '.jpg';
        } elseif ($extension === 'html') {
            $text = $pdfTo->html($callback, $resultFile);
            $resultFile .= '.html';
        } else {
            $returnFilePath = false;
            $text = $pdfTo->text($callback);
            $resultFile .= '.txt';
        }

        // If an output file is set, save the result to the specified directory
        if (isset($this->outputFile)) {

            // Write the text content to the output file
            if (! $returnFilePath) {
                file_put_contents($resultFile, $text);
            }

            return $resultFile;
        }

        return $returnFilePath ? file_get_contents($text) : $text;
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-pdf-to';
    }
}
