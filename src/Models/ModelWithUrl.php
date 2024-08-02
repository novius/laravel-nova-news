<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Novius\LaravelPublishable\Traits\Publishable;

abstract class ModelWithUrl extends Model
{
    abstract public function getFrontRouteName(): ?string;

    abstract public function getFrontRouteParameter(): ?string;

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function url(): ?string
    {
        $routeName = $this->getFrontRouteName();
        $parameter = $this->getUrlParameter();

        if ($routeName === null || ! $this->exists || ! $parameter) {
            return null;
        }

        return route($routeName, [
            $parameter => $this->slug,
        ]);
    }

    public function previewUrl(): ?string
    {
        $routeName = $this->getFrontRouteName();
        $parameter = $this->getUrlParameter();

        if (empty($routeName) || ! $parameter || ! $this->exists) {
            return null;
        }

        $params = [
            $parameter => $this->slug,
        ];

        $guard = config('laravel-nova-news.guard_preview');
        if (empty($guard) && in_array(Publishable::class, class_uses_recursive($this), true) && ! $this->isPublished()) {
            $params['previewToken'] = $this->preview_token;
        }

        return route($routeName, $params);
    }

    protected function getUrlParameter(): ?string
    {
        $parameter = $this->getFrontRouteParameter();
        if (! empty($parameter)) {
            return $parameter;
        }

        $routeName = $this->getFrontRouteName();
        if (empty($routeName)) {
            return null;
        }

        $route = Route::getRoutes()->getByName($routeName);
        if (! $route) {
            return null;
        }

        if (! preg_match('/({\w+})/', $route->uri(), $matches)) {
            return null;
        }

        return substr($matches[0], 1, -1);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $query = static::where('locale', app()->currentLocale());

        if (in_array(Publishable::class, class_uses_recursive($this), true)) {
            $guard = config('laravel-nova-news.guard_preview');
            if (! empty($guard) && Auth::guard($guard)->check()) {
                return $this->resolveRouteBindingQuery($query, $value, $field)->first();
            }

            if (request()->has('previewToken')) {
                $query->where(function (Builder $query) {
                    $query->published()
                        ->orWhere('preview_token', request()->get('previewToken'));
                });

                return $this->resolveRouteBindingQuery($query, $value, $field)->first();
            }

            return $this->resolveRouteBindingQuery($query->published(), $value, $field)->first();
        }

        return $this->resolveRouteBindingQuery($query, $value, $field)->first();
    }
}
