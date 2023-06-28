<?php

namespace Novius\LaravelNovaNews;

class NovaNews
{
    public static function getPostModel(): string
    {
        return config('laravel-nova-news.models.post', \Novius\LaravelNovaNews\Models\NewsPost::class);
    }

    public static function getPostResource(): string
    {
        return config('laravel-nova-news.resources.post', \Novius\LaravelNovaNews\Nova\NewsPost::class);
    }

    public static function getCategoryModel(): string
    {
        return config('laravel-nova-news.models.category', \Novius\LaravelNovaNews\Models\NewsCategory::class);
    }

    public static function getCategoryResource(): string
    {
        return config('laravel-nova-news.resources.category', \Novius\LaravelNovaNews\Nova\NewsCategory::class);
    }

    public static function getTagModel(): string
    {
        return config('laravel-nova-news.models.tag', \Novius\LaravelNovaNews\Models\NewsTag::class);
    }

    public static function getTagResource(): string
    {
        return config('laravel-nova-news.resources.tag', \Novius\LaravelNovaNews\Nova\NewsTag::class);
    }

    public static function getLocales(): array
    {
        return config('laravel-nova-news.locales', []);
    }
}
