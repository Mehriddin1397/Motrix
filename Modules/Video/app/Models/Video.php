<?php

namespace Modules\Video\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Motorcycle\Models\Motorcycle;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Video extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'category_id',
        'motorcycle_id',
        'title',
        'slug',
        'url_or_path',
        'duration_seconds',
        'is_ai_generated',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_ai_generated' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getEmbedUrlAttribute(): ?string
    {
        $url = $this->url_or_path;

        if (! $url) {
            return null;
        }

        if (preg_match('#(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})#i', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        return $url;
    }

    public function getIsDirectVideoAttribute(): bool
    {
        return (bool) preg_match('/\.(mp4|webm|ogg)$/i', parse_url($this->url_or_path, PHP_URL_PATH) ?? '');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VideoCategory::class, 'category_id');
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }
}
