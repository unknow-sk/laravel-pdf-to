# Laravel PDF To
[![GitHub](https://img.shields.io/github/license/unknow-sk/laravel-pdf-to?style=flat-square)](LICENSE)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/unknow-sk/laravel-pdf-to.svg?style=flat-square)](https://packagist.org/packages/unknow-sk/laravel-pdf-to)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/unknow-sk/laravel-pdf-to/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/unknow-sk/laravel-pdf-to/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/unknow-sk/laravel-pdf-to/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/unknow-sk/laravel-pdf-to/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/unknow-sk/laravel-pdf-to.svg?style=flat-square)](https://packagist.org/packages/unknow-sk/laravel-pdf-to)

Laravel package for extracting Text/Html from a PDF or converting it to images (PNG, JPeG).

## Support us

[<img src="https://unknow.sk/logo.svg" width="288px" />](https://opencollective.com/unknow-sk)

We invest a lot of time and give our hearts to work in (Open Source)[https://unknow.sk].

## Installation

You can install the package via composer:

```bash
composer require unknow-sk/laravel-pdf-to
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-pdf-to-config"
```

This is the content of the published config file:

```php
return [
    /**
     * Set the pdftotext binary path manually
     */
    'pdftotext_bin' => env('PDF_TO_TEXT_PATH'),

    /**
     * Set the pdftohtml binary path manually
     */
    'pdftohtml_bin' => env('PDF_TO_HTML_PATH'),

    /**
     * Set the pdftoppm binary path manually
     */
    'pdftoppm_bin' => env('PDF_TO_PPM_PATH'),

    /**
     * Set the pdftocairo binary path manually
     */
    'pdftocairo_bin' => env('PDF_TO_CAIRO_PATH'),

    /**
     * Set the default output directory
     */
    'output_dir' => env('PDF_TO_OUTPUT_DIR', storage_path('app/pdf-to')),
];
```

### Required Packages

This package relies on the following external tools:

- **pdftotext**: For extracting text from PDFs.
- **pdftohtml**: For converting PDFs to HTML.
- **pdftoppm**: For generating images from PDFs.

Make sure these tools are installed and available in your system's PATH. On macOS, you can install them via Homebrew:

```bash
brew install poppler
```

### Binary Files Configuration

By default, the package attempts to locate the required binary files (`pdftotext`, `pdftohtml`, `pdftoppm`, or `pdftocairo`) automatically. If these binaries are not found in your system's PATH, you will need to set their paths manually in the configuration file.

You can update the configuration file `config/pdf-to.php` as follows:

```php
return [
    'pdftotext_bin' => env('PDF_TO_TEXT_PATH'),
    'pdftohtml_bin' => env('PDF_TO_HTML_PATH'),
    'pdftoppm_bin' => env('PDF_TO_PPM_PATH'),
    'pdftocairo_bin' => env('PDF_TO_CAIRO_PATH'),
];
```

### Alternative Libraries

For text extraction, this package uses the [Spatie/pdf-to-text](https://github.com/spatie/pdf-to-text). However, if you don't have `pdftoppm` or `pdftocairo` installed, you can also use the [Spatie/pdf-to-image](https://github.com/spatie/pdf-to-image) for image generation. This provides a fallback mechanism to ensure functionality even without the required binaries.

## Usage

### Extract Text from PDF

```php
use UnknowSk\LaravelPdfTo\Facades\LaravelPdfTo;

$text = LaravelPdfTo::setFile('path/to/your/file.pdf')
    ->setTimeout(120) // optionally
    ->result('txt');
echo $text;
```

### Convert PDF to HTML

```php
use UnknowSk\LaravelPdfTo\Facades\LaravelPdfTo;

$html = LaravelPdfTo::setFile('path/to/your/file.pdf')
    ->setConfig(['options' => [...]]) // optionally
    ->saveAs('output-file') // optionally, if you wan to store as file, then result returns path
    ->result('html');
echo $html;
```

### Convert PDF to Images

```php
use UnknowSk\LaravelPdfTo\Facades\LaravelPdfTo;

$image = LaravelPdfTo::setFile('path/to/your/file.pdf')
    ->setTimeout(180) // optionally
    ->result('png');
echo $image; // Path to the generated image
```

## Testing

To run the tests, use the following command:

```bash
composer test
```

The tests include functionality for extracting text, converting to HTML, and generating images from PDFs. Example test files are located in the `tests/` directory.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Unknow.sk](https://github.com/unknow-sk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
