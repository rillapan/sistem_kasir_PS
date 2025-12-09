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

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pengeluaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pengeluaran Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($monthlyTotal, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Data Pengeluaran
            </h6>
            <div class="dropdown no-arrow">
                <a href="{{ route('expense.create') }}" class="btn btn-primary btn-sm">Tambah Pengeluaran</a>
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('expense.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                            value="{{ $filters['start_date'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                            value="{{ $filters['end_date'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="expense_category_id">Kategori</label>
                        <select class="form-control" id="expense_category_id" name="expense_category_id">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ ($filters['expense_category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-info btn-sm">Filter</button>
                            <a href="{{ route('expense.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Metode Pembayaran</th>
                            <th scope="col">Dibuat Oleh</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expenses as $expense)
                            <tr>
                                <td>{{ $expense->tanggal->format('d/m/Y') }}</td>
                                <td><span class="badge badge-info">{{ $expense->expenseCategory->nama ?? '-' }}</span></td>
                                <td>{{ $expense->deskripsi }}</td>
                                <td class="text-danger font-weight-bold">Rp {{ number_format($expense->jumlah, 0, ',', '.') }}</td>
                                <td>{{ $expense->metode_pembayaran ?? '-' }}</td>
                                <td>{{ $expense->user->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('expense.show', $expense->id) }}" class="btn btn-info btn-sm">Detail</a>
                                    <a href="{{ route('expense.edit', $expense->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('expense.destroy', $expense->id) }}" method="post" class="d-inline">
                                        @method('delete')
                                        @csrf
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin menghapus pengeluaran ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data pengeluaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $expenses->links() }}
        </div>
    </div>
@endsection

