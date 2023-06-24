<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Novius\LaravelNovaNews\Models\NewsCategory;
use Novius\LaravelNovaNews\Models\NewsPost;

uses(RefreshDatabase::class);

it('increments the slug when creating a category with a duplicate slug', function () {
    $category = NewsCategory::factory()->create(['slug' => 'test']);
    $category2 = NewsCategory::factory()->create(['slug' => 'test']);
    $this->assertEquals('test', $category->slug);
    $this->assertEquals('test-1', $category2->slug);
});

it('cannot be created with invalid attributes', function () {
    NewsCategory::factory()->create(['name' => null]);
})->throws(QueryException::class);

it('can attach categories to a post', function () {
    $post = NewsPost::factory()->create();
    $category1 = NewsCategory::factory()->create();
    $category2 = NewsCategory::factory()->create();

    $post->categories()->attach([$category1->id, $category2->id]);

    $this->assertTrue($post->categories->contains($category1));
    $this->assertTrue($post->categories->contains($category2));
});
