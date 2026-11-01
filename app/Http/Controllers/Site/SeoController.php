<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\ServicePage;

class SeoController extends Controller
{
    public function sitemap()
    {
        $urls = collect([
            ['loc' => route('home'), 'lastmod' => now()],
            ['loc' => route('contact'), 'lastmod' => now()],
            ['loc' => route('faq'), 'lastmod' => now()],
            ['loc' => route('blog.index'), 'lastmod' => now()],
        ]);

        foreach (ServicePage::active()->orderBy('sort_order')->get() as $service) {
            $urls->push(['loc' => route('services.show', $service->slug), 'lastmod' => $service->updated_at]);
        }

        foreach (Post::published()->latest('published_at')->get() as $post) {
            $urls->push(['loc' => route('blog.show', $post->slug), 'lastmod' => $post->updated_at]);
        }

        return response()
            ->view('site.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $text = "User-agent: *\n";
        $text .= "Disallow: /admin\n";
        $text .= "Allow: /\n\n";
        $text .= 'Sitemap: '.route('sitemap')."\n";

        return response($text)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
