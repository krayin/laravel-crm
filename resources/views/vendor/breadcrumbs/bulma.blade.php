@unless ($breadcrumbs->isEmpty())
    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            @foreach ($breadcrumbs as $breadcrumb)

                @if ($loop->last)
                    @if ($breadcrumb->url)
                        <li class="is-active"><a href="{{ $breadcrumb->url }}" aria-current="page">{{ $breadcrumb->title }}</a></li>
                    @else
                        <li class="is-active"><a aria-current="page">{{ $breadcrumb->title }}</a></li>
                    @endif
                @else
                    @if ($breadcrumb->url)
                        <li><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                    @else
                        <li class="is-active"><a>{{ $breadcrumb->title }}</a></li>
                    @endif
                @endif

            @endforeach
        </ul>
    </nav>
@endunless
