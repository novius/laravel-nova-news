<?php

namespace Novius\LaravelNovaNews\Tests;

use Novius\LaravelLinkable\LaravelLinkableServiceProvider;
use Novius\LaravelMeta\LaravelMetaServiceProvider;
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

        config(['laravel-nova-news.locales' => ['en', 'fr']]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelNovaNewsServiceProvider::class,
            LaravelTranslatableServiceProvider::class,
            LaravelPublishableServiceProvider::class,
            LaravelMetaServiceProvider::class,
            LaravelLinkableServiceProvider::class,
        ];
    }
}
