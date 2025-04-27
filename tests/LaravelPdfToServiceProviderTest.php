<?php

use UnknowSk\LaravelPdfTo\LaravelPdfToServiceProvider;

it('registers the service provider', function () {
    $provider = new LaravelPdfToServiceProvider(app());

    $provider->register();

    expect(app()->bound('laravel-pdf-to'))->toBeTrue();
});
