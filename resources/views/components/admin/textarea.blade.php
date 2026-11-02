@props([
    'name',
    'label',
    'value' => null,
    'rows' => 5,
    'help' => null,
    'required' => false,
    'dir' => null,
    'editor' => false,
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

    <textarea
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        @if ($dir) dir="{{ $dir }}" @endif
        @if ($editor) data-editor @endif
        @if ($invalid) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <p id="{{ $inputId }}-error" role="alert">{{ $message }}</p>
    @else
        @if ($help)
            <p>{{ $help }}</p>
        @endif
    @enderror
</div>
