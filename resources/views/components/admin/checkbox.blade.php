@props([
    'name',
    'label',
    'checked' => false,
    'help' => null,
])

@php
    $invalid = $errors->has($name);
    $inputId = 'field-'.$name;
@endphp

<div class="field" @if ($invalid) data-invalid @endif>
    <label for="{{ $inputId }}" class="!w-full cursor-pointer">
        <input
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="checkbox"
            value="1"
            @checked(old($name, $checked))
            @if ($invalid) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
            {{ $attributes }}
        >
        <span class="text-sm font-bold text-navy">{{ $label }}</span>
    </label>

    @error($name)
        <p id="{{ $inputId }}-error" role="alert">{{ $message }}</p>
    @else
        @if ($help)
            <p>{{ $help }}</p>
        @endif
    @enderror
</div>
