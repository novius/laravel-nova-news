<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Novius\LaravelNovaNews\NovaNews;

/**
 * Class Tag
 *
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class NewsTag extends Model
{
    protected $table = 'nova_news_tags';

    protected $fillable = [
        'name',
    ];

    public function posts()
    {
        return $this->belongsToMany(NovaNews::getPostModel(), 'nova_news_post_tag', 'news_tag_id', 'news_post_id');
    }
}
