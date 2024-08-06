<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Novius\LaravelLinkable\Configs\LinkableConfig;
use Novius\LaravelLinkable\Traits\Linkable;
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
class NewsTag extends Model
{
    use HasFactory;
    use HasSlug;
    use Linkable;
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected ?LinkableConfig $_linkableConfig;

    public function linkableConfig(): ?LinkableConfig
    {
        $route = config('laravel-nova-news.front_routes_name.tag');
        $routeParameterName = config('laravel-nova-news.front_routes_parameters.tag');
        if (empty($routeParameterName) && empty($route)) {
            return null;
        }

        if (! isset($this->_linkableConfig)) {
            $this->_linkableConfig = new LinkableConfig(
                routeName: $route,
                routeParameterName: $routeParameterName,
                optionLabel: 'name',
                optionGroup: trans('laravel-nova-news::crud-tag.resource_label'),
                resolveQuery: function (Builder|NewsCategory $query) {
                    $query->where('locale', app()->currentLocale());
                },
            );
        }

        return $this->_linkableConfig;
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
