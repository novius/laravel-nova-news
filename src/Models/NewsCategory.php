<?php

namespace Novius\LaravelNovaNews\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Novius\LaravelMeta\Enums\IndexFollow;
use Novius\LaravelMeta\MetaModelConfig;
use Novius\LaravelMeta\Traits\HasMeta;
use Novius\LaravelNovaNews\Database\Factories\NewsCategoryFactory;
use Novius\LaravelNovaNews\NovaNews;
use Novius\LaravelTranslatable\Traits\Translatable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $locale
 * @property int locale_parent_id
 * @property array $extras
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string|null $seo_robots
 * @property-read string|null $seo_title
 * @property-read string|null $seo_description
 * @property-read string|null $seo_keywords
 * @property-read string|null $og_type
 * @property-read string|null $og_title
 * @property-read string|null $og_description
 * @property-read string|null $og_image
 * @property-read string|null $og_image_url
 *
 * @method static Builder|NewsCategory indexableByRobots()
 * @method static Builder|NewsCategory newModelQuery()
 * @method static Builder|NewsCategory newQuery()
 * @method static Builder|NewsCategory notIndexableByRobots()
 * @method static Builder|NewsCategory query()
 *
 * @mixin Eloquent
 */
class NewsCategory extends ModelWithUrl
{
    use HasFactory;
    use HasMeta;
    use HasSlug;
    use SoftDeletes;
    use Translatable;

    protected $table = 'nova_news_categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $casts = [
        'extras' => 'json',
    ];

    protected static function booted(): void
    {
        static::saving(function ($category) {
            $locales = config('laravel-nova-news.locales', []);

            if (empty($category->locale) && count($locales) === 1) {
                $category->locale = array_key_first($locales);
            }
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getMetaConfig(): MetaModelConfig
    {
        if (! isset($this->metaConfig)) {
            $this->metaConfig = MetaModelConfig::make()
                ->setDefaultSeoRobots(IndexFollow::index_follow)
                ->setFallbackTitle('name');
        }

        return $this->metaConfig;
    }

    public function getFrontRouteName(): ?string
    {
        return config('laravel-nova-news.front_routes_name.category');
    }

    public function getFrontRouteParameter(): ?string
    {
        return config('laravel-nova-news.front_routes_parameters.category');
    }

    public function localParent()
    {
        return $this->hasOne(static::class, 'id', 'locale_parent_id');
    }

    public function posts()
    {
        return $this->belongsToMany(NovaNews::getPostModel(), 'nova_news_post_category', 'news_category_id', 'news_post_id');
    }

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): Factory
    {
        return NewsCategoryFactory::new();
    }
}
