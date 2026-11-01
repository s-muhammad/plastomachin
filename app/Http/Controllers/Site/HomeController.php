<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Models\ServicePage;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('site.home', [
            'services' => ServicePage::cachedActive(),
            'seo' => SeoPage::data('home'),
        ]);
    }
}
