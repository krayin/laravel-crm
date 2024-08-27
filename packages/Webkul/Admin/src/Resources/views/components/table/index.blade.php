<table {{ $attributes->merge(['class' => 'table-fixed w-full min-w-[800px] text-left text-sm border border-gray-200 dark:border-gray-800']) }}>
    {{ $slot }}
</table>