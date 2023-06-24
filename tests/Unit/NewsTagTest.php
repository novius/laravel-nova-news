<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Novius\LaravelNovaNews\Models\NewsPost;
use Novius\LaravelNovaNews\Models\NewsTag;

uses(RefreshDatabase::class);

it('increments the slug when creating a tag with a duplicate slug', function () {
    $tag = NewsTag::factory()->create(['slug' => 'test']);
    $tag2 = NewsTag::factory()->create(['slug' => 'test']);
    $this->assertEquals('test', $tag->slug);
    $this->assertEquals('test-1', $tag2->slug);
});

it('cannot be created with invalid attributes', function () {
    NewsTag::factory()->create(['name' => null]);
})->throws(QueryException::class);

it('can attach tags to a post', function () {
    $post = NewsPost::factory()->create();
    $tag1 = NewsTag::factory()->create();
    $tag2 = NewsTag::factory()->create();

    $post->tags()->attach([$tag1->id, $tag2->id]);

    $this->assertTrue($post->tags->contains($tag1));
    $this->assertTrue($post->tags->contains($tag2));
});
