<?php

namespace Hubio\LaravelPdfTo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hubio\LaravelPdfTo\LaravelPdfTo
 */
class LaravelPdfTo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Hubio\LaravelPdfTo\LaravelPdfTo::class;
    }
}
