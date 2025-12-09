<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan FnB</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan FnB</h1>
    <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
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
                    <td>Rp {{ number_format($data->total_modal, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->total_penjualan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($data->laba_rugi, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
