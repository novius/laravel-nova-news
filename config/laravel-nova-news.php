<?php

return [
    /*
     * The resource used to manage your posts.
     */
    'post_resource' => \Novius\LaravelNovaNews\Nova\NewsPost::class,
    'category_resource' => \Novius\LaravelNovaNews\Nova\NewsCategory::class,
    'tag_resource' => \Novius\LaravelNovaNews\Nova\NewsTag::class,

    /*
     * The model used to manage your posts.
     */
    'post_model' => \Novius\LaravelNovaNews\Models\NewsPost::class,
    'category_model' => \Novius\LaravelNovaNews\Models\NewsCategory::class,
    'tag_model' => \Novius\LaravelNovaNews\Models\NewsTag::class,

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
    'front_route_name' => 'nova-news.post',
];
