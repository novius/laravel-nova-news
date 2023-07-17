<?php

namespace Novius\LaravelNovaNews\Tests;

use Novius\LaravelNovaNews\LaravelNovaNewsServiceProvider;
use Novius\LaravelPublishable\LaravelPublishableServiceProvider;
use Novius\LaravelTranslatable\LaravelTranslatableServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setup();

        $this->loadLaravelMigrations();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelNovaNewsServiceProvider::class,
            LaravelTranslatableServiceProvider::class,
            LaravelPublishableServiceProvider::class,
        ];
    }
}
