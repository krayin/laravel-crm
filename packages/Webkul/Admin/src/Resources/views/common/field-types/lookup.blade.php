@php
    $lookUpEntityData = app('Webkul\Attribute\Repositories\AttributeRepository')
        ->getLookUpEntity($attribute->code, old($attribute->code) ?: $value);
@endphp

<lookup-component :attribute='@json($attribute)' :validations="'{{$validations}}'" :lookup-data='@json($lookUpEntityData)'></lookup-component>