<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Novius\LaravelNovaNews\Models\NewsTag;

uses(RefreshDatabase::class);

it('has a name', function () {
    expect(NewsTag::factory()->create()->name)->toBeString();
});

it('has a slug', function () {
    expect(NewsTag::factory()->create()->slug)->toBeString();
});
