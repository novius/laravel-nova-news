<?php

namespace Novius\LaravelNovaNews;

use Novius\LaravelNovaNews\Models\NewsCategory;
use Novius\LaravelNovaNews\Models\NewsPost;
use Novius\LaravelNovaNews\Models\NewsTag;

class NovaNews
{
    public static function getPostModel(): string
    {
        return config('laravel-nova-news.models.post', NewsPost::class);
    }

    public static function getPostResource(): string
    {
        return config('laravel-nova-news.resources.post', Nova\NewsPost::class);
    }

    public static function getCategoryModel(): string
    {
        return config('laravel-nova-news.models.category', NewsCategory::class);
    }

    public static function getCategoryResource(): string
    {
        return config('laravel-nova-news.resources.category', Nova\NewsCategory::class);
    }

    public static function getTagModel(): string
    {
        return config('laravel-nova-news.models.tag', NewsTag::class);
    }

    public static function getTagResource(): string
    {
        return config('laravel-nova-news.resources.tag', Nova\NewsTag::class);
    }
}
