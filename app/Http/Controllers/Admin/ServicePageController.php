<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServicePage;
use App\Support\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicePageController extends Controller
{
    public function index()
    {
        return view('admin.services.index', [
            'services' => ServicePage::orderBy('sort_order')->orderBy('id')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.services.create', [
            'service' => new ServicePage(['is_active' => true, 'sort_order' => (ServicePage::max('sort_order') ?? 0) + 1]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Slug::unique(ServicePage::class, $data['slug'] ?? null, $data['title']);
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = $this->uploadImage($request);

        $service = ServicePage::create($data);
        $this->syncFaqs($request, $service);

        return redirect()->route('admin.services.index')->with('success', 'صفحه‌ی خدماتی ایجاد شد.');
    }

    public function edit(ServicePage $service)
    {
        $service->load('faqs');

        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, ServicePage $service)
    {
        $data = $this->validated($request);
        $data['slug'] = Slug::unique(ServicePage::class, $data['slug'] ?? null, $data['title'], $service->id);
        $data['is_active'] = $request->boolean('is_active');
        unset($data['image']);

        if ($request->hasFile('image')) {
            $this->deleteImage($service->image);
            $data['image'] = $this->uploadImage($request);
        }

        $service->update($data);
        $this->syncFaqs($request, $service);

        return redirect()->route('admin.services.index')->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(ServicePage $service)
    {
        $this->deleteImage($service->image);
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'صفحه‌ی خدماتی حذف شد.');
    }

    public function toggle(ServicePage $service)
    {
        $service->update(['is_active' => ! $service->is_active]);

        return back()->with('success', 'وضعیت نمایش تغییر کرد.');
    }

    public function move(ServicePage $service, string $direction)
    {
        $neighbour = ServicePage::query()
            ->when($direction === 'up', fn ($query) => $query->where('sort_order', '<', $service->sort_order)->orderByDesc('sort_order'))
            ->when($direction === 'down', fn ($query) => $query->where('sort_order', '>', $service->sort_order)->orderBy('sort_order'))
            ->first();

        if ($neighbour) {
            [$service->sort_order, $neighbour->sort_order] = [$neighbour->sort_order, $service->sort_order];
            $service->save();
            $neighbour->save();
        }

        return back();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'h1' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'canonical' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'faqs' => ['nullable', 'array'],
            'faqs.*.question' => ['nullable', 'string', 'max:500'],
            'faqs.*.answer' => ['nullable', 'string', 'max:5000'],
        ], [], [
            'title' => 'عنوان',
            'slug' => 'آدرس',
            'h1' => 'تیتر اصلی',
            'body' => 'متن',
            'image' => 'عکس شاخص',
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        unset($data['faqs']);

        return $data;
    }

    private function uploadImage(Request $request): ?string
    {
        return $request->hasFile('image')
            ? $request->file('image')->store('uploads/services', 'public')
            : null;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'images/')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function syncFaqs(Request $request, ServicePage $service): void
    {
        $service->faqs()->delete();

        $position = 1;
        foreach ($request->input('faqs', []) as $faq) {
            $question = trim((string) ($faq['question'] ?? ''));
            $answer = trim((string) ($faq['answer'] ?? ''));

            if ($question !== '' && $answer !== '') {
                $service->faqs()->create([
                    'question' => $question,
                    'answer' => $answer,
                    'sort_order' => $position++,
                ]);
            }
        }
    }
}
