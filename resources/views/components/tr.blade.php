@props(['section' => 'head', 'last' => false, 'class' => ''])
<tr {{ $attributes->merge(['class' => $last ? $class : $class . ' border-b dark:border-gray-800 border-gray-300']) }}>
    {{ $slot }}
</tr>
