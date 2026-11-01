<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        return view('admin.posts.index', [
            'posts' => Post::latest('created_at')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.posts.create', [
            'post' => new Post(['status' => 'draft']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Slug::unique(Post::class, $data['slug'] ?? null, $data['title']);
        $data = $this->prepareStatus($request, $data);
        $data['image'] = $this->uploadImage($request);

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'مقاله ایجاد شد.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request);
        $data['slug'] = Slug::unique(Post::class, $data['slug'] ?? null, $data['title'], $post->id);
        $data = $this->prepareStatus($request, $data);
        unset($data['image']);

        if ($request->hasFile('image')) {
            $this->deleteImage($post->image);
            $data['image'] = $this->uploadImage($request);
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'تغییرات ذخیره شد.');
    }

    public function destroy(Post $post)
    {
        $this->deleteImage($post->image);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'مقاله حذف شد.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'canonical' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ], [], [
            'title' => 'عنوان',
            'slug' => 'آدرس',
            'body' => 'متن',
            'image' => 'عکس شاخص',
            'status' => 'وضعیت',
        ]);
    }

    private function prepareStatus(Request $request, array $data): array
    {
        if ($data['status'] === 'published') {
            $data['published_at'] = $data['published_at'] ?? now();
        } else {
            $data['published_at'] = $data['published_at'] ?? null;
        }

        return $data;
    }

    private function uploadImage(Request $request): ?string
    {
        return $request->hasFile('image')
            ? $request->file('image')->store('uploads/posts', 'public')
            : null;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'images/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
