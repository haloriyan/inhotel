<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>No. Whatsapp</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $visitor)
            <tr>
                <td>{{ $visitor->name }}</td>
                <td>{{ $visitor->email }}</td>
                <td>{{ $visitor->phone }}</td>
            </tr>
        @endforeach
    </tbody>
</table>