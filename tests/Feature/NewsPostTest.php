<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Novius\LaravelNovaNews\Models\NewsPost;

uses(RefreshDatabase::class);

it('has a title', function () {
    expect(NewsPost::factory()->create()->title)->toBeString();
});

it('has a slug', function () {
    expect(NewsPost::factory()->create()->slug)->toBeString();
});

it('has a locale', function () {
    expect(NewsPost::factory()->create()->locale)->toBeString();
});

it('has a featured', function () {
    expect(NewsPost::factory()->create()->featured)->toBeBool();
});

// @TODO: Fix this test
// it('has a publication_status', function () {
//     $post = NewsPost::factory()->create();

//     assertThat(PublicationStatus.valueOf("draft"), is(notNullValue()));
// });

it('has an intro', function () {
    expect(NewsPost::factory()->create()->intro)->toBeString();
});

it('has a content', function () {
    expect(NewsPost::factory()->create()->content)->toBeString();
});

it('has a featured_image', function () {
    expect(NewsPost::factory()->create()->featured_image)->toBeNull();
});

it('has a card_image', function () {
    expect(NewsPost::factory()->create()->card_image)->toBeNull();
});

it('has a preview_token', function () {
    expect(NewsPost::factory()->create()->preview_token)->toBeString();
});

it('has a seo_title', function () {
    expect(NewsPost::factory()->create()->seo_title)->toBeString();
});

it('has a seo_description', function () {
    expect(NewsPost::factory()->create()->seo_description)->toBeString();
});

it('has a og_title', function () {
    expect(NewsPost::factory()->create()->og_title)->toBeString();
});

it('has a og_description', function () {
    expect(NewsPost::factory()->create()->og_description)->toBeString();
});

it('has a og_image', function () {
    expect(NewsPost::factory()->create()->og_image)->toBeNull();
});

it('has a created_at date', function () {
    expect(NewsPost::factory()->create()->created_at)->toBeInstanceOf(Carbon::class);
});

it('has a updated_at date', function () {
    expect(NewsPost::factory()->create()->updated_at)->toBeInstanceOf(Carbon::class);
});

it('can be soft deleted', function () {
    $post = NewsPost::factory()->create();
    $post->delete();

    $this->assertTrue($post->trashed());
});

it('can be restored')
    ->expect(function () {
        $post = NewsPost::factory()->create();
        $post->delete();
        $post->restore();

        $this->assertFalse($post->trashed());
    });
