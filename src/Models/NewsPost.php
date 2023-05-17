<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
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
 * @property int $status
 * @property Carbon $publication_date
 * @property Carbon $end_publication_date
 * @property string $preview_token
 * @property string $seo_title
 * @property string $seo_description
 * @property string $og_title
 * @property string $og_description
 * @property string $og_image
 * @property array $extras
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class NewsPost extends Model
{
    use HasSlug;
    use SoftDeletes;

    protected $table = 'nova_news_posts';

    protected $guarded = ['id'];

    const STATUS_DRAFT = 'Draft';

    const STATUS_PUBLISHED = 'Published';

    protected $casts = [
        'publication_date' => 'datetime',
        'end_publication_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function scopePublished(Builder $query): Builder
    {
        $now = now();

        return $query->where('post_status', self::STATUS_PUBLISHED)
            ->where('publication_date', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->where('end_publication_date', '>=', $now)
                    ->orWhereNull('end_publication_date');
            });
    }

    public function scopeNotPublished(Builder $query): Builder
    {
        $now = now();

        return $query->where('post_status', self::STATUS_DRAFT)
            ->orWhere('publication_date', '>', $now)
            ->orWhere(function ($query) use ($now) {
                $query->where('end_publication_date', '<', $now)
                    ->whereNotNull('end_publication_date');
            });
    }

    public function isPublished(): bool
    {
        $now = now();

        return $this->post_status === self::STATUS_PUBLISHED
            && $this->publication_date <= $now
            && ($this->end_publication_date === null || $this->end_publication_date >= $now);
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
        return $this->belongsToMany(NewsCategory::class, 'nova_news_post_category');
    }

    public function tags()
    {
        return $this->belongsToMany(NewsTag::class, 'nova_news_post_tag');
    }
}
