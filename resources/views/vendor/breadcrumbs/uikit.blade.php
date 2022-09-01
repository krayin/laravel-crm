@unless ($breadcrumbs->isEmpty())
    <ul class="uk-breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($loop->last)
                <li><span>{{ $breadcrumb->title }}</span></li>
            @elseif ($breadcrumb->url)
                <li><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
            @else
                <li class="uk-disabled"><a>{{ $breadcrumb->title }}</a></li>
            @endif
        @endforeach
    </ul>
@endunless
