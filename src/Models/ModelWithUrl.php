<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Novius\LaravelPublishable\Traits\Publishable;

abstract class ModelWithUrl extends Model
{
    abstract public function getFrontRouteName(): ?string;

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

        if (in_array(Publishable::class, class_uses_recursive($this), true) && ! $this->isPublished()) {
            $params['previewToken'] = $this->preview_token;
        }

        return route($routeName, $params);
    }

    protected function getUrlParameter(): ?string
    {
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
}
