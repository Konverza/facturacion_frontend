@props(['thead', 'tbody', 'datatable' => true, 'class' => ''])
<div
    class="{{ $class }} w-full overflow-x-auto rounded-lg border border-gray-300 bg-white dark:border-gray-800 dark:bg-gray-950 xl:overflow-x-hidden">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-sm text-gray-500 dark:text-gray-400']) }}>
        <thead
            class="{{ $datatable ? 'border-y' : '' }} border-gray-300 text-xs uppercase text-gray-700 dark:border-gray-800 dark:text-gray-300 lg:text-wrap text-nowrap">
            {{ $thead ?? '' }}
        </thead>
        <tbody {{ $tbody->attributes->merge([]) }} class="">
            {{ $tbody ?? '' }}
        </tbody>
    </table>
</div>
