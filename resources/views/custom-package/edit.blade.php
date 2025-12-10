@extends('layouts.app')

@section('content')
    <!-- Content Row -->
    @if ($errors->any())
        <div class="alert alert-danger col-lg-8" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <a href="{{ route('custom-package.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Paket Kustom</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('custom-package.update', $package->id) }}" method="POST" id="packageForm">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_paket">Nama Paket</label>
                            <input type="text" class="form-control" id="nama_paket" name="nama_paket" value="{{ old('nama_paket', $package->nama_paket) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="harga_total">Harga Total</label>
                            <input type="number" class="form-control" id="harga_total" name="harga_total" value="{{ old('harga_total', $package->harga_total) }}" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $package->deskripsi) }}</textarea>
                </div>

                <hr>
                <h5>Perangkat</h5>
                <div id="devicesContainer">
                    @foreach ($package->playstations as $index => $playstation)
                        <div class="device-item row mb-2">
                            <div class="col-md-5">
                                <select class="form-control device-select" name="playstations[{{ $index }}][id]" required>
                                    <option value="">Pilih Jenis PlayStation</option>
                                    @foreach ($playstations as $availablePlaystation)
                                        <option value="{{ $availablePlaystation->id }}" {{ $availablePlaystation->id == $playstation->id ? 'selected' : '' }}>
                                            {{ $availablePlaystation->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="number" class="form-control" name="playstations[{{ $index }}][lama_main]" placeholder="Lama Main (menit)" min="1" value="{{ $playstation->pivot->lama_main }}" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-device" @if ($loop->first) style="display: none;" @endif>Hapus</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary btn-sm mb-3" id="addDevice">Tambah Perangkat</button>

                <hr>
                <h5>F&B</h5>
                <div id="fnbsContainer">
                    @if ($package->fnbs->count() > 0)
                        @foreach ($package->fnbs as $index => $fnb)
                            <div class="fnb-item row mb-2">
                                <div class="col-md-5">
                                    <select class="form-control fnb-select" name="fnbs[{{ $index }}][id]">
                                        <option value="">Pilih F&B</option>
                                        @foreach ($fnbs as $availableFnb)
                                            @if($availableFnb->stok == -1 || $availableFnb->stok >= 1)
                                                <option value="{{ $availableFnb->id }}" {{ $availableFnb->id == $fnb->id ? 'selected' : '' }}>
                                                    {{ $availableFnb->nama }}
                                                    @if($availableFnb->stok == -1)
                                                        <span class="badge badge-success">Unlimited</span>
                                                    @else
                                                        (Stok: {{ $availableFnb->stok }})
                                                    @endif
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" class="form-control" name="fnbs[{{ $index }}][quantity]" placeholder="Quantity" min="1" value="{{ $fnb->pivot->quantity }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-fnb" @if ($loop->first) style="display: none;" @endif>Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="fnb-item row mb-2">
                            <div class="col-md-5">
                                <select class="form-control fnb-select" name="fnbs[0][id]">
                                    <option value="">Pilih F&B</option>
                                    @foreach ($fnbs as $availableFnb)
                                        @if($availableFnb->stok == -1 || $availableFnb->stok >= 1)
                                            <option value="{{ $availableFnb->id }}">
                                                {{ $availableFnb->nama }}
                                                @if($availableFnb->stok == -1)
                                                    <span class="badge badge-success">Unlimited</span>
                                                @else
                                                    (Stok: {{ $availableFnb->stok }})
                                                @endif
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="number" class="form-control" name="fnbs[0][quantity]" placeholder="Quantity" min="1" value="1">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-fnb" style="display: none;">Hapus</button>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-secondary btn-sm mb-3" id="addFnb">Tambah F&B</button>

                <hr>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Paket</button>
                    <a href="{{ route('custom-package.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let deviceIndex = {{ $package->playstations->count() }};
        let fnbIndex = {{ $package->fnbs->count() > 0 ? $package->fnbs->count() : 1 }};

        document.getElementById('addDevice').addEventListener('click', function() {
            const container = document.getElementById('devicesContainer');
            const deviceItem = document.createElement('div');
            deviceItem.className = 'device-item row mb-2';
            deviceItem.innerHTML = `
                <div class="col-md-5">
                    <select class="form-control device-select" name="playstations[${deviceIndex}][id]" required>
                        <option value="">Pilih Perangkat</option>
                        @foreach ($playstations as $playstation)
                            <option value="{{ $playstation->id }}">{{ $playstation->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" name="playstations[${deviceIndex}][lama_main]" placeholder="Lama Main (menit)" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-device">Hapus</button>
                </div>
            `;
            container.appendChild(deviceItem);
            deviceIndex++;
            updateRemoveButtons();
        });

        document.getElementById('addFnb').addEventListener('click', function() {
            const container = document.getElementById('fnbsContainer');
            const fnbItem = document.createElement('div');
            fnbItem.className = 'fnb-item row mb-2';
            fnbItem.innerHTML = `
                <div class="col-md-5">
                    <select class="form-control fnb-select" name="fnbs[${fnbIndex}][id]">
                        <option value="">Pilih F&B</option>
                        @foreach ($fnbs as $fnb)
                            <option value="{{ $fnb->id }}">{{ $fnb->nama }} (Stok: {{ $fnb->stok }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" name="fnbs[${fnbIndex}][quantity]" placeholder="Quantity" min="1" value="1">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-fnb">Hapus</button>
                </div>
            `;
            container.appendChild(fnbItem);
            fnbIndex++;
            updateRemoveButtons();
        });

        function updateRemoveButtons() {
            const deviceItems = document.querySelectorAll('.device-item');
            const fnbItems = document.querySelectorAll('.fnb-item');
            
            deviceItems.forEach((item, index) => {
                const removeBtn = item.querySelector('.remove-device');
                removeBtn.style.display = deviceItems.length > 1 ? 'block' : 'none';
            });
            
            fnbItems.forEach((item, index) => {
                const removeBtn = item.querySelector('.remove-fnb');
                removeBtn.style.display = fnbItems.length > 1 ? 'block' : 'none';
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-device')) {
                e.target.closest('.device-item').remove();
                updateRemoveButtons();
            }
            if (e.target.classList.contains('remove-fnb')) {
                e.target.closest('.fnb-item').remove();
                updateRemoveButtons();
            }
        });

        // Prevent duplicate selections
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('device-select')) {
                const selectedValues = Array.from(document.querySelectorAll('.device-select')).map(select => select.value);
                document.querySelectorAll('.device-select').forEach(select => {
                    Array.from(select.options).forEach(option => {
                        if (option.value !== '' && selectedValues.includes(option.value) && option.value !== select.value) {
                            option.disabled = true;
                        } else {
                            option.disabled = false;
                        }
                    });
                });
            }
        });
    </script>
@endsection
