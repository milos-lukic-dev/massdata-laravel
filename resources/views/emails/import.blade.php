<h3>Failed File: <b>{{ $import->file_name }}</b>, import type: <b>{{ $import->import_type }}</b></h3>
<table>
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Invalid Row</th>
            <th>Invalid Column</th>
            <th>Invalid Value</th>
            <th>Error Message</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($errorRows))
            @foreach($errorRows as $error)
                <tr>
                    <td>{{ $error['file_row'] }}</td>
                    <td>{{ $error['file_column'] }}</td>
                    <td>{{ $error['value'] }}</td>
                    <td>{{ $error['message'] }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

