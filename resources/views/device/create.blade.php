@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    Form Tambah Perangkat
                </h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <form method="POST" action="{{ route('device.store') }}">
                    @csrf
                    <input type="hidden" name="status" value="tersedia">
                    <div class="mb-3">
                        <label for="nama" class="form-label ">Nama Perangkat</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                            name="nama" required autofocus value="{{ old('nama') }}">
                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label>Jenis Playstation (bisa memilih lebih dari satu)</label>
                        @if($plays->count() > 0)
                            <div class="playstation-selection">
                                @foreach ($plays as $play)
                                    <div class="form-check">
                                        <input class="form-check-input @error('playstation_ids.*') is-invalid @enderror" type="checkbox" name="playstation_ids[]" value="{{ $play->id }}" id="playstation_{{ $play->id }}">
                                        <label class="form-check-label" for="playstation_{{ $play->id }}">
                                            {{ $play->nama }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Tidak ada data PlayStation yang tersedia. Silakan tambahkan PlayStation terlebih dahulu.
                            </div>
                        @endif
                        @error('playstation_ids')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Pilih satu atau lebih jenis PlayStation untuk perangkat ini</small>
                    </div>
                    <button class="btn btn-primary btn-sm" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
