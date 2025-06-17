@props([
    'options' => [],
    'id',
    'name',
    'label' => '',
    'options',
    'selected',
    'text',
    'required' => false,
    'value' => '',
    'class' => '',
    'selected' => '',
    'text' => '',
    'readonly' => false,
    'search' => true,
])

<div class="w-full flex flex-col gap-1">
    @if (!empty($label))
        <label for="{{ $id }}"
            class="{{ $required ? "after:content-['*'] after:ml-0.5 after:text-red-500" : '' }} mb-1 block text-sm font-medium text-gray-500 dark:text-gray-300">
            {{ ucfirst($label) }}
        </label>
    @endif
    <input type="hidden" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
        {{ $attributes }} @if ($required) required @endif select />
    <div class="relative">
        <div class="selected @error($name) is-invalid @enderror @if ($readonly) pointer-events-none @endif {{ $class }} flex w-full items-center justify-between rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-800 dark:bg-gray-950 dark:text-white"
            id="selected-{{ $id }}">
            <span class="itemSelected truncate" id="{{ $id }}_selected">
                {{ $selected && isset($options[$selected]) ? $options[$selected] : ($text ?: 'Seleccionar') }}
            </span>
            @if (!$readonly)
                <x-icon icon="arrow-down" class="arrow-down-select h-5 w-5 text-gray-500 dark:text-white" />
            @endif
        </div>
        <ul
            class="selectOptions {{ count($options) > 6 ? 'h-64 overflow-auto' : '' }} @if (!$search) pt-1 @endif motion-preset-fade absolute z-10 mb-8 mt-2 hidden w-full rounded-lg border border-gray-300 bg-white px-1 pb-1 shadow-lg dark:border-gray-800 dark:bg-gray-950">
            @if ($search)
                <li class="sticky top-0 z-10 mb-2 w-full bg-white dark:bg-gray-950">
                    <input type="text" placeholder="Buscar..."
                        class="search-input mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-white" />
                </li>
            @endif
            @if (count($options) === 0)
                <li
                    class="itemOption pointer-events-none rounded-lg px-4 py-2 text-sm text-gray-900 dark:text-white dark:hover:bg-gray-900">
                    No hay opciones disponibles
                </li>
            @else
                @foreach ($options as $value => $item)
                    <li class="itemOption cursor-default truncate rounded-lg px-4 py-2 text-sm text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-900"
                        title="{{ $item }}" data-value="{{ $value }}"
                        data-input="#{{ $id }}">
                        {{ $item }}
                    </li>
                @endforeach
            @endif
        </ul>
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
