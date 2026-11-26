<?php

namespace App\Http\Controllers;

use App\Models\VideoPost;
use Illuminate\Http\Request;

class VideoPostController extends Controller
{
    public function index()
    {
        $posts = VideoPost::query()
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            })
            ->latest()
            ->paginate(12);

        return view('video-posts.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = VideoPost::where('slug', $slug)
            ->where(function ($q) {
                if (auth()->check() && (auth()->user()->hasRole('administrator') || auth()->user()->hasRole('moderator'))) {
                    return;
                }
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            })
            ->firstOrFail();
        $post->increment('views_count');

        $relatedPosts = VideoPost::where('id', '!=', $post->id)
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            })
            ->latest()
            ->limit(4)
            ->get();

        return view('video-posts.show', compact('post', 'relatedPosts'));
    }
}
