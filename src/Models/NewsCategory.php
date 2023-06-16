<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Novius\LaravelNovaNews\NovaNews;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $delete_at
 */
class NewsCategory extends ModelWithUrl
{
    use HasSlug;
    use SoftDeletes;

    protected $table = 'nova_news_categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getFrontRouteName(): ?string
    {
        return config('laravel-nova-news.front_routes_name.category');
    }

    public function posts()
    {
        return $this->belongsToMany(NovaNews::getPostModel(), 'nova_news_post_category', 'news_category_id', 'news_post_id');
    }
}
