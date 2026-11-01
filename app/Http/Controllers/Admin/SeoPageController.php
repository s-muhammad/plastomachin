<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeoPageController extends Controller
{
    public function index()
    {
        return view('admin.seo.index', [
            'pages' => SeoPage::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, SeoPage $seoPage)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'canonical' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [], [
            'title' => 'عنوان متا',
            'description' => 'توضیح متا',
            'og_image' => 'عکس شبکه‌های اجتماعی',
        ]);

        if ($request->hasFile('og_image')) {
            if ($seoPage->og_image && ! str_starts_with($seoPage->og_image, 'images/')) {
                Storage::disk('public')->delete($seoPage->og_image);
            }
            $data['og_image'] = $request->file('og_image')->store('uploads/seo', 'public');
        }

        $seoPage->update($data);

        return back()->with('success', 'اطلاعات سئوی صفحه ذخیره شد.');
    }
}
