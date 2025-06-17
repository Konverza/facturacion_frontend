@props([
    'type' => 'text',
    'name',
    'id' => '',
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
    'legend' => false,
])

@php
    // Determinar la clase de error si existe
    $errorClass = $errors->has($name) ? 'is-invalid' : '';

    // Agregar a la clase del label si es requerido
    $labelClass = $required ? "after:content-['*'] after:ml-0.5 after:text-red-500" : '';

    // Construir clases dinámicas para el input
    $classes = collect([
        'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full py-2.5 px-4',
        'focus:ring-4 focus:ring-gray-200 focus:border-gray-600 focus:outline-none',
        'dark:bg-gray-950 dark:border-gray-800 dark:placeholder-gray-400 dark:text-white',
        'dark:focus:ring-gray-900 dark:focus:border-gray-500',
        'transition duration-300',
        $class,
        $errorClass,
    ])
        ->filter()
        ->join(' ');
@endphp
<div>
    @if ($label && $type !== 'checkbox' && $type !== 'radio' && $type !== 'file' && $type !== 'toggle')
        <div class="mb-1 flex flex-col gap-1">
            <label for="{{ $id }}" title="{{ $label }}"
                class="{{ $labelClass }} mb-1 block overflow-hidden truncate whitespace-nowrap text-wrap text-sm font-medium text-gray-500 dark:text-gray-300">
                {{ $label }}:
                @if ($legend ?? false)
                    <span class="ms-1 text-xs text-zinc-500 dark:text-zinc-400">({{ $legend }})</span>
                @endif
            </label>
        </div>
    @endif

    <div class="relative w-full">
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5">
                <span class="font-medium text-gray-500 dark:text-gray-400">
                    <x-icon icon="{{ $icon }}" class="h-5 w-5 text-current" />
                </span>
            </div>
        @endif

        @if ($type === 'textarea')
            <textarea id="{{ $id }}" {{ $attributes }} name="{{ $name }}" rows="3"
                class="{{ $classes }}" @if ($required) required @endif placeholder="{{ $placeholder }}">{{ $value }}</textarea>
        @elseif ($type === 'checkbox')
            <div class="flex items-start gap-1">
                <input type="checkbox" value="{{ $value }}" name="{{ $name }}"
                    id="{{ $id }}" {{ $attributes }} {{ $checked ? 'checked' : '' }}
                    class="{{ $class }} mt-0.5 h-4 w-4 rounded border-2 border-gray-300 bg-gray-100 text-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-800 dark:bg-gray-950 dark:text-primary-300 dark:ring-offset-gray-800 dark:focus:ring-primary-300">
                <label for="{{ $id }}"
                    class="{{ $labelClass }} ms-1 inline-flex text-sm font-medium text-gray-500 dark:text-gray-300">
                    {{ $label }}
                </label>
            </div>
        @elseif($type === 'radio')
            <input type="radio" value="{{ $value }}" name="{{ $name }}" id="{{ $id }}"
                {{ $attributes }} {{ $checked ? 'checked' : '' }}
                class="{{ $class }} h-4 w-4 rounded-full border-2 border-gray-300 bg-gray-100 text-primary-500 focus:ring-2 focus:ring-primary-500 dark:border-gray-800 dark:bg-gray-950 dark:text-primary-300 dark:ring-offset-gray-800 dark:focus:ring-primary-300">
            <label for="{{ $id }}"
                class="{{ $labelClass }} ms-1 inline-block text-sm font-medium text-gray-500 dark:text-gray-300">
                {{ $label }}
            </label>
        @elseif($type === 'toggle')
            <label class="inline-flex cursor-pointer items-center">
                <input type="checkbox" value="{{ $value }}" name="{{ $name }}"
                    id="{{ $id }}" {{ $attributes }} {{ $checked ? 'checked' : '' }}
                    class="{{ $class }} peer sr-only" {{ $disabled ? 'disabled' : '' }}>
                <div
                    class="{{ $disabled ? 'peer-disabled:bg-primary-100 peer-disabled:after:bg-white dark:peer-disabled:bg-primary-800 dark:peer-disabled:after:bg-gray-500 dark:peer-disabled:after:border-gray-500 cursor-not-allowed' : '' }} peer relative h-5 max-h-5 min-h-5 w-9 min-w-9 max-w-9 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 dark:border-gray-800 dark:bg-gray-900 dark:peer-checked:bg-primary-400 dark:peer-focus:ring-gray-800 rtl:peer-checked:after:-translate-x-full">
                </div>
                <span class="ms-3 text-sm font-medium text-gray-500 dark:text-gray-300">
                    {{ $label }}
                </span>
            </label>
        @elseif($type === 'file')
            <span
                class="@if ($required) after:ml-0.5 after:text-red-500 after:content-['*'] @endif mb-1 block text-sm font-medium text-gray-500 dark:text-gray-300">
                {{ $label }}:
            </span>
            <div
                class="flex flex-col items-start justify-between overflow-hidden rounded-lg border border-gray-300 bg-gray-50 dark:border-gray-800 dark:bg-gray-950">
                <div class="flex w-full justify-between">
                    <div class="flex items-center gap-2">
                        <label for="{{ $id }}"
                            class="flex cursor-pointer items-center gap-1 text-nowrap border-e border-gray-300 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-100 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-900">
                            <input type="file" name="{{ $name }}" id="{{ $id }}"
                                class="input-doc hidden" accept="{{ $accept }}" {{ $attributes }}
                                data-max-mb="{{ $maxSize }}"
                                data-button-remove="#remove-file-{{ $id }}"
                                data-name="#file-name-{{ $id }}" />
                            <x-icon icon="file" class="h-4 w-4 text-gray-600 dark:text-gray-300" />
                            Elegir archivo
                        </label>
                        <p id="file-name-{{ $id }}"
                            title="Formatos permitidos: {{ $accept ?? 'jpg, png, jpeg, webp' }}. Tamaño máximo:
                            {{ $maxSize / 1024 . 'MB' ?? '2MB' }}"
                            class="font-dine-r line-clamp-1 text-xs text-gray-600 dark:text-gray-300">
                            Formatos permitidos: {{ $accept ?? 'jpg, png, jpeg, webp' }}. Tamaño máximo:
                            {{ $maxSize / 1024 . 'MB' ?? '2MB' }}
                        </p>
                    </div>
                    <button class="remove-file me-2 hidden" id="remove-file-{{ $id }}" type="button"
                        data-input="#{{ $id }}">
                        <x-icon icon="x" class="h-4 w-4 text-red-500" />
                    </button>
                </div>
            </div>
        @else
            <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}"
                placeholder="{{ $placeholder }}" value="{{ $value ?? old($name) }}"
                class="{{ $classes }} {{ $icon ? 'ps-10' : '' }}"
                @if ($required) required @endif {{ $attributes }}>

            @if ($type === 'password')
                <button type="button"
                    class="toggle-password absolute inset-y-0 end-0 flex items-center border-s border-gray-300 px-3 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <span id="eye-icon">
                        <x-icon icon="eye" class="h-5 w-5 text-current" />
                    </span>
                    <span id="eye-closed-icon" class="hidden">
                        <x-icon icon="eye-off" class="h-5 w-5 text-current" />
                    </span>
                </button>
            @endif

        @endif
    </div>

    @error($name)
        <div class="mt-2 flex items-center gap-2 text-sm text-red-500">
            <x-icon icon="alert-circle" class="h-4 w-4 min-w-4 max-w-4" />
            <span class="error-msg line-clamp-1 text-ellipsis text-red-500">
                {{ $message }}
            </span>
        </div>
    @enderror
    <div class="mt-2 flex items-center gap-2 text-xs text-red-500" id=error-{{ $id }} style="display: none;">
        <x-icon icon="alert-circle" class="h-3 w-3 min-w-3 max-w-3" />
        <span class="line-clamp-1 text-ellipsis text-red-500">
            El campo {{ strtolower($label) }} es obligatorio
        </span>
    </div>
</div>
