<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ServicePage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function show(string $slug)
    {
        $service = ServicePage::active()
            ->where('slug', $slug)
            ->with('faqs')
            ->firstOrFail();

        $others = ServicePage::active()
            ->whereKeyNot($service->getKey())
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(4)
            ->get();

        $seo = [
            'title' => $service->seo_title ?: $service->h1.' | '.config('site.defaults.site_name'),
            'description' => $service->seo_description ?: Str::limit(strip_tags($service->excerpt ?: $service->body), 155),
            'canonical' => $service->canonical ?: url()->current(),
            'og_title' => $service->seo_title ?: $service->h1,
            'og_description' => $service->seo_description ?: $service->excerpt,
            'og_image' => $service->image_url,
        ];

        return view('site.services.show', compact('service', 'others', 'seo'));
    }
}
