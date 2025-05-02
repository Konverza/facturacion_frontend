@props([
    'type',
    'text',
    'icon',
    'typeButton',
    'class',
    'iconAlign' => 'left',
    'onlyIcon' => false,
    'size' => 'normal',
    'loading' => false, // Añadido para el estado de carga
    'rounded' => true,
])

@php
    // Definir las clases según el tamaño
    $sizes = [
        'small' => [
            'padding' => 'px-3 py-2',
            'text' => 'text-xs',
            'icon' => 'h-4 w-4',
        ],
        'normal' => [
            'padding' => 'px-4 py-2.5',
            'text' => 'text-sm',
            'icon' => 'h-5 w-5',
        ],
        'large' => [
            'padding' => 'px-6 py-3',
            'text' => 'text-lg',
            'icon' => 'h-6 w-6',
        ],
    ];

    // Establecer el padding dependiendo de si es solo ícono o no
    $padding = $onlyIcon ? 'p-2' : $sizes[$size]['padding'];

    // Clases base
    $baseClasses =
        'font-medium flex items-center justify-center gap-1 transition-colors transition duration-300 text-nowrap  ' .
        $padding;

    if($rounded) {
        $baseClasses .= ' rounded-lg';
    }

    // Tipos de botones
    $buttonTypes = [
        'primary' =>
            'bg-primary-500 text-white hover:bg-primary-600 dark:bg-primary-300 dark:text-white dark:hover:bg-primary-400',
        'secondary' =>
            'border text-gray-600 hover:bg-gray-100 border-gray-300 dark:border-gray-700 dark:text-white dark:bg-gray-900 dark:hover:bg-gray-800',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 dark:bg-red-500 dark:text-white dark:hover:bg-red-600',
        'warning' =>
            'bg-yellow-400 text-white hover:bg-yellow-500 dark:bg-yellow-500 dark:text-white dark:hover:bg-yellow-600',
        'info' => 'bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-500 dark:text-white dark:hover:bg-blue-600',
        'success' =>
            'bg-green-500 text-white hover:bg-green-600 dark:bg-green-500 dark:text-white dark:hover:bg-green-600',
        'default' =>
            'border text-gray-600 hover:bg-gray-100 border-gray-300 dark:border-gray-800 dark:text-white dark:hover:bg-gray-900',
    ];

    // Clases finales para el botón
    $classes = $buttonTypes[$typeButton ?? 'default'] . ' ' . $baseClasses . ' ' . $class;

    // Estado de carga: cuando está cargando, añadimos opacidad
    $loadingClasses = $loading ? 'opacity-75 cursor-not-allowed' : '';
    $classes .= ' ' . $loadingClasses;
@endphp

@if ($type === 'a')
    <a href="{{ $attributes->get('href') }}" {{ $attributes->except('href') }} class="{{ $classes }}">
        @if ($loading)
            <!-- Spinner para estado de carga -->
            <x-icon icon="spinner" class="{{ $sizes[$size]['icon'] }} animate-spin text-white" />
        @else
            @if ($iconAlign === 'left' && !$onlyIcon)
                <x-icon :icon="$icon" class="{{ $sizes[$size]['icon'] }} text-current" />
            @endif
            @if (!$onlyIcon)
                <span class="{{ $sizes[$size]['text'] }}">{{ $text }}</span>
            @endif
            @if ($iconAlign === 'right' && !$onlyIcon)
                <x-icon :icon="$icon" class="{{ $sizes[$size]['icon'] }} text-current" />
            @endif
            @if ($onlyIcon)
                <x-icon :icon="$icon" class="{{ $sizes[$size]['icon'] }} text-current" />
            @endif
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes }} class="{{ $classes }}"
        @if ($loading) disabled @endif>
        @if ($loading)
            <!-- Spinner para estado de carga -->
            <x-icon icon="spinner" class="{{ $sizes[$size]['icon'] }} animate-spin text-white" />
        @else
            @if ($iconAlign === 'left' && !$onlyIcon)
                <x-icon :icon="$icon" class="{{ $sizes[$size]['icon'] }} text-current" />
            @endif
            @if (!$onlyIcon)
                <span class="{{ $sizes[$size]['text'] }}">{{ $text }}</span>
            @endif
            @if ($iconAlign === 'right' && !$onlyIcon)
                <x-icon :icon="$icon" class="{{ $sizes[$size]['icon'] }} text-current" />
            @endif
            @if ($onlyIcon)
                <x-icon :icon="$icon" class="{{ $sizes[$size]['icon'] }} text-current" />
            @endif
        @endif
    </button>
@endif
