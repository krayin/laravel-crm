@if ($value)

    {{ $value['address'] }}<br>
    {{ $value['postcode'] . '  ' . $value['city'] }}<br>
    {{ core()->state_name($value['state']) }}<br>
    {{ core()->country_name($value['country']) }}<br>

@else

    {{ __('admin::app.common.not-available') }}

@endif