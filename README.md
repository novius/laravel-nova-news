<div align="center">

# Laravel Nova News

[![Novius CI](https://github.com/novius/laravel-nova-news/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/novius/laravel-nova-news/actions/workflows/main.yml)

</div>

## Introduction 

This [Laravel Nova](https://nova.laravel.com/) package allows you to manage Post news in your Laravel Nova admin panel.  
You will be able to create posts, categories and tags.  
You can attach multiple categories and tags to a post. Categories can be viewed as a listing page.

## Requirements

* Laravel Nova >= 4.0
* Laravel >= 8.0

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
    'post_resource' => \App\Nova\Post::class,
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

## Migrations and lang files

If you want to customize the migrations or lang files, you can publish them with:

```bash
php artisan vendor:publish --provider="Novius\LaravelNovaNews\LaravelNovaNewsServiceProvider" --tag="migrations"
```

```bash
php artisan vendor:publish --provider="Novius\LaravelNovaNews\LaravelNovaNewsServiceProvider" --tag="lang"
```

## Helper functions

Some helper functions are available to retrieve posts, categories and tags.  
They're found in the `Novius\LaravelNovaNews\Helpers\NovaNewsHelpers` class.

* **`NovaNewsHelpers::getPosts()`**  
  Retrieve all posts. Use the `$onlyPublished` parameter to filter only published posts. Default is `true`.
* **`NovaNewsHelpers::getPostTagsNames($post)`**  
  Retrieve all tags names for a given post.

## Lint

Lint your code with Laravel Pint using:

```bash
composer run-script lint
```

## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
