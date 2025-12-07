@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>
    <div class="col-md-12 col-lg-6">
        <form method="POST" action="{{ route('expense.update', $expense->id) }}">
            @method('put')
            @csrf
            <div class="mb-3">
                <label for="expense_category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                @if($categories->count() > 0)
                    <select class="form-control @error('expense_category_id') is-invalid @enderror" id="expense_category_id" name="expense_category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->nama }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <select class="form-control" disabled>
                        <option value="">Belum ada kategori</option>
                    </select>
                    <small class="form-text text-muted">
                        <a href="{{ route('expense-category.create') }}">Buat kategori pengeluaran terlebih dahulu</a>
                    </small>
                @endif
                @error('expense_category_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                    name="deskripsi" value="{{ old('deskripsi', $expense->deskripsi) }}" required>
                @error('deskripsi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah"
                    name="jumlah" value="{{ old('jumlah', $expense->jumlah) }}" min="0" step="0.01" required>
                @error('jumlah')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                    name="tanggal" value="{{ old('tanggal', $expense->tanggal->format('Y-m-d')) }}" required>
                @error('tanggal')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                <select class="form-control @error('metode_pembayaran') is-invalid @enderror" id="metode_pembayaran" name="metode_pembayaran">
                    <option value="">Pilih Metode Pembayaran</option>
                    <option value="Tunai" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="E-Wallet" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="Transfer Bank" {{ old('metode_pembayaran', $expense->metode_pembayaran) == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
                @error('metode_pembayaran')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan"
                    name="catatan" rows="3">{{ old('catatan', $expense->catatan) }}</textarea>
                @error('catatan')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <button class="btn btn-primary btn-sm" type="submit">Update</button>
                <a href="{{ route('expense.index') }}" class="btn btn-secondary btn-sm">Batal</a>
            </div>
        </form>
    </div>
@endsection

