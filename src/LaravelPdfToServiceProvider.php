<?php

namespace UnknowSk\LaravelPdfTo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use UnknowSk\LaravelPdfTo\Commands\PdfToHtmlCommand;
use UnknowSk\LaravelPdfTo\Commands\PdfToImageCommand;
use UnknowSk\LaravelPdfTo\Commands\PdfToTextCommand;

class LaravelPdfToServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-pdf-to')
            ->hasConfigFile()
            ->hasCommands([
                PdfToHtmlCommand::class,
                PdfToImageCommand::class,
                PdfToTextCommand::class,
            ]);
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton('laravel-pdf-to', function () {
            return new LaravelPdfTo;
        });
    }
}
