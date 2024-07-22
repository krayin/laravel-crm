@props([
    'name'  => '',
    'entity' => null,
])

<div class="flex justify-start max-lg:hidden">
    <div class="flex items-center gap-x-3.5">        
        {{ Breadcrumbs::view('admin::partials.breadcrumbs', $name, $entity) }}
    </div>
</div>
