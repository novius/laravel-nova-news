<div align="center">

# Laravel Nova News

[![Novius CI](https://github.com/novius/laravel-nova-news/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/novius/laravel-nova-news/actions/workflows/main.yml)

</div>

## Introduction 

This [Laravel Nova](https://nova.laravel.com/) package allows you to manage Post news in your Laravel Nova admin panel.  
You will be able to create posts, categories and tags.  
You can attach multiple categories and tags to a post. Categories can be viewed as a listing page.

## Requirements

* PHP >= 8.2
* Laravel Nova >= 4.0
* Laravel >= 11.0

> **NOTE**: These instructions are for Laravel >= 10.0 and PHP >= 8.2 If you are using prior version, please
> see the [previous version's docs](https://github.com/novius/laravel-nova-news/tree/0.1.x).

## Installation

You can install the package via composer:

```bash
composer require novius/laravel-nova-news
```

Register the tool in the `tools` method of the `NovaServiceProvider`:

```php
// app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        new \Novius\LaravelNovaNews\LaravelNovaNews(),
    ];
}
```

Run migrations with:

```bash
php artisan migrate
```

## Configuration

You can optionally publish the config file with:

```bash
php artisan vendor:publish --provider="Novius\LaravelNovaNews\LaravelNovaNewsServiceProvider" --tag="config"
```

This will allow you defined routes names and  

This will allow you to:  
* define the name of the routes and their parameter
* override resource or model class
* define locales used

```php
// config/laravel-nova-news.php

return [
    // ...
    'resources' => [
        'post' => \App\Nova\Post::class,
    ],
];
```

```php
// app/Nova/Post.php

namespace App\Nova;

use Laravel\Nova\Fields\Text;

class Post extends \Novius\LaravelNovaNews\Nova\NewsPost
{
    public function mainFields(): array
    {
        return [
            ...parent::mainFields(),

            Text::make('Subtitle'),
        ];
    }
}
```

## Front Stuff

If you want a pre-generated front controller and routes, you can run following command :

```shell
php artisan news-manager:publish-front {--without-categories} {--without-tags} 
``` 

This command appends routes to `routes/web.php` and creates a new `App\Http\Controllers\FrontNewsController`.

You can then customize your routes and your controller.

In views called by the controller use the documentation of [laravel-meta](https://github.com/novius/laravel-meta?tab=readme-ov-file#front) to implement meta tags

## Assets

Next we need to publish the Laravel Nova Translatable package's assets. We do this by running the following command:

```sh
php artisan vendor:publish --provider="Novius\LaravelNovaTranslatable\LaravelNovaTranslatableServiceProvider" --tag="public"
```

## Migrations and lang files

If you want to customize the migrations or lang files, you can publish them with:

```bash
php artisan vendor:publish --provider="Novius\LaravelNovaNews\LaravelNovaNewsServiceProvider" --tag="migrations"
```

```bash
php artisan vendor:publish --provider="Novius\LaravelNovaNews\LaravelNovaNewsServiceProvider" --tag="lang"
```

## Testing

Run the tests with:

```bash
composer test
```

## Lint

Lint your code with Laravel Pint using:

```bash
composer lint
```

## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
