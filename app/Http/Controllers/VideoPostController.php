<?php

namespace App\Http\Controllers;

use App\Models\VideoPost;
use Illuminate\Http\Request;

class VideoPostController extends Controller
{
    public function index()
    {
        $posts = VideoPost::query()
            ->latest()
            ->paginate(12);

        return view('video-posts.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = VideoPost::where('slug', $slug)->firstOrFail();
        $post->increment('views_count');

        $relatedPosts = VideoPost::where('id', '!=', $post->id)
            ->latest()
            ->limit(4)
            ->get();

        return view('video-posts.show', compact('post', 'relatedPosts'));
    }
}
