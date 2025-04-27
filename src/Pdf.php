<?php

namespace UnknowSk\LaravelPdfTo;

use Closure;
use RuntimeException;
use Spatie\PdfToImage\Exceptions\PdfDoesNotExist;
use Spatie\PdfToText\Exceptions\BinaryNotFoundException;
use Spatie\PdfToText\Exceptions\CouldNotExtractText;
use Spatie\PdfToText\Exceptions\PdfNotFound;
use Symfony\Component\Process\Process;

class Pdf extends \Spatie\PdfToText\Pdf
{
    /**
     * @var ?string Binary path to the pdftohtml executable
     */
    protected ?string $htmlBinPath;

    /**
     * @var ?string Binary path to the pdftoppm executable
     */
    protected ?string $ppmBinPath;

    /**
     * @var ?string Binary path to the pdftocairo executable
     */
    protected ?string $cairoBinPath;

    /**
     * Initialize the Pdf class.
     *
     * @throws BinaryNotFoundException
     *
     * @see \Spatie\PdfToText\Pdf
     */
    public function __construct(
        ?string $binPath = null,
        ?string $htmlBinPath = null,
        ?string $ppmBinPath = null,
        ?string $cairoBinPath = null
    ) {
        parent::__construct($binPath);

        $this->htmlBinPath = $htmlBinPath ?? $this->findPdfToHtml();
        $this->ppmBinPath = $ppmBinPath ?? $this->findPdfToPpm();
        $this->cairoBinPath = $cairoBinPath ?? $this->findPdfToCairo();
    }

    /**
     * Set the PDF to an HTML file to convert via pdftohtml.
     *
     * @throws BinaryNotFoundException
     */
    protected function findPdfToHtml(): string
    {
        return $this->findPdfTo([
            '/usr/bin/pdftohtml',          // Common on Linux
            '/usr/local/bin/pdftohtml',    // Common on Linux
            '/opt/homebrew/bin/pdftohtml', // Homebrew on macOS (Apple Silicon)
            '/opt/local/bin/pdftohtml',    // MacPorts on macOS
            '/usr/local/bin/pdftohtml',    // Homebrew on macOS (Intel)
        ]);
    }

    /**
     * Set the PDF to JPeG or PNG file to convert via pdftoppm.
     *
     * @throws BinaryNotFoundException
     */
    protected function findPdfToPpm(): string
    {
        return $this->findPdfTo([
            '/usr/bin/pdftoppm',          // Common on Linux
            '/usr/local/bin/pdftoppm',    // Common on Linux
            '/opt/homebrew/bin/pdftoppm', // Homebrew on macOS (Apple Silicon)
            '/opt/local/bin/pdftoppm',    // MacPorts on macOS
            '/usr/local/bin/pdftoppm',    // Homebrew on macOS (Intel)
        ]);
    }

    /**
     * Set the PDF to JPeG or PNG file to convert via pdftocairo.
     *
     * @throws BinaryNotFoundException
     */
    protected function findPdfToCairo(): string
    {
        return $this->findPdfTo([
            '/usr/bin/pdftocairo',          // Common on Linux
            '/usr/local/bin/pdftocairo',    // Common on Linux
            '/opt/homebrew/bin/pdftocairo', // Homebrew on macOS (Apple Silicon)
            '/opt/local/bin/pdftocairo',    // MacPorts on macOS
            '/usr/local/bin/pdftocairo',    // Homebrew on macOS (Intel)
        ]);
    }

