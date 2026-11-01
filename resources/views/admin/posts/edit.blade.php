@extends('admin.layouts.app')

@section('title', 'ویرایش مقاله')

@section('content')
    <x-admin.page-header
        title="ویرایش مقاله"
        :description="'در حال ویرایش: '.$post->title"
    >
        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener" class="btn" data-variant="outline">
            <x-icon name="eye" class="size-4" />
            نمایش در سایت
        </a>
        <a href="{{ route('admin.posts.index') }}" class="btn" data-variant="ghost">
            <x-icon name="arrow-right" class="size-4" />
            بازگشت
        </a>
    </x-admin.page-header>

    @include('admin.posts._form')
@endsection
