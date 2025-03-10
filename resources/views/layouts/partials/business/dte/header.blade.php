@props(['title' => ''])

<div class="flex w-full sm:items-center justify-between px-4 sm:flex-row flex-col-reverse gap-y-4">
    <h1 class="text-xl font-bold text-primary-500 dark:text-primary-300 sm:text-2xl md:text-3xl lg:text-4xl">
        {{ $title }}
    </h1>
    <a href="{{ Route('business.customers.index') }}"
        class="flex items-center gap-1 ml-auto text-xs  sm:text-sm text-gray-600 dark:text-gray-400">
        <x-icon icon="arrow-back" class="sm:size-5 size-4" />
        Regresar
    </a>
</div>
