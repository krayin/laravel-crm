@unless ($breadcrumbs->isEmpty())
    <nav aria-label="You are here:" role="navigation">
        <ul class="breadcrumbs">
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($loop->last)
                    <li class="current"><span class="show-for-sr">Current:</span> {{ $breadcrumb->title }}</li>
                @elseif ($breadcrumb->url)
                    <li><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                @else
                    <li class="disabled">{{ $breadcrumb->title }}</li>
                @endif

            @endforeach
        </ul>
    </nav>
@endunless
