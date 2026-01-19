@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>
    <div class="col-md-12 col-lg-6">
        <form method="POST" action="{{ route('expense-category.update', $category->id) }}">
            @method('put')
            @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                    name="nama" value="{{ old('nama', $category->nama) }}" required>
                @error('nama')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                    name="deskripsi" rows="3">{{ old('deskripsi', $category->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                <button class="btn btn-primary btn-sm" type="submit">Update</button>
                <a href="{{ route('expense-category.index') }}" class="btn btn-secondary btn-sm">Batal</a>
            </div>
        </form>
    </div>
@endsection

