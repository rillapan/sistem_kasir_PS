<table>
    <thead>
        <tr>
            <th>Nama Produk</th>
            <th>Jumlah Terjual</th>
            <th>Total Modal</th>
            <th>Total Penjualan</th>
            <th>Laba/Rugi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($salesData as $data)
            <tr>
                <td>{{ $data->nama }}</td>
                <td>{{ $data->jumlah_terjual }}</td>
                <td>{{ $data->total_modal }}</td>
                <td>{{ $data->total_penjualan }}</td>
                <td>{{ $data->laba_rugi }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
