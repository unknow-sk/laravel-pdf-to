<?php

use UnknowSk\LaravelPdfTo\Facades\LaravelPdfTo;

it('provides the correct facade accessor', function () {
    $facadeRoot = LaravelPdfTo::getFacadeRoot();
    expect($facadeRoot)->toBeInstanceOf(\UnknowSk\LaravelPdfTo\LaravelPdfTo::class);
});
