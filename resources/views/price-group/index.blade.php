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
                Data Kelompok Harga
            </h6>
            <div class="dropdown no-arrow">
                <a href="{{ route('price-group.create') }}" class="btn btn-primary btn-sm">Tambah Kelompok Harga</a>
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Jumlah Barang</th>
                        <th scope="col">Deskripsi</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($priceGroups as $priceGroup)
                        <tr>
                            <th scope="row">
                                {{ ($priceGroups->currentpage() - 1) * $priceGroups->perpage() + $loop->index + 1 }}
                            </th>
                            <td>{{ $priceGroup->nama }}</td>
                            <td>Rp {{ number_format($priceGroup->harga, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-info">{{ $priceGroup->fnbs_count }} Barang</span>
                            </td>
                            <td>{{ $priceGroup->deskripsi ? Str::limit($priceGroup->deskripsi, 50) : '-' }}</td>
                            <td>
                                <a href="{{ route('price-group.fnbs', $priceGroup->id) }}" class="btn btn-info btn-sm" title="Lihat Daftar Barang FnB">
                                    <i class="fas fa-list"></i> Lihat Barang
                                </a>
                                <a href="{{ route('price-group.edit', $priceGroup->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('price-group.destroy', $priceGroup->id) }}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin menghapus kelompok harga ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $priceGroups->links() }}
        </div>
    </div>
@endsection

