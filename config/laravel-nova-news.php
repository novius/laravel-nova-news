<?php

return [
    /*
     * Resources used to manage your posts.
     */
    'resources' => [
        'post' => \Novius\LaravelNovaNews\Nova\NewsPost::class,
        'category' => \Novius\LaravelNovaNews\Nova\NewsCategory::class,
        'tag' => \Novius\LaravelNovaNews\Nova\NewsTag::class,
    ],

    /*
     * Models used to manage your posts.
     */
    'models' => [
        'post' => \Novius\LaravelNovaNews\Models\NewsPost::class,
        'category' => \Novius\LaravelNovaNews\Models\NewsCategory::class,
        'tag' => \Novius\LaravelNovaNews\Models\NewsTag::class,
    ],

    /*
     * The locales available for your posts.
     */
    'locales' => [
        'en' => 'English',
        'fr' => 'Français',
    ],

    /*
     * The route name used to display news posts and categories.
     */
    'front_routes_name' => [
        'post' => null,
        'category' => null,
        'tag' => null,
    ],
];
