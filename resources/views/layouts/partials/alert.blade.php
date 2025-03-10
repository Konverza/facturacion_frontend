@php
    $alertType = '';
    $icon = '';
    $bgColor = '';
    $textColor = '';
    $title = '';
    $message = '';

    if (Session::has('success') && Session::has('success_message')) {
        $alertType = 'success';
        $icon = 'circle-check'; // Icono para éxito
        $bgColor = 'bg-green-500';
        $textColor = 'text-green-500 dark:text-green-400';
        $title = Session::get('success');
        $message = Session::get('success_message');
    } elseif (Session::has('error') && Session::has('error_message')) {
        $alertType = 'error';
        $icon = 'alert-circle'; // Icono para error
        $bgColor = 'bg-red-500';
        $textColor = 'text-red-500 dark:text-red-400';
        $title = Session::get('error');
        $message = Session::get('error_message');
    } elseif (Session::has('warning') && Session::has('warning_message')) {
        $alertType = 'warning';
        $icon = 'info-circle'; // Icono para advertencia
        $bgColor = 'bg-yellow-500';
        $textColor = 'text-yellow-500 dark:text-yellow-400';
        $title = Session::get('warning');
        $message = Session::get('warning_message');
    }
@endphp

@if ($alertType)
    <div class="flex items-center justify-center">
        <div
            class="alert fixed top-4 z-[100] flex w-max animate-fade-left overflow-hidden rounded-lg bg-white shadow-md animate-duration-300 dark:bg-gray-900 sm:right-4">
            <div class="{{ $bgColor }} flex items-center justify-center px-4">
                <x-icon icon="{{ $icon }}" class="size-6 min-w-6 max-w-6 text-white" />
            </div>
            <div class="flex w-full items-center justify-between gap-4 px-4 py-2">
                <div>
                    <span class="{{ $textColor }} text-sm font-semibold sm:text-base">
                        {{ $title }}
                    </span>
                    <p class="line-clamp-2 w-60 sm:w-max text-wrap text-xs text-gray-600 dark:text-gray-200 sm:text-sm">
                        {{ $message }}
                    </p>
                </div>
                <div>
                    <x-button type="button" icon="x" typeButton="secondary" onlyIcon
                        class="alert-close {{ $textColor }}" size="small" />
                </div>
            </div>
        </div>
    </div>
@endif
