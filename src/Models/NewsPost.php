<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Novius\LaravelPublishable\Traits\Publishable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Post
 *
 * @property string $title
 * @property string $slug
 * @property string $locale
 * @property bool $featured
 * @property string $intro
 * @property string $content
 * @property string $featured_image
 * @property string $card_image
 * @property string $post_status
 * @property NewsCategory $categories
 * @property NewsTag $tags
 * @property string $preview_token
 * @property string $seo_title
 * @property string $seo_description
 * @property string $og_title
 * @property string $og_description
 * @property string $og_image
 * @property array $extras
 * @property Carbon|null $published_first_at
 * @property Carbon|null $published_at
 * @property Carbon|null $expired_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class NewsPost extends Model
{
    use HasSlug;
    use Publishable;

    protected $table = 'nova_news_posts';

    protected $guarded = ['id'];

    protected $casts = [
        'extras' => 'json',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($post) {
            if (empty($post->preview_token)) {
                $post->preview_token = Str::random();
            }

            $locales = config('laravel-nova-news.locales', []);

            if (empty($post->locale) && count($locales) === 1) {
                $post->locale = array_key_first($locales);
            }
        });
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function url(): ?string
    {
        $routeName = config('laravel-nova-news.front_route_name');

        if (empty($routeName) || ! Route::has($routeName) || ! $this->exists) {
            return null;
        }

        return route($routeName, [
            'slug' => $this->slug,
        ]);
    }

    public function previewUrl(): ?string
    {
        $routeName = config('laravel-nova-news.front_route_name');

        if (empty($routeName) || ! Route::has($routeName) || ! $this->exists) {
            return null;
        }

        $params = [
            'slug' => $this->slug,
        ];

        if (! $this->isPublished()) {
            $params['previewToken'] = $this->preview_token;
        }

        return route($routeName, $params);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function categories()
    {
        return $this->belongsToMany(config('laravel-nova-news.category_model'), 'nova_news_post_category', 'news_post_id', 'news_category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(config('laravel-nova-news.tag_model'), 'nova_news_post_tag', 'news_post_id', 'news_tag_id');
    }
}
