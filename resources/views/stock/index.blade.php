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
    </div>

    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Manajemen Stok FnB
            </h6>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Stok Saat Ini</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fnbs as $fnb)
                        <tr>
                            <td>{{ $fnb->nama }}</td>
                            <td>
                                @if($fnb->stok == -1)
                                    <span class="badge badge-success">Unlimited</span>
                                @else
                                    {{ $fnb->stok }}
                                @endif
                            </td>
                            <td>
                                @if($fnb->stok == -1)
                                    <span class="badge badge-secondary mr-2">Stok Unlimited</span>
                                    <small class="text-muted">Tidak dapat ditambah/dikurangi</small>
                                    <a href="{{ route('stock.history', $fnb->id) }}" class="btn btn-info btn-sm ml-2">Riwayat</a>
                                @else
                                    <a href="{{ route('stock.add', $fnb->id) }}" class="btn btn-success btn-sm">Tambah Stok</a>
                                    <a href="{{ route('stock.reduce', $fnb->id) }}" class="btn btn-warning btn-sm">Kurangi Stok</a>
                                    <a href="{{ route('stock.history', $fnb->id) }}" class="btn btn-info btn-sm">Riwayat</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
