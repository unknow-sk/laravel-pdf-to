<?php

use Hubio\LaravelPdfTo\Facades\LaravelPdfTo;

it('provides the correct facade accessor', function () {
    $facadeRoot = LaravelPdfTo::getFacadeRoot();
    expect($facadeRoot)->toBeInstanceOf(\Hubio\LaravelPdfTo\LaravelPdfTo::class);
});
