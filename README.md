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
* Laravel >= 10.0

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

This will allow you to override the resource class for example.

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
