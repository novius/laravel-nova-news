<?php

namespace Novius\LaravelNovaNews;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;
use Novius\LaravelLinkable\Facades\Linkable;
use Novius\LaravelNovaNews\Console\FrontControllerCommand;
use Novius\LaravelNovaNews\Models\NewsCategory;
use Novius\LaravelNovaNews\Models\NewsPost;

class LaravelNovaNewsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-nova-news.php', 'laravel-nova-news');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            // Load Nova resources
            Nova::resources(array_filter([
                NovaNews::getPostResource(),
                NovaNews::getCategoryResource(),
                NovaNews::getTagResource(),
            ]));
        });

        $this->app->booted(function () {
            Linkable::addModels(array_filter([
                NovaNews::getPostModel(),
                NovaNews::getCategoryModel(),
                NovaNews::getTagModel(),
            ]));
            Linkable::addRoutes(array_flip(array_filter([
                trans('laravel-nova-news::crud-post.resource_label') => config('laravel-nova-news.front_routes_name.posts'),
                trans('laravel-nova-news::crud-category.resource_label') => config('laravel-nova-news.front_routes_name.categories'),
            ])));
        });

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'laravel-nova-news');

        $this->publishes([
            __DIR__.'/../lang' => lang_path('vendor/laravel-nova-news'),
        ], 'lang');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../config/laravel-nova-news.php' => config_path('laravel-nova-news.php'),
        ], 'config');

        Validator::extend('newsSlug', function ($attr, $value) {
            return is_string($value) && preg_match('/^[a-zA-Z0-9-_]+$/', $value);
        });

        Validator::extend('uniquePost', function ($attr, $value, $parameters) {
            if (empty($parameters[0])) {
                return false;
            }

            $resourceId = $parameters[1] ?? null;
            $query = NewsPost::where('locale', $parameters[0])
                ->where('slug', $value);
            if ($resourceId) {
                $query->where('id', '<>', $resourceId);
            }

            return empty($query->first());
        });

        Validator::extend('uniqueCategory', function ($attr, $value, $parameters) {
            if (empty($parameters[0])) {
                return false;
            }

            $resourceId = $parameters[1] ?? null;
            $query = NewsCategory::where('locale', $parameters[0])
                ->where('slug', $value);
            if ($resourceId) {
                $query->where('id', '<>', $resourceId);
            }

            return empty($query->first());
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                FrontControllerCommand::class,
            ]);
        }
    }
}
