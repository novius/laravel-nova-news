<?php

use Novius\LaravelNovaNews\Nova\NewsCategory;
use Novius\LaravelNovaNews\Nova\NewsPost;
use Novius\LaravelNovaNews\Nova\NewsTag;

return [
    /*
     * Resources used to manage your posts.
     */
    'resources' => [
        'post' => NewsPost::class,
        'category' => NewsCategory::class,
        'tag' => NewsTag::class,
    ],

    /*
     * Models used to manage your posts.
     */
    'models' => [
        'post' => Novius\LaravelNovaNews\Models\NewsPost::class,
        'category' => Novius\LaravelNovaNews\Models\NewsCategory::class,
        'tag' => Novius\LaravelNovaNews\Models\NewsTag::class,
    ],

    /*
     * The locales available for your posts. By default, it's the locales defined in your app.'
     */
    /*    'locales' => [
            'en' => 'English',
            'fr' => 'Français',
    ],*/

    /*
     * The route name used to display news posts and categories.
     */
    'front_routes_name' => [
        'posts' => null,
        'post' => null,
        'categories' => null,
        'category' => null,
        'tag' => null,
    ],

    /*
     * The route name used to display news posts and categories.
     */
    'front_routes_parameters' => [
        'post' => null,
        'category' => null,
        'tag' => null,
    ],

    'guard_preview' => null,
];
