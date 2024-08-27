<tr {{ $attributes->merge(['scope' => 'row', 'class' => 'border-b border-gray-200 last:border-b-0 dark:border-gray-800']) }}>
    {{ $slot }}
</tr>