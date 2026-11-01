@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'help' => null,
    'required' => false,
    'dir' => null,
])

@php
    $invalid = $errors->has($name);
    $inputId = 'field-'.$name;
@endphp

<div class="field" @if ($invalid) data-invalid @endif>
    <label for="{{ $inputId }}">
        {{ $label }}
        @if ($required)
            <span class="text-destructive">*</span>
        @endif
    </label>

    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'file') value="{{ old($name, $value) }}" @endif
        @if ($required) required @endif
        @if ($dir) dir="{{ $dir }}" @endif
        @if ($invalid) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
        {{ $attributes }}
    >

    @error($name)
        <p id="{{ $inputId }}-error" role="alert">{{ $message }}</p>
    @else
        @if ($help)
            <p>{{ $help }}</p>
        @endif
    @enderror
</div>
