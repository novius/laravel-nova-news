<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Novius\LaravelNovaNews\Models\NewsPost;

uses(RefreshDatabase::class);

it('generates slug from title', function (string $title) {
    $post = NewsPost::factory()->create(['title' => $title]);
    expect($post->slug)->toBe(Str::slug($title));

    $post = NewsPost::factory()->create(['title' => $title]);
    expect($post->slug)->toBe(Str::slug($title).'-1');

    $post = NewsPost::factory()->create(['title' => $title]);
    expect($post->slug)->toBe(Str::slug($title).'-2');
})->with('news_titles');

it('cannot be created with invalid attributes', function () {
    NewsPost::factory()->create(['title' => null]);
})->throws(QueryException::class);
