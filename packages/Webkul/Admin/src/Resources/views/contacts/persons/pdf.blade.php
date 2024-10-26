<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    lang="{{ $locale = app()->getLocale() }}"
    dir="{{ in_array($locale, ['fa', 'ar']) ? 'rtl' : 'ltr' }}"
>
    <head>
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css">
            * {
                font-family: DejaVu Sans;
            }

            body {
                font-size: 14px;
                color: #091341;
            }

            .header {
                font-size: 40px;
                color: #000DBB;
                text-align: center;
                padding: 30px 0;
                border-bottom: 1px solid #E9EFFC;
            }

            .section {
                margin: 20px 0;
                padding: 0 20px;
            }

            .section-title {
                font-size: 16px;
                color: #000DBB;
                margin-bottom: 10px;
            }

            .detail-row {
                margin: 5px 0;
            }

            .detail-label {
                font-weight: bold;
                display: inline-block;
                width: 150px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 10px 0;
            }

            th, td {
                padding: 8px;
                border-bottom: 1px solid #E9EFFC;
                text-align: left;
            }

            th {
                background-color: #E9EFFC;
                color: #000DBB;
            }
        </style>
    </head>

    <body>
        <div class="header">
            @lang('admin::app.contacts.persons.contact-information')
        </div>

        {!! view_render_event('admin.contacts.persons.pdf.header.before', ['person' => $person]) !!}

        <div class="section">
            <div class="section-title">
                @lang('admin::app.contacts.persons.personal-information')
            </div>

            <div class="detail-row">
                <span class="detail-label">@lang('admin::app.contacts.persons.name'):</span>
                <span>{{ $person->name }}</span>
            </div>

            @if ($person->contact_numbers)
                <div class="detail-row">
                    <span class="detail-label">@lang('admin::app.contacts.persons.contact-numbers'):</span>
                    @foreach ($person->contact_numbers as $number)
                        <div>{{ $number['value'] ?? '' }}</div>
                    @endforeach
                </div>
            @endif

            @if ($person->created_at)
                <div class="detail-row">
                    <span class="detail-label">@lang('admin::app.contacts.persons.created-date'):</span>
                    <span>{{ $person->created_at->format('d M Y') }}</span>
                </div>
            @endif
        </div>

        {!! view_render_event('admin.contacts.persons.pdf.header.after', ['person' => $person]) !!}

        @if ($person->tags)
            <div class="section">
                <div class="section-title">
                    @lang('admin::app.contacts.persons.tags')
                </div>

                @foreach ($person->tags as $tag)
                    <span>{{ $tag->name }}, </span>
                @endforeach
            </div>
        @endif

        {!! view_render_event('admin.contacts.persons.pdf.content.after', ['person' => $person]) !!}
    </body>
</html>
