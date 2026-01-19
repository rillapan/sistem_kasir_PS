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
                Data Paket Kustom
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                </a>
                <a href="{{ route('custom-package.create') }}" class="btn btn-primary btn-sm">Tambah Paket</a>
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Nama Paket</th>
                        <th scope="col">Harga Total</th>
                        <th scope="col">Jenis PlayStation</th>
                        <th scope="col">Kelompok Harga & F&B</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packages as $package)
                        <tr>
                            <td>{{ $package->nama_paket }}</td>
                            <td>Rp {{ number_format($package->harga_total, 0, ',', '.') }}</td>
                            <td>
                                @foreach ($package->playstations as $playstation)
                                    <span class="badge badge-info">{{ $playstation->nama }} - {{ number_format($playstation->pivot->lama_main / 60, 1) }} jam</span><br>
                                @endforeach
                            </td>
                            <td>
                                @if(!empty($package->price_group_names))
                                    <strong>Kelompok:</strong><br>
                                    @foreach($package->price_group_names as $groupName)
                                        <span class="badge badge-info">{{ $groupName }}</span><br>
                                    @endforeach
                                    <br>
                                    @if($package->fnbs->count() > 0)
                                        <strong>F&B:</strong><br>
                                        @foreach ($package->fnbs as $fnb)
                                            <span class="badge badge-success">{{ $fnb->nama }}</span><br>
                                        @endforeach
                                    @else
                                        <em>Tidak ada F&B</em>
                                    @endif
                                @elseif($package->priceGroup)
                                    <strong>Kelompok: {{ $package->priceGroup->nama }}</strong><br>
                                    @if($package->fnbs->count() > 0)
                                        @foreach ($package->fnbs as $fnb)
                                            <span class="badge badge-success">{{ $fnb->nama }}</span><br>
                                        @endforeach
                                    @else
                                        <em>Tidak ada F&B</em>
                                    @endif
                                @else
                                    @if($package->fnbs->count() > 0)
                                        <strong>F&B Tersedia:</strong><br>
                                        @foreach ($package->fnbs as $fnb)
                                            <span class="badge badge-success">{{ $fnb->nama }}</span><br>
                                        @endforeach
                                    @else
                                        <em>Tidak ada F&B</em>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($package->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('custom-package.show', $package->id) }}" class="btn btn-info btn-sm">Detail</a>
                                <a href="{{ route('custom-package.edit', $package->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('custom-package.destroy', $package->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus paket ini?')">Hapus</button>
                                </form>
                                <form action="{{ route('custom-package.toggle-status', $package->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-secondary btn-sm">
                                        @if ($package->is_active)
                                            Nonaktifkan
                                        @else
                                            Aktifkan
                                        @endif
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
