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
                Jenis Playstation
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                </a>
                <a href="{{ route('playstation.create') }}" class="btn btn-primary btn-sm">Tambah Data</a>
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Harga Dasar</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plays as $play)
                        <tr>
                            <th scope="row">
                                {{ ($plays->currentpage() - 1) * $plays->perpage() + $loop->index + 1 }}</th>
                            <td>{{ $play->nama }}</td>
                            <td>{{ 'Rp ' . number_format($play->harga, 0, ',', '.') }}</td>
                            <td class="d-flex">
                                <a href="/playstation/{{ $play->id }}/edit" class="btn btn-sm btn-info mr-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('device.byPlaystation', $play->id) }}" class="btn btn-sm btn-primary mr-1" title="View Devices">
                                    <i class="fas fa-tv"></i>
                                </a>
                                <a href="{{ route('hourly-prices.hourly-prices.index', $play->id) }}" class="btn btn-sm btn-success mr-1" title="Manage Hourly Prices">
                                    <i class="fas fa-clock"></i>
                                </a>
                                <form action="/playstation/{{ $play->id }}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $plays->links() }}
        </div>
    </div>
    </div>
@endsection
