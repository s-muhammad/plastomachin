@extends('admin.layouts.app')

@section('title', 'ویرایش صفحه خدماتی')

@section('content')
    <x-admin.page-header
        title="ویرایش صفحه خدماتی"
        :description="'در حال ویرایش: '.$service->title"
    >
        <a href="{{ route('services.show', $service->slug) }}" target="_blank" rel="noopener" class="btn" data-variant="outline">
            <x-icon name="eye" class="size-4" />
            نمایش در سایت
        </a>
        <a href="{{ route('admin.services.index') }}" class="btn" data-variant="ghost">
            <x-icon name="arrow-right" class="size-4" />
            بازگشت
        </a>
    </x-admin.page-header>

    @include('admin.services._form')
@endsection
