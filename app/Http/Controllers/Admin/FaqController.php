<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.faqs.index', [
            'faqs' => Faq::general()->orderBy('sort_order')->orderBy('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.faqs.create', [
            'faq' => new Faq(['sort_order' => (Faq::general()->max('sort_order') ?? 0) + 1]),
        ]);
    }

    public function store(Request $request)
    {
        Faq::create($this->validated($request) + ['service_page_id' => null]);

        return redirect()->route('admin.faqs.index')->with('success', 'سوال اضافه شد.');
    }

    public function edit(Faq $faq)
    {
        abort_unless($faq->service_page_id === null, 404);

        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        abort_unless($faq->service_page_id === null, 404);

        $faq->update($this->validated($request));

        return redirect()->route('admin.faqs.index')->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(Faq $faq)
    {
        abort_unless($faq->service_page_id === null, 404);

        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'سوال حذف شد.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [], [
            'question' => 'سوال',
            'answer' => 'پاسخ',
            'sort_order' => 'ترتیب',
        ]);
    }
}
