<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\SeoPage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('site.contact', [
            'seo' => SeoPage::data('contact'),
        ]);
    }

    public function store(Request $request)
    {
        // Bots that fill the hidden field get a normal response but nothing is saved.
        if ($request->filled('website')) {
            return back()->with('success', 'پیام شما ثبت شد. به‌زودی با شما تماس می‌گیریم.');
        }

        $phone = preg_replace('/\D+/', '', (string) $request->input('phone'));
        $phone = preg_replace('/^(?:0098|98)/', '0', $phone);
        $request->merge(['phone' => $phone]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'phone' => ['required', 'string', 'regex:/^0\d{9,10}$/'],
            'message' => ['nullable', 'string', 'max:2000'],
            'source_page' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'نام خود را وارد کنید.',
            'name.min' => 'نام باید حداقل ۳ حرف باشد.',
            'name.max' => 'نام بیش از حد طولانی است.',
            'phone.required' => 'شماره تماس را وارد کنید.',
            'phone.regex' => 'شماره تماس معتبر نیست. نمونه: ۰۹۱۲۱۲۳۴۵۶۷',
            'message.max' => 'متن پیام حداکثر می‌تواند ۲۰۰۰ حرف باشد.',
        ], [
            'name' => 'نام',
            'phone' => 'شماره تماس',
            'message' => 'پیام',
        ]);

        ContactMessage::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'message' => $validated['message'] ?? null,
            'source_page' => $validated['source_page'] ?? url()->previous(),
            'status' => 'new',
        ]);

        return back()->with('success', 'پیام شما ثبت شد. به‌زودی با شما تماس می‌گیریم.');
    }
}
