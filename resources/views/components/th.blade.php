@props(['last' => false, 'first' => false])
<th
    {{ $attributes->merge(['class' => $last ? 'px-4 py-3' : ' border-e border-gray-300 px-4 py-3 dark:border-gray-800']) }}>
    {{ $slot }}
</th>
