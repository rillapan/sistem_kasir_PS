@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ $title }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tambah Harga per Jam</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hourly-prices.hourly-prices.store', $playstation->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="playstation_nama" class="form-label">PlayStation</label>
                            <input type="text" class="form-control" value="{{ $playstation->nama }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="hour" class="form-label">Durasi (Jam)</label>
                            <input type="number" class="form-control @error('hour') is-invalid @enderror" 
                                   id="hour" name="hour" value="{{ old('hour') }}" min="1" required>
                            @error('hour')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price') }}" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('hourly-prices.hourly-prices.index', $playstation->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection