@if ($value)

    <a href="{{ route('admin.settings.attributes.download', ['path' => $value]) }}" target="_blank">
        <i class="icon download-icon"></i>
    </a>

@else

    {{ __('admin::app.common.not-available') }}

@endif