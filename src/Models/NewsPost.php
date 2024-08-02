<?php

namespace Novius\LaravelNovaNews\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Novius\LaravelMeta\Enums\IndexFollow;
use Novius\LaravelMeta\MetaModelConfig;
use Novius\LaravelMeta\Traits\HasMeta;
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
 * @property array $extras
 * @property PublicationStatus $publication_status
 * @property Carbon|null $published_first_at
 * @property Carbon|null $published_at
 * @property Carbon|null $expired_at
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
 * @method static Builder|NewsPost indexableByRobots()
 * @method static Builder|NewsPost newModelQuery()
 * @method static Builder|NewsPost newQuery()
 * @method static Builder|NewsPost notIndexableByRobots()
 * @method static Builder|NewsPost notPublished()
 * @method static Builder|NewsPost onlyDrafted()
 * @method static Builder|NewsPost onlyExpired()
 * @method static Builder|NewsPost onlyWillBePublished()
 * @method static Builder|NewsPost published()
 * @method static Builder|NewsPost query()
 * @method static Builder|NewsPost withLocale(?string $locale)
 *
 * @mixin Eloquent
 */
class NewsPost extends ModelWithUrl
{
    use HasFactory;
    use HasMeta;
    use HasSlug;
    use Publishable;
    use SoftDeletes;
    use Translatable;

    protected $table = 'nova_news_posts';

    protected $guarded = ['id'];

    protected $casts = [
        'extras' => 'json',
    ];

    protected static function booted(): void
    {
        static::saving(static function ($post) {
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

    public function getMetaConfig(): MetaModelConfig
    {
        if (! isset($this->metaConfig)) {
            $this->metaConfig = MetaModelConfig::make()
                ->setDefaultSeoRobots(IndexFollow::index_follow)
                ->setFallbackTitle('title')
                ->setFallbackDescription('intro')
                ->setFallbackImage('featured_image');
        }

        return $this->metaConfig;
    }

    public function localParent(): HasOne
    {
        return $this->hasOne(static::class, 'id', 'locale_parent_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NovaNews::getCategoryModel(), 'nova_news_post_category', 'news_post_id', 'news_category_id');
    }

    public function tags(): BelongsToMany
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
