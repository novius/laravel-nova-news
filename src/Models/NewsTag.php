<?php

namespace Novius\LaravelNovaNews\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Tag
 *
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class NewsTag extends Model
{
    use HasFactory;

    protected $table = 'nova_news_tags';

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function posts()
    {
        return $this->belongsToMany(NewsPost::class, 'nova_news_post_tag');
    }
}
