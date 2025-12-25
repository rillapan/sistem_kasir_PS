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
    <div class="row mb-5">
        <div class="col">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        My Account
                    </h6>
                    {{-- <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        </a>
                        <a href="/profile/{{ auth()->user()->id }}/edit" class="btn btn-primary btn-sm">Edit Profile</a>
                    </div> --}}
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <form method="POST" action="/profile/{{ auth()->user()->id }}">
                        @method('put')
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">Name</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                    value="{{ old('name', auth()->user()->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" value="{{ old('email', auth()->user()->email) }}">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                class="form-control" id="password_confirmation"
                                autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <!-- Card Body -->
                <div class="card-body">
                    <img style="display:block; margin-right:auto;margin-left:auto;" width="200px"
                        class="img-profile rounded-circle" src="img/undraw_profile.svg" />
                </div>
                <h4 class="text-center">{{ auth()->user()->name }}</h4>
                <p class="text-center">{{ auth()->user()->status }}</p>
                
                @if(auth()->user()->isKasir() && auth()->user()->workShift)
                    <div class="card-body border-top">
                        <h6 class="text-center text-primary font-weight-bold mb-3">
                            <i class="fas fa-clock"></i> Informasi Jam Kerja
                        </h6>
                        <div class="text-center">
                            <span class="badge badge-info badge-pill mb-2" style="font-size: 1rem;">
                                {{ auth()->user()->workShift->nama_shift }}
                            </span>
                            <div class="text-muted small">
                                <i class="fas fa-clock"></i>
                                {{ auth()->user()->workShift->jam_mulai }} - {{ auth()->user()->workShift->jam_selesai }}
                            </div>
                            @if(auth()->user()->workShift->keterangan)
                                <div class="text-muted small mt-2">
                                    <i class="fas fa-info-circle"></i>
                                    {{ auth()->user()->workShift->keterangan }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
