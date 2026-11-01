@extends('admin.layouts.app')

@section('title', 'صفحه خدماتی جدید')

@section('content')
    <x-admin.page-header
        title="صفحه خدماتی جدید"
        description="اطلاعات صفحه، متن معرفی، تصویر و سوالات متداول را وارد کنید."
    >
        <a href="{{ route('admin.services.index') }}" class="btn" data-variant="outline">
            <x-icon name="arrow-right" class="size-4" />
            بازگشت به فهرست
        </a>
    </x-admin.page-header>

    @include('admin.services._form')
@endsection
