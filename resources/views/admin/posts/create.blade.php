@extends('admin.layouts.app')

@section('title', 'مقاله‌ی جدید')

@section('content')
    <x-admin.page-header
        title="مقاله‌ی جدید"
        description="عنوان، متن و تصویر مقاله را وارد کنید. انتشار می‌تواند به آینده موکول شود."
    >
        <a href="{{ route('admin.posts.index') }}" class="btn" data-variant="outline">
            <x-icon name="arrow-right" class="size-4" />
            بازگشت به فهرست
        </a>
    </x-admin.page-header>

    @include('admin.posts._form')
@endsection
