@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Paket Kustom</h1>
        <a href="{{ route('custom-package.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $package->nama_paket }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informasi Paket</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama Paket:</strong></td>
                            <td>{{ $package->nama_paket }}</td>
                        </tr>
                        <tr>
                            <td><strong>Harga Total:</strong></td>
                            <td>Rp {{ number_format($package->harga_total, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if ($package->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @if ($package->deskripsi)
                        <tr>
                            <td><strong>Deskripsi:</strong></td>
                            <td>{{ $package->deskripsi }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Aksi</h5>
                    <div class="btn-group-vertical" role="group">
                        <a href="{{ route('custom-package.edit', $package->id) }}" class="btn btn-warning">Edit Paket</a>
                        <form action="{{ route('custom-package.toggle-status', $package->id) }}" method="POST" class="mt-2">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-secondary">
                                @if ($package->is_active)
                                    Nonaktifkan
                                @else
                                    Aktifkan
                                @endif
                            </button>
                        </form>
                        <form action="{{ route('custom-package.destroy', $package->id) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus paket ini?')">Hapus Paket</button>
                        </form>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h5>Perangkat</h5>
                    @if ($package->playstations->count() > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Perangkat</th>
                                    <th>Lama Main</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($package->playstations as $playstation)
                                    <tr>
                                        <td>{{ $playstation->nama }}</td>
                                        <td>{{ number_format($playstation->pivot->lama_main / 60, 1) }} jam</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Tidak ada perangkat dalam paket ini.</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5>Kelompok Harga</h5>
                    @if(!empty($package->price_group_names))
                        @foreach($package->price_group_names as $groupName)
                            <span class="badge badge-info">{{ $groupName }}</span>
                        @endforeach
                    @elseif($package->priceGroup)
                        <span class="badge badge-info">{{ $package->priceGroup->nama }}</span>
                    @else
                        <p>Tidak ada kelompok harga.</p>
                    @endif
                    
                    <hr>
                    
                    <h5>F&B</h5>
                    @if ($package->fnbs->count() > 0)
                        <div class="d-flex flex-wrap">
                            @foreach ($package->fnbs as $fnb)
                                <span class="badge badge-light border mr-2 mb-2 p-2">
                                    <i class="fas fa-utensils text-primary mr-1"></i> {{ $fnb->nama }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p>Tidak ada F&B dalam paket ini.</p>
                    @endif
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-12">
                    <h5>Ringkasan Paket</h5>
                    <div class="alert alert-info">
                        <h6>{{ $package->nama_paket }}</h6>
                        @if ($package->playstations && $package->playstations->count() > 0)
                            @foreach ($package->playstations as $playstation)
                                <p class="mb-1">
                                    <i class="fas fa-gamepad"></i> {{ $playstation->nama }} ({{ $playstation->pivot->lama_main }} menit)
                                </p>
                            @endforeach
                        @endif
                        @if ($package->fnbs && $package->fnbs->count() > 0)
                            @foreach ($package->fnbs as $fnb)
                                <p class="mb-1">
                                    <i class="fas fa-utensils"></i> {{ $fnb->nama }}
                                </p>
                            @endforeach
                        @endif
                        <hr class="my-2">
                        <p class="mb-0"><strong>Total Harga: Rp {{ number_format($package->harga_total, 0, ',', '.') }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
