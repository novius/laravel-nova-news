<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Novius\LaravelNovaNews\Models\NewsCategory;

uses(RefreshDatabase::class);

it('has a name', function () {
    expect(NewsCategory::factory()->create()->name)->toBeString();
});

it('has a slug', function () {
    expect(NewsCategory::factory()->create()->slug)->toBeString();
});
