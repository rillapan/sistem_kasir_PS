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
            <h6 class="m-0 font-weight-bold text-primary">Buat Paket Kustom Baru</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('custom-package.store') }}" method="POST" id="packageForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_paket">Nama Paket</label>
                            <input type="text" class="form-control" id="nama_paket" name="nama_paket" value="{{ old('nama_paket') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="harga_total">Harga Total</label>
                            <input type="number" class="form-control" id="harga_total" name="harga_total" value="{{ old('harga_total') }}" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="price_group_ids">Kelompok Harga Fnb</label>
                    <div class="price-groups-container">
                        <div class="price-group-item row mb-2">
                            <div class="col-md-10">
                                <select class="form-control price-group-select" name="price_group_ids[0]" onchange="filterFnBsByPriceGroups()">
                                    <option value="">Pilih Kelompok Harga</option>
                                    @foreach ($priceGroups as $priceGroup)
                                        <option value="{{ $priceGroup->id }}" {{ old('price_group_ids.0') == $priceGroup->id ? 'selected' : '' }}>
                                            {{ $priceGroup->nama }} - Rp {{ number_format($priceGroup->harga, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-price-group" style="display: none;">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" id="addPriceGroup">Tambah Kelompok Harga</button>
                    <small class="text-muted">Pilih satu atau lebih kelompok harga untuk menampilkan F&B yang tersedia</small>
                </div>

                <hr>
                <h5>Jenis PlayStation</h5>
                <div id="devicesContainer">
                    <div class="device-item row mb-2">
                        <div class="col-md-5">
                            <select class="form-control device-select" name="playstations[0][id]" required>
                                <option value="">Pilih Jenis PlayStation</option>
                                @foreach ($playstations as $playstation)
                                    <option value="{{ $playstation->id }}">{{ $playstation->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="number" class="form-control" name="playstations[0][lama_main]" placeholder="Lama Main (jam)" min="0.5" step="0.5" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm remove-device" style="display: none;">Hapus</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm mb-3" id="addDevice">Tambah Jenis PlayStation</button>

                <hr>
                <h5>F&B Items (Berdasarkan Kelompok Harga)</h5>
                <div id="fnbsContainer">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span id="fnb-selection-info">Pilih kelompok harga di atas untuk menampilkan F&B yang tersedia</span>
                    </div>

                    <!-- Display F&B items based on selected price group -->
                    <div id="fnb-items-display" class="row">
                        <!-- F&B items will be loaded here dynamically -->
                    </div>
                </div>

                <hr>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Simpan Paket</button>
                    <a href="{{ route('custom-package.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let deviceIndex = 1;
        let fnbIndex = 1;
        let priceGroupIndex = 1;

        // Price group management
        document.getElementById('addPriceGroup').addEventListener('click', function() {
            const container = document.querySelector('.price-groups-container');
            const priceGroupItem = document.createElement('div');
            priceGroupItem.className = 'price-group-item row mb-2';
            priceGroupItem.innerHTML = `
                <div class="col-md-10">
                    <select class="form-control price-group-select" name="price_group_ids[${priceGroupIndex}]" onchange="filterFnBsByPriceGroups()">
                        <option value="">Pilih Kelompok Harga</option>
                        @foreach ($priceGroups as $priceGroup)
                            <option value="{{ $priceGroup->id }}">{{ $priceGroup->nama }} - Rp {{ number_format($priceGroup->harga, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-price-group">Hapus</button>
                </div>
            `;
            container.appendChild(priceGroupItem);
            priceGroupIndex++;
            updatePriceGroupRemoveButtons();
            updatePriceGroupSelectOptions();
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-price-group')) {
                e.target.closest('.price-group-item').remove();
                updatePriceGroupRemoveButtons();
                updatePriceGroupSelectOptions();
                filterFnBsByPriceGroups();
            }
        });

        function updatePriceGroupRemoveButtons() {
            const priceGroupItems = document.querySelectorAll('.price-group-item');
            priceGroupItems.forEach((item, index) => {
                const removeButton = item.querySelector('.remove-price-group');
                if (removeButton) {
                    removeButton.style.display = priceGroupItems.length > 1 ? 'block' : 'none';
                }
            });
        }

        function updatePriceGroupSelectOptions() {
            const selectedValues = Array.from(document.querySelectorAll('.price-group-select'))
                .map(select => select.value)
                .filter(value => value !== '');
            
            document.querySelectorAll('.price-group-select').forEach(select => {
                Array.from(select.options).forEach(option => {
                    if (option.value !== '' && selectedValues.includes(option.value) && option.value !== select.value) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('price-group-select')) {
                updatePriceGroupSelectOptions();
            }
        });

        document.getElementById('addDevice').addEventListener('click', function() {
            const container = document.getElementById('devicesContainer');
            const deviceItem = document.createElement('div');
            deviceItem.className = 'device-item row mb-2';
            deviceItem.innerHTML = `
                <div class="col-md-5">
                    <select class="form-control device-select" name="playstations[${deviceIndex}][id]" required>
                        <option value="">Pilih Jenis PlayStation</option>
                        @foreach ($playstations as $playstation)
                            <option value="{{ $playstation->id }}">{{ $playstation->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" name="playstations[${deviceIndex}][lama_main]" placeholder="Lama Main (jam)" min="0.5" step="0.5" required>
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
            alert('Fitur tambah F&B individual telah dinonaktifkan. Gunakan pemilihan berdasarkan kelompok harga di atas.');
        });

        // Store all available FnBs globally so we can reference them in filtering
        const allFnbs = @json($fnbs);

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-device')) {
                e.target.closest('.device-item').remove();
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

        // FnB filtering by price groups - display F&B items from selected price groups
        function filterFnBsByPriceGroups() {
            const priceGroupSelects = document.querySelectorAll('.price-group-select');
            const selectedPriceGroupIds = Array.from(priceGroupSelects)
                .map(select => select.value)
                .filter(value => value !== '');
            
            const fnbSelectionInfo = document.getElementById('fnb-selection-info');
            const fnbItemsDisplay = document.getElementById('fnb-items-display');

            if (selectedPriceGroupIds.length === 0) {
                // If no price group selected, show info message
                fnbSelectionInfo.textContent = 'Pilih kelompok harga di atas untuk menampilkan F&B yang tersedia';
                fnbItemsDisplay.innerHTML = '';
                return;
            }

            // Fetch FnBs filtered by multiple price groups
            axios.get('/api/get-fnbs-by-price-groups', {
                params: {
                    price_group_ids: selectedPriceGroupIds
                }
            })
            .then(function(response) {
                const filteredFnbs = response.data.fnbs;
                console.log('Filtered FnBs:', filteredFnbs);

                // Update info text
                fnbSelectionInfo.innerHTML = `Menampilkan ${filteredFnbs.length} item F&B dari ${selectedPriceGroupIds.length} kelompok harga yang dipilih`;

                // Display the F&B items
                displayAvailableFnbs(filteredFnbs);
            })
            .catch(function(error) {
                console.error('Error filtering FnBs:', error);
                fnbSelectionInfo.innerHTML = 'Terjadi kesalahan saat memuat F&B dari kelompok harga';
                fnbItemsDisplay.innerHTML = '';
            });
        }

        function displayAvailableFnbs(fnbs) {
            const container = document.getElementById('fnb-items-display');

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

            // Add the hidden inputs after the container
            document.getElementById('fnbsContainer').insertAdjacentHTML('beforeend', hiddenInputsHtml);
        }
        
        function showFilterInfo(count) {
            const priceGroupSelect = document.getElementById('price_group_id');
            const selectedOption = priceGroupSelect.options[priceGroupSelect.selectedIndex];
            const priceGroupName = selectedOption ? selectedOption.textContent.split(' - ')[0] : '';

            // Update the existing info div
            const filterInfoDiv = document.getElementById('fnb-filter-info');
            const filterInfoText = document.getElementById('filter-info-text');

            if (count > 0) {
                filterInfoText.innerHTML = `Menampilkan ${count} item F&B dari kelompok harga "${priceGroupName}"`;
                filterInfoDiv.style.display = 'block';
            } else {
                filterInfoText.innerHTML = `Tidak ada item F&B dalam kelompok harga "${priceGroupName}"`;
                filterInfoDiv.style.display = 'block';
            }
        }

        // Initialize F&B filtering if price groups are already selected (e.g., after form validation error)
        document.addEventListener('DOMContentLoaded', function() {
            const priceGroupSelects = document.querySelectorAll('.price-group-select');
            const hasSelectedGroups = Array.from(priceGroupSelects).some(select => select.value !== '');
            if (hasSelectedGroups) {
                filterFnBsByPriceGroups();
            }
        });

        // Also trigger when the page loads after form submission if needed
        window.onload = function() {
            const priceGroupSelects = document.querySelectorAll('.price-group-select');
            const hasSelectedGroups = Array.from(priceGroupSelects).some(select => select.value !== '');
            if (hasSelectedGroups) {
                filterFnBsByPriceGroups();
            }
        };
    </script>
@endsection
