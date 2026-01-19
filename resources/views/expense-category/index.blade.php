@extends('layouts.app')

@section('content')
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
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Data Kategori Pengeluaran
            </h6>
            <div class="dropdown no-arrow">
                <a href="{{ route('expense-category.create') }}" class="btn btn-primary btn-sm">Tambah Kategori</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Nama Kategori</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Jumlah Pengeluaran</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td><span class="badge badge-info">{{ $category->nama }}</span></td>
                                <td>{{ $category->deskripsi ? \Str::limit($category->deskripsi, 50) : '-' }}</td>
                                <td>{{ $category->expenses_count }} pengeluaran</td>
                                <td>
                                    <a href="{{ route('expense-category.edit', $category->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('expense-category.destroy', $category->id) }}" method="post" class="d-inline">
                                        @method('delete')
                                        @csrf
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin menghapus kategori ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data kategori</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $categories->links() }}
        </div>
    </div>
@endsection
