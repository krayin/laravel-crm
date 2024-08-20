
@php
    $bgColors = ['bg-orange-100', 'bg-red-100', 'bg-green-100', 'bg-blue-100', 'bg-purple-100'];
    $textColors = ['text-orange-800', 'text-red-800', 'text-green-800', 'text-blue-800', 'text-purple-800'];
@endphp
@foreach ($bgColors as $bgColor)
    <div class="{{ $bgColor }}"></div>
@endforeach

@foreach ($textColors as $textColor)
    <div class="{{ $textColor }}"></div>
@endforeach