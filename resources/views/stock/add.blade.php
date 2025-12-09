@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>
    <div class="col-md-12 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    Tambah Stok: {{ $fnb->nama }}
                </h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <form method="POST" action="{{ route('stock.add', $fnb->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="qty" class="form-label">Jumlah Tambah</label>
                        <input type="number" class="form-control @error('qty') is-invalid @enderror" id="qty"
                            name="qty" min="1" required>
                        @error('qty')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Catatan</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note"
                            name="note"></textarea>
                        @error('note')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary btn-sm" type="submit">Tambah Stok</button>
                        <a href="{{ route('stock.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
