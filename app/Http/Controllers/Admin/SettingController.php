<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'values' => Setting::values(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:300'],
            'working_hours' => ['nullable', 'string', 'max:200'],
            'map_url' => ['nullable', 'string', 'max:1000'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['required', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ], [], [
            'site_name' => 'نام سایت',
            'phone' => 'شماره تلفن',
            'whatsapp' => 'شماره واتساپ',
            'address' => 'آدرس',
            'meta_title' => 'متای پیش‌فرض',
            'meta_description' => 'توضیح متای پیش‌فرض',
        ]);

        $data['socials_enabled'] = $request->boolean('socials_enabled') ? '1' : '0';

        Setting::setMany($data);

        return back()->with('success', 'تنظیمات ذخیره شد.');
    }
}
