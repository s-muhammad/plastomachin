@extends('admin.layouts.app')

@section('title', 'سوال متداول جدید')

@section('content')
    <x-admin.page-header
        title="سوال متداول جدید"
        description="پرسش و پاسخ را کوتاه و روشن بنویسید."
    >
        <a href="{{ route('admin.faqs.index') }}" class="btn" data-variant="outline">
            <x-icon name="arrow-right" class="size-4" />
            بازگشت به فهرست
        </a>
    </x-admin.page-header>

    @include('admin.faqs._form')
@endsection
