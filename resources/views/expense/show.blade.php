@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pengeluaran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Kategori</th>
                            <td><span class="badge badge-info">{{ $expense->expenseCategory->nama ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $expense->deskripsi }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td class="text-danger font-weight-bold">Rp {{ number_format($expense->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $expense->tanggal->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Metode Pembayaran</th>
                            <td>{{ $expense->metode_pembayaran ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Oleh</th>
                            <td>{{ $expense->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $expense->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        @if($expense->catatan)
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $expense->catatan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('expense.edit', $expense->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('expense.destroy', $expense->id) }}" method="post" class="d-inline">
                    @method('delete')
                    @csrf
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin menghapus pengeluaran ini?')">Hapus</button>
                </form>
                <a href="{{ route('expense.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            </div>
        </div>
    </div>
@endsection

