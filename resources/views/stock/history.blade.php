@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Riwayat Mutasi Stok: {{ $fnb->nama }}
            </h6>
            <a href="{{ route('stock.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Tipe</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mutations as $mutation)
                        <tr>
                            <td>{{ $mutation->date }}</td>
                            <td>{{ ucfirst($mutation->type) }}</td>
                            <td>{{ $mutation->qty }} {{ $fnb->satuan }}</td>
                            <td>{{ $mutation->note }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
