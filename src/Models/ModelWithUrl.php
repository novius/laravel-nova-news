<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Novius\LaravelPublishable\Traits\Publishable;

abstract class ModelWithUrl extends Model
{
    abstract public function getFrontRouteName(): ?string;

    public function url(): ?string
    {
        $routeName = $this->getFrontRouteName();

        if ($routeName === null || ! $this->exists || ! Route::has($routeName)) {
            return null;
        }

        return route($routeName, [
            'slug' => $this->slug,
        ]);
    }

    public function previewUrl(): ?string
    {
        $routeName = $this->getFrontRouteName();

        if (empty($routeName) || ! Route::has($routeName) || ! $this->exists) {
            return null;
        }

        $params = [
            'slug' => $this->slug,
        ];

        if (in_array(Publishable::class, class_uses($this), true) && ! $this->isPublished()) {
            $params['previewToken'] = $this->preview_token;
        }

        return route($routeName, $params);
    }
}
