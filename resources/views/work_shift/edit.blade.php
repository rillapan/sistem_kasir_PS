@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Jam Kerja</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('work_shifts.update', $workShift->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_shift">Nama Shift</label>
                            <input type="text" class="form-control @error('nama_shift') is-invalid @enderror" 
                                   id="nama_shift" name="nama_shift" value="{{ old('nama_shift', $workShift->nama_shift) }}" required
                                   placeholder="Contoh: Pagi, Siang, Malam">
                            @error('nama_shift')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jam_mulai">Jam Mulai</label>
                            <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" 
                                   id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', $workShift->jam_mulai) }}" required>
                            @error('jam_mulai')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jam_selesai">Jam Selesai</label>
                            <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" 
                                   id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', $workShift->jam_selesai) }}" required>
                            @error('jam_selesai')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                              id="keterangan" name="keterangan" rows="3" 
                              placeholder="Contoh: Shift pagi untuk hari kerja normal">{{ old('keterangan', $workShift->keterangan) }}</textarea>
                    @error('keterangan')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Catatan:</strong> Untuk shift yang melewati tengah malam (seperti 22:00 - 06:00), 
                    jam selesai bisa lebih kecil dari jam mulai.
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> 
                    @if($workShift->users->count() > 0)
                        Terdapat {{ $workShift->users->count() }} kasir yang menggunakan shift ini. 
                        Perubahan akan memengaruhi jam kerja kasir tersebut.
                    @else
                        Shift ini tidak digunakan oleh kasir manapun.
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('work_shifts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
