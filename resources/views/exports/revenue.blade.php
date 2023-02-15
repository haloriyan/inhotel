<table>
    <thead>
        <tr>
            <th>No. Invoice</th>
            <th>Nama Pemesan</th>
            <th>Total</th>
            <th>Items</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $payment)
            <tr>
                <td>{{ $payment->invoice_number }}</td>
                <td>{{ $payment->visitor->name }}</td>
                <td>{{ $payment->grand_total }}</td>
                <td>
                    @foreach ($payment->items as $item)
                        <div>
                            - {{ $item->product->name }} 
                            ({{ $item->product->price }} x {{$item->product->quantity}})
                        </div>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>