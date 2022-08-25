<table>
    <thead>
        <tr>
            @foreach ($columns as $column)
                <th>{{ $column }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach ($records as $record)
            <tr>
                @foreach($record as $column => $value)
                    <td> {{ strip_tags($value) }} </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
