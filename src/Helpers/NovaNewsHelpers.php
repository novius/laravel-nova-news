<?php

namespace Novius\LaravelNovaNews\Helpers;

use Novius\LaravelNovaNews\Models\NewsPost;
use Novius\LaravelNovaNews\NovaNews;

class NovaNewsHelpers
{
    /**
     * Get all posts.
     */
    public static function getPosts(bool $onlyPublished = true): \Illuminate\Support\Collection
    {
        if ($onlyPublished) {
            return NovaNews::getPostModel()::published()->get();
        }

        return NovaNews::getPostModel()::all();
    }

    /**
     * Get all tags names for a given post.
     */
    public static function getPostTagsNames(NewsPost $post): \Illuminate\Support\Collection
    {
        return collect($post->tags->pluck('name'));
    }
}
