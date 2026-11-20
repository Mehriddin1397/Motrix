<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class VideoPost extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'video_path',
        'thumbnail_path',
        'duration_seconds',
        'views_count',
        'send_to_telegram',
        'send_to_youtube',
        'send_to_facebook',
        'send_to_instagram',
        'telegram_status',
        'youtube_status',
        'facebook_status',
        'instagram_status',
        'telegram_message_id',
        'youtube_video_id',
        'facebook_post_id',
        'instagram_media_id',
        'error_log',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'send_to_telegram' => 'boolean',
            'send_to_youtube' => 'boolean',
            'send_to_facebook' => 'boolean',
            'send_to_instagram' => 'boolean',
            'published_at' => 'datetime',
            'views_count' => 'integer',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getVideoUrlAttribute(): ?string
    {
        if (! $this->video_path) {
            return null;
        }

        return Storage::disk('public')->url($this->video_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail_path) {
            return null;
        }

        return Storage::disk('public')->url($this->thumbnail_path);
    }

    public function getPublicUrlAttribute(): string
    {
        return 'https://motrix.uz/video-posts/' . $this->slug;
    }
}
