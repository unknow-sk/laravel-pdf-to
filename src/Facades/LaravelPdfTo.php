<?php

namespace UnknowSk\LaravelPdfTo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \UnknowSk\LaravelPdfTo\LaravelPdfTo
 */
class LaravelPdfTo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \UnknowSk\LaravelPdfTo\LaravelPdfTo::class;
    }
}
