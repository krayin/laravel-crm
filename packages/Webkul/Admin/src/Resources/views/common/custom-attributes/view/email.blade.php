@if (is_array($value))

    @foreach ($value as $item)
        <span class="multi-value">
            {{ $item['value'] }}

            <span>{{ ' (' . $item['label'] . ')'}}</span>
        </span>
    @endforeach

@else

    {{ __('admin::app.common.not-available') }}
    
@endif