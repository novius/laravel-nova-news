<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Nova\Nova;
use Novius\LaravelNovaNews\LaravelNovaNewsServiceProvider;
use Novius\LaravelNovaNews\Nova\NewsCategory;
use Novius\LaravelNovaNews\Nova\NewsPost;
use Novius\LaravelNovaNews\Nova\NewsTag;

uses(RefreshDatabase::class);

test('It creates the Nova Service Provider', function () {
    $provider = new LaravelNovaNewsServiceProvider($this->app);

    expect($provider)->toBeInstanceOf(LaravelNovaNewsServiceProvider::class);
});

test('It registers the resources', function () {
    expect(in_array(NewsPost::class, Nova::$resources))->toBeTrue();
    expect(in_array(NewsCategory::class, Nova::$resources))->toBeTrue();
    expect(in_array(NewsTag::class, Nova::$resources))->toBeTrue();
});
