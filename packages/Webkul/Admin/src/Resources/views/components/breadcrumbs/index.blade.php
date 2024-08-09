@props([
    'name'   => '',
    'entity' => null,
    'route'  => null,
])

<div class="flex justify-start max-lg:hidden">
    <div class="flex items-center gap-x-3.5">        
        @if($route)
            {{ Breadcrumbs::view('admin::partials.breadcrumbs', $name, $route, $entity) }}
        @else
            {{ Breadcrumbs::view('admin::partials.breadcrumbs', $name, $entity) }}
        @endif
    </div>
</div>
