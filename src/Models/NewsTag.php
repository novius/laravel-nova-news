<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Novius\LaravelNovaNews\Database\Factories\NewsTagFactory;
use Novius\LaravelNovaNews\NovaNews;
use Novius\LaravelTranslatable\Traits\Translatable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Tag
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $locale
 * @property int locale_parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class NewsTag extends ModelWithUrl
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;
    use Translatable;

    protected $table = 'nova_news_tags';

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($tag) {
            $locales = config('laravel-nova-news.locales', []);

            if (empty($tag->locale) && count($locales) === 1) {
                $tag->locale = array_key_first($locales);
            }
        });
    }

    public function getFrontRouteName(): ?string
    {
        return config('laravel-nova-news.front_routes_name.tag');
    }

    public function getFrontRouteParameter(): ?string
    {
        return config('laravel-nova-news.front_routes_parameters.tag');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function posts()
    {
        return $this->belongsToMany(NovaNews::getPostModel(), 'nova_news_post_tag', 'news_tag_id', 'news_post_id');
    }

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): Factory
    {
        return NewsTagFactory::new();
    }
}
