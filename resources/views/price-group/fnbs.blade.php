@extends('layouts.app')

@section('content')
    <!-- Content Row -->
    @if (session()->has('success'))
        <div class="alert alert-success col-lg-8" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('gagal'))
        <div class="alert alert-danger col-lg-8" role="alert">
            {{ session('gagal') }}
        </div>
    @endif

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <a href="{{ route('price-group.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Kelompok Harga
        </a>
    </div>

    <div class="card shadow mb-4">
        <!-- Card Header -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Data Barang FnB - Kelompok Harga: {{ $priceGroup->nama }} (Rp {{ number_format($priceGroup->harga, 0, ',', '.') }})
            </h6>
            <div class="dropdown no-arrow">
                <a href="{{ route('fnb.create') }}" class="btn btn-primary btn-sm">Tambah Barang</a>
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            @if($fnbs->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Harga Jual</th>
                            <th scope="col">Stok</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fnbs as $fnb)
                            <tr>
                                <th scope="row">
                                    {{ ($fnbs->currentpage() - 1) * $fnbs->perpage() + $loop->index + 1 }}
                                </th>
                                <td>{{ $fnb->nama }}</td>
                                <td>Rp {{ number_format($fnb->harga_jual, 0, ',', '.') }}</td>
                                <td>
                                    @if($fnb->stok == -1)
                                        <span class="badge badge-success">Unlimited</span>
                                    @else
                                        {{ $fnb->stok }} {{ $fnb->satuan ?? '' }}
                                    @endif
                                </td>
                                <td>{{ $fnb->deskripsi ? Str::limit($fnb->deskripsi, 50) : '-' }}</td>
                                <td>
                                    <a href="{{ route('fnb.edit', $fnb->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('fnb.destroy', $fnb->id) }}" method="post" class="d-inline">
                                        @method('delete')
                                        @csrf
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin menghapus barang ini?')">Hapus</button>
                                    </form>
                                    <a href="{{ route('stock.history', $fnb->id) }}" class="btn btn-info btn-sm">Riwayat Stok</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $fnbs->links() }}
            @else
                <div class="alert alert-info" role="alert">
                    Belum ada barang FnB yang menggunakan kelompok harga ini.
                </div>
            @endif
        </div>
    </div>
@endsection