    /**
     * Finds the binary in the common paths.
     *
     * @throws BinaryNotFoundException
     */
    public function findPdfTo(array $commonPaths): string
    {
        foreach ($commonPaths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        throw new BinaryNotFoundException('The required binary was not found or is not executable.');
    }

    /**
     * Convert to HTML.
     */
    public function html(?Closure $callback = null, ?string $output = null, bool $base64 = true): string
    {
        if (! in_array('-c', $this->options, true)) {
            $this->options[] = '-c';
        }
        if (! in_array('-noframes', $this->options, true)) {
            $this->options[] = '-noframes';
        }
        if (! in_array('-s', $this->options, true)) {
            $this->options[] = '-s';
        }

        $process = new Process(array_merge([$this->htmlBinPath], $this->options, [$this->pdf, $output]));
        $process->setTimeout($this->timeout);
        $process = $callback ? $callback($process) : $process;
        $process->run();
        if (! $process->isSuccessful()) {
            throw new CouldNotExtractText($process);
        }

        if ($base64) {
            $this->replaceImagesWithBase64($output.'.html', dirname($output.'.html'));
        }

        return $output;
    }

    /**
     * @throws PdfNotFound|BinaryNotFoundException
     */
    public static function getHtml(string $pdf, ?string $binPath = null, array $options = [], $timeout = 60, ?Closure $callback = null): string
    {

        return (new self(htmlBinPath: $binPath))
            ->setOptions($options)
            ->setTimeout($timeout)
            ->setPdf($pdf)
            ->text($callback);
    }

    /**
     * Convert to Image.
     *
     * @throws PdfDoesNotExist|BinaryNotFoundException
     */
    public function image(string $format = 'png', ?Closure $callback = null, ?string $output = null): string
    {
        $command = $this->cairoBinPath ?: $this->ppmBinPath;

        if ($command === null) {
            if (! class_exists(\Spatie\PdfToImage\Pdf::class)) {
                throw new BinaryNotFoundException('Neither pdftocairo nor pdftoppm is available, and spatie/pdf-to-image is not installed.');
            }

            $pdf = new \Spatie\PdfToImage\Pdf($this->pdf);
            $pdf->save($output);

            return $output;
        }

        // Determine the output format and add appropriate options
        $options = $this->options;
        if ($this->cairoBinPath) {
            $options[] = '-singlefile';
            $options[] = '-'.$format; // e.g., -png or -jpeg for pdftocairo
        } else {
            $options[] = '-r'; // Example: Add resolution option for pdftoppm
        }

        $process = new Process(array_merge([$command], $options, [$this->pdf, $output]));
        $process->setTimeout($this->timeout);
        $process = $callback ? $callback($process) : $process;
        $process->run();

        if (! $process->isSuccessful()) {
            throw new CouldNotExtractText($process);
        }

        return $output;
    }

    /**
     * Convert to PNG.
     *
     * @throws BinaryNotFoundException|PdfDoesNotExist
     */
    public function png(?Closure $callback = null, ?string $output = null): string
    {
        return $this->image('png', $callback, $output);
    }

    /**
     * @throws PdfNotFound|BinaryNotFoundException|PdfDoesNotExist
     */
    public static function getPng(string $pdf, ?string $binPath = null, array $options = [], $timeout = 60, ?Closure $callback = null): string
    {
        return (str_ends_with($binPath, 'pdftocairo') ? new self(cairoBinPath: $binPath) : new self(ppmBinPath: $binPath))
            ->setOptions($options)
            ->setTimeout($timeout)
            ->setPdf($pdf)
            ->png($callback);
    }

    /**
     * Convert to PNG.
     *
     * @throws BinaryNotFoundException|PdfDoesNotExist
     */
    public function jpg(?Closure $callback = null, ?string $output = null): string
    {
        return $this->image('jpeg', $callback, $output);
    }

    /**
     * @throws PdfNotFound|BinaryNotFoundException|PdfDoesNotExist
     */
    public static function getJpg(string $pdf, ?string $binPath = null, array $options = [], $timeout = 60, ?Closure $callback = null): string
    {
        return (str_ends_with($binPath, 'pdftocairo') ? new self(cairoBinPath: $binPath) : new self(ppmBinPath: $binPath))
            ->setOptions($options)
            ->setTimeout($timeout)
            ->setPdf($pdf)
            ->jpg($callback);
    }

    /**
     * Replace all image paths in the HTML file with their base64-encoded versions.
     *
     * @throws RuntimeException
     */
    public function replaceImagesWithBase64(string $htmlFilePath, string $outputDir): string
    {
        if (! file_exists($htmlFilePath)) {
            throw new RuntimeException("HTML file not found: $htmlFilePath");
        }

        $htmlContent = file_get_contents($htmlFilePath);

        // Match all image tags in the HTML
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $htmlContent, $imgMatches);
        $imagePaths = $imgMatches[1] ?? [];

        // Match all background-image URLs in style attributes
        preg_match_all('/background-image:\s*url\(["\']?([^"\')]+)["\']?\)/i', $htmlContent, $styleMatches);
        $stylePaths = $styleMatches[1] ?? [];

        $allPaths = array_merge($imagePaths, $stylePaths);

        foreach ($allPaths as $imagePath) {
            $absolutePath = $outputDir.DIRECTORY_SEPARATOR.basename($imagePath);

            if (file_exists($absolutePath)) {
                $imageData = base64_encode(file_get_contents($absolutePath));
                $mimeType = mime_content_type($absolutePath);
                $base64Src = "data:$mimeType;base64,$imageData";

                // Replace the image path with the base64 data
                $htmlContent = str_replace($imagePath, $base64Src, $htmlContent);
            }
        }

        return $htmlContent;
    }
}
