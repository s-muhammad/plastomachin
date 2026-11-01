<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\SeoPage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        return view('site.blog.index', [
            'posts' => Post::published()->latest('published_at')->paginate(9),
            'seo' => SeoPage::data('blog'),
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $related = Post::published()
            ->whereKeyNot($post->getKey())
            ->latest('published_at')
            ->limit(2)
            ->get();

        $seo = [
            'title' => $post->seo_title ?: $post->title.' | '.config('site.defaults.site_name'),
            'description' => $post->seo_description ?: Str::limit(strip_tags($post->excerpt ?: $post->body), 155),
            'canonical' => $post->canonical ?: url()->current(),
            'og_title' => $post->seo_title ?: $post->title,
            'og_description' => $post->seo_description ?: $post->excerpt,
            'og_image' => $post->image_url,
            'type' => 'article',
            'published_at' => $post->published_at?->toIso8601String(),
        ];

        return view('site.blog.show', compact('post', 'related', 'seo'));
    }
}
