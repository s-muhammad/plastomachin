@extends('admin.layouts.app')

@section('title', 'ویرایش سوال متداول')

@section('content')
    <x-admin.page-header
        title="ویرایش سوال متداول"
        :description="\Illuminate\Support\Str::limit($faq->question, 90)"
    >
        <a href="{{ route('admin.faqs.index') }}" class="btn" data-variant="outline">
            <x-icon name="arrow-right" class="size-4" />
            بازگشت به فهرست
        </a>
    </x-admin.page-header>

    @include('admin.faqs._form')
@endsection
