@props(['last' => false])
<td
    {{ $attributes->merge(['class' => $last ? 'px-4 py-2 text-nowrap' : 'px-4 py-2 border-e border-gray-300 dark:border-gray-800']) }}>
    {{ $slot }}
</td>
