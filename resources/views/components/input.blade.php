@props([
    'type' => 'text',
    'name',
    'id' => $name,
    'label' => '',
    'checked' => false,
    'placeholder' => '',
    'value' => '',
    'class' => '',
    'required' => false,
    'icon' => '',
    'error' => true,
    'maxSize' => 1024,
    'accept' => 'jpg, png, jpeg, webp',
    'disabled' => false,
])

@php
    $errorClass = $errors->has($name) ? 'is-invalid' : '';
    $labelClass = $required ? "after:content-['*'] after:ml-0.5 after:text-red-500" : '';

    $classes = collect([
        'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full py-2.5 px-4',
        'focus:ring-4 focus:ring-gray-200 focus:border-gray-600 focus:outline-none',
        'dark:bg-gray-950 dark:border-gray-800 dark:placeholder-gray-400 dark:text-white',
        'dark:focus:ring-gray-900/30 dark:focus:border-gray-500',
        'transition duration-300',
        $class,
        $errorClass,
    ])
        ->filter()
        ->join(' ');
@endphp

<div>
    @if ($label && !in_array($type, ['checkbox', 'radio', 'file', 'toggle']))
        <label for="{{ $id }}"
            class="{{ $labelClass }} mb-1 block text-sm font-medium text-gray-500 dark:text-gray-300">
            {{ $label }}:
        </label>
    @endif

    <div class="relative w-full">
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5">
                <x-icon icon="{{ $icon }}" class="h-5 w-5 text-current" />
            </div>
        @endif

        @switch($type)
            @case('textarea')
                <textarea rows="3" class="{{ $classes }}"
                    {{ $attributes->merge([
                        'name' => $name,
                        'id' => $id,
                        'placeholder' => $placeholder,
                        'required' => $required,
                        'disabled' => $disabled,
                    ]) }}>{{ old($name, $value) }}</textarea>
            @break

            @case('checkbox')
                <input type="checkbox"
                    {{ $attributes->merge([
                        'name' => $name,
                        'id' => $id,
                        'value' => $value,
                        'checked' => $checked,
                        'class' => "$class h-4 w-4 rounded border-gray-300 text-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-800 dark:bg-gray-950 dark:text-primary-300 dark:ring-offset-gray-800 dark:focus:ring-primary-300",
                    ]) }}>
                <label for="{{ $id }}"
                    class="{{ $labelClass }} ms-1 text-sm font-medium text-gray-500 dark:text-gray-300">
                    {{ $label }}
                </label>
            @break

            @case('radio')
                <input type="radio"
                    {{ $attributes->merge([
                        'name' => $name,
                        'id' => $id,
                        'value' => $value,
                        'checked' => $checked,
                        'class' => "$class h-4 w-4 rounded-full border-2 border-gray-300 bg-gray-100 text-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-800 dark:bg-gray-950 dark:text-primary-300 dark:ring-offset-gray-800 dark:focus:ring-primary-300",
                    ]) }}>
                <label for="{{ $id }}"
                    class="{{ $labelClass }} ms-1 text-sm font-medium text-gray-500 dark:text-gray-300">
                    {{ $label }}
                </label>
            @break

            @case('toggle')
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="peer sr-only {{ $class }}"
                        {{ $attributes->merge([
                            'name' => $name,
                            'id' => $id,
                            'value' => $value,
                            'checked' => $checked,
                            'disabled' => $disabled,
                        ]) }}>
                    <div
                        class="relative w-9 h-5 bg-gray-200 rounded-full peer-checked:bg-primary-500 after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-gray-300 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 dark:bg-gray-900 dark:peer-checked:bg-primary-300 dark:peer-focus:ring-gray-800">
                    </div>
                    <span class="ms-3 text-sm text-gray-500 dark:text-gray-300">{{ $label }}</span>
                </label>
            @break

            @case('file')
                <label class="block text-sm font-medium text-gray-500 dark:text-gray-300 mb-1">{{ $label }}:</label>
                <div class="border border-dashed rounded-lg p-2 dark:border-gray-800">
                    <input type="file" class="hidden"
                        {{ $attributes->merge([
                            'name' => $name,
                            'id' => $id,
                            'accept' => $accept,
                        ]) }}
                        data-max-mb="{{ $maxSize }}" />
                    <label for="{{ $id }}"
                        class="cursor-pointer inline-flex items-center text-sm text-gray-600 dark:text-gray-300 gap-2">
                        <x-icon icon="file" class="h-4 w-4" />
                        Elegir archivo
                    </label>
                    <p class="text-xs text-gray-500 mt-1">
                        Formatos permitidos: {{ $accept }}. Tamaño máximo: {{ $maxSize / 1024 }}MB
                    </p>
                </div>
            @break

            @default
                <input type="{{ $type }}" class="{{ $classes }} {{ $icon ? 'ps-10' : '' }}"
                    {{ $attributes->merge([
                        'name' => $name,
                        'id' => $id,
                        'placeholder' => $placeholder,
                        'value' => old($name, $value),
                        'required' => $required,
                        'disabled' => $disabled,
                    ]) }}>
                @if ($type === 'password')
                    <button type="button"
                        class="toggle-password absolute inset-y-0 end-0 flex items-center border-s border-gray-300 px-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                        <x-icon icon="eye" class="h-5 w-5" />
                    </button>
                @endif
        @endswitch
    </div>

    {{-- Error Message --}}
    @if ($error && $errors->has($name))
        <div class="mt-2 text-sm text-red-500 flex items-center gap-2">
            <x-icon icon="alert-circle" class="h-4 w-4" />
            {{ $errors->first($name) }}
        </div>
    @endif
</div>
