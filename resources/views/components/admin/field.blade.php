@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'help' => null,
    'required' => false,
    'dir' => null,
    'id' => null,
    'counter' => null,
])

@php
    $invalid = $errors->has($name);
    $inputId = $id ?? 'field-'.$name;
@endphp

<div class="field min-w-0" @if ($invalid) data-invalid @endif>
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

    @if ($counter)
        <p class="text-[11px] text-slate-400" data-char-counter="{{ $inputId }}" data-max="{{ $counter }}"></p>
    @endif

    @error($name)
        <p id="{{ $inputId }}-error" role="alert">{{ $message }}</p>
    @else
        @if ($help)
            <p>{{ $help }}</p>
        @endif
    @enderror
</div>
