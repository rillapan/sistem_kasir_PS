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
                    <label for="price_group_id">Kelompok Harga (Opsional)</label>
                    <select class="form-control" id="price_group_id" name="price_group_id" onchange="filterFnBsByPriceGroupEdit()">
                        <option value="">Pilih Kelompok Harga</option>
                        @foreach ($priceGroups as $priceGroup)
                            <option value="{{ $priceGroup->id }}" {{ old('price_group_id', $package->price_group_id) == $priceGroup->id ? 'selected' : '' }}>
                                {{ $priceGroup->nama }} - Rp {{ number_format($priceGroup->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Jika dipilih, FnB akan difilter berdasarkan kelompok harga ini</small>
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
                <h5>F&B Items (Berdasarkan Kelompok Harga)</h5>
                <div id="fnbsContainer">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span id="fnb-selection-info-edit">
                            @if($package->price_group_id)
                                Memuat F&B berdasarkan kelompok harga yang dipilih...
                            @else
                                Pilih kelompok harga di atas untuk menampilkan F&B yang tersedia
                            @endif
                        </span>
                    </div>

                    <!-- Display F&B items based on selected price group -->
                    <div id="fnb-items-display-edit" class="row">
                        @if($package->price_group_id)
                            <!-- Load based on price group -->
                            @php
                                $fnbsInPriceGroup = \App\Models\Fnb::where('price_group_id', $package->price_group_id)->get();
                            @endphp
                            @foreach($fnbsInPriceGroup as $index => $fnb)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $fnb->nama }}</h6>
                                            <p class="card-text">
                                                Harga: Rp {{ number_format($fnb->harga_jual, 0, ',', '.') }}<br>
                                                Stok: {{ $fnb->stok == -1 ? '<span class="badge badge-success">Unlimited</span>' : $fnb->stok }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Hidden inputs to preserve the data -->
                                    <input type="hidden" name="fnbs[{{ $index }}][id]" value="{{ $fnb->id }}">
                                    <input type="hidden" name="fnbs[{{ $index }}][quantity]" value="1">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

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

        function updateRemoveButtons() {
            const deviceItems = document.querySelectorAll('.device-item');

            deviceItems.forEach((item, index) => {
                const removeBtn = item.querySelector('.remove-device');
                removeBtn.style.display = deviceItems.length > 1 ? 'block' : 'none';
            });
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-device')) {
                e.target.closest('.device-item').remove();
                updateRemoveButtons();
            }
        });


        // FnB filtering by price group for edit form
        function filterFnBsByPriceGroupEdit() {
            const priceGroupId = document.getElementById('price_group_id').value;
            const fnbSelectionInfo = document.getElementById('fnb-selection-info-edit');
            const fnbItemsDisplay = document.getElementById('fnb-items-display-edit');

            if (!priceGroupId) {
                // If no price group selected, show info message
                fnbSelectionInfo.textContent = 'Pilih kelompok harga di atas untuk menampilkan F&B yang tersedia';
                fnbItemsDisplay.innerHTML = '';
                return;
            }

            // Fetch FnBs filtered by price group
            axios.get('/api/get-fnbs-by-price-group', {
                params: {
                    price_group_id: priceGroupId
                }
            })
            .then(function(response) {
                const filteredFnbs = response.data.fnbs;
                console.log('Filtered FnBs:', filteredFnbs);

                // Update info text
                fnbSelectionInfo.innerHTML = `Menampilkan ${filteredFnbs.length} item F&B dari kelompok harga yang dipilih`;

                // Display the F&B items
                displayAvailableFnbsEdit(filteredFnbs);
            })
            .catch(function(error) {
                console.error('Error filtering FnBs:', error);
                fnbSelectionInfo.innerHTML = 'Terjadi kesalahan saat memuat F&B dari kelompok harga';
                fnbItemsDisplay.innerHTML = '';
            });
        }

        function displayAvailableFnbsEdit(fnbs) {
            const container = document.getElementById('fnb-items-display-edit');

            if (fnbs.length === 0) {
                container.innerHTML = '<div class="col-12"><p class="text-muted">Tidak ada F&B dalam kelompok harga ini</p></div>';
                // Add hidden input to track that no FnBs are selected
                document.getElementById('fnbsContainer').insertAdjacentHTML('beforeend',
                    '<input type="hidden" name="fnbs" value="[]">'
                );
                return;
            }

            let html = '';
            let hiddenInputsHtml = '';

            fnbs.forEach((fnb, index) => {
                const cardHtml = `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title">${fnb.nama}</h6>
                                <p class="card-text">
                                    Harga: Rp ${new Intl.NumberFormat('id-ID').format(fnb.harga_jual)}<br>
                                    Stok: ${fnb.stok == -1 ? '<span class="badge badge-success">Unlimited</span>' : fnb.stok}
                                </p>
                            </div>
                        </div>
                    </div>
                `;

                // Add hidden input to include this F&B item in the form submission with default quantity 1
                hiddenInputsHtml += `
                    <input type="hidden" name="fnbs[${index}][id]" value="${fnb.id}">
                    <input type="hidden" name="fnbs[${index}][quantity]" value="1">
                `;

                html += cardHtml;
            });

            container.innerHTML = html;

            // Add the hidden inputs after the container (clear previous ones first)
            const existingHiddenInputs = document.querySelectorAll('#fnbsContainer input[type="hidden"][name^="fnbs"]');
            existingHiddenInputs.forEach(input => input.remove());

            document.getElementById('fnbsContainer').insertAdjacentHTML('beforeend', hiddenInputsHtml);
        }

        // Initialize F&B filtering if a price group is already selected
        document.addEventListener('DOMContentLoaded', function() {
            const priceGroupId = document.getElementById('price_group_id').value;
            if (priceGroupId) {
                filterFnBsByPriceGroupEdit();
            }
        });
    </script>
@endsection
