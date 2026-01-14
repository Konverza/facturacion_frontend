@props([
    'title' => '',
    'icon' => null,
    'collapsible' => true,
    'collapsed' => true,
    'headerClass' => '',
    'bodyClass' => '',
    'id' => 'card-' . uniqid()
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700']) }}>
    <!-- Header del card -->
    @if($collapsible)
        <button 
            type="button"
            data-card-toggle="{{ $id }}"
            class="w-full flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors duration-200 {{ $headerClass }}"
        >
            <div class="flex items-center gap-3">
                @if($icon)
                    <x-icon :icon="$icon" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                @endif
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    {{ $title }}
                </h2>
            </div>
            <svg 
                data-card-caret="{{ $id }}"
                class="w-5 h-5 text-gray-600 dark:text-gray-400 transition-transform duration-200 {{ $collapsed ? '' : 'rotate-180' }}"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    @else
        <div class="flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-600 {{ $headerClass }}">
            <div class="flex items-center gap-3">
                @if($icon)
                    <x-icon :icon="$icon" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                @endif
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    {{ $title }}
                </h2>
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    <!-- Contenido del card -->
    <div 
        data-card-content="{{ $id }}"
        class="{{ $collapsible && $collapsed ? 'hidden' : '' }} {{ $collapsible ? 'border-t border-gray-200 dark:border-gray-700' : '' }}"
    >
        <div class="p-6 {{ $bodyClass }}">
            {{ $slot }}
        </div>
    </div>
</div>

@if($collapsible)
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('[data-card-toggle="{{ $id }}"]');
            const content = document.querySelector('[data-card-content="{{ $id }}"]');
            const caret = document.querySelector('[data-card-caret="{{ $id }}"]');
            
            if (toggleBtn && content && caret) {
                toggleBtn.addEventListener('click', function() {
                    content.classList.toggle('hidden');
                    caret.classList.toggle('rotate-180');
                    
                    // Emitir evento personalizado cuando se expande/colapsa
                    const event = new CustomEvent('card-toggled', {
                        detail: {
                            id: '{{ $id }}',
                            expanded: !content.classList.contains('hidden')
                        }
                    });
                    document.dispatchEvent(event);
                });
            }
        });
    </script>
    @endpush
@endif
