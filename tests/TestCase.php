<?php

namespace Hubio\LaravelPdfTo\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Hubio\LaravelPdfTo\LaravelPdfToServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelPdfToServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
