<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Novius\LaravelNovaNews\Database\Factories\NewsPostFactory;
use Novius\LaravelNovaNews\NovaNews;
use Novius\LaravelPublishable\Enums\PublicationStatus;
use Novius\LaravelPublishable\Traits\Publishable;
use Novius\LaravelTranslatable\Traits\Translatable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Post
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $locale
 * @property int locale_parent_id
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
 * @property PublicationStatus $publication_status
 * @property Carbon|null $published_first_at
 * @property Carbon|null $published_at
 * @property Carbon|null $expired_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class NewsPost extends ModelWithUrl
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;
    use Publishable;
    use Translatable;

    protected $table = 'nova_news_posts';

    protected $guarded = ['id'];

    protected $casts = [
        'extras' => 'json',
    ];

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

    public function getFrontRouteName(): ?string
    {
        return config('laravel-nova-news.front_routes_name.post');
    }

    public function getFrontRouteParameter(): ?string
    {
        return config('laravel-nova-news.front_routes_parameters.post');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function localParent()
    {
        return $this->hasOne(static::class, 'id', 'locale_parent_id');
    }

    public function categories()
    {
        return $this->belongsToMany(NovaNews::getCategoryModel(), 'nova_news_post_category', 'news_post_id', 'news_category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(NovaNews::getTagModel(), 'nova_news_post_tag', 'news_post_id', 'news_tag_id');
    }

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): Factory
    {
        return NewsPostFactory::new();
    }
}
