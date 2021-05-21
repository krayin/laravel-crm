@if ($value)

    <a href="{{ route('admin.settings.attributes.download', ['path' => $value]) }}" target="_blank">
        <img src="{{ Storage::url($value) }}" class="image"/>
    </a>

@else

    {{ __('admin::app.common.not-available') }}

@endif