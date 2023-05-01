<?php

namespace Novius\LaravelNovaNews;

class NovaNews
{
    public static function getPostModel(): string
    {
        return config('laravel-nova-news.post_model', \Novius\LaravelNovaNews\Models\NewsPost::class);
    }

    public static function getPostResource(): string
    {
        return config('laravel-nova-news.post_resource', \Novius\LaravelNovaNews\Nova\NewsPost::class);
    }

    public static function getCategoryResource(): string
    {
        return config('laravel-nova-news.category_resource', \Novius\LaravelNovaNews\Nova\NewsCategory::class);
    }

    public static function getTagResource(): string
    {
        return config('laravel-nova-news.tag_resource', \Novius\LaravelNovaNews\Nova\NewsTag::class);
    }
}
