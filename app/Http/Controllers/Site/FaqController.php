<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\SeoPage;

class FaqController extends Controller
{
    public function __invoke()
    {
        return view('site.faq', [
            'faqs' => Faq::general()->orderBy('sort_order')->orderBy('id')->get(),
            'seo' => SeoPage::data('faq'),
        ]);
    }
}
