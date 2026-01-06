@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>
    <div class="col-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    Form Transaksi
                </h6>
            </div>
            <!-- Card Body -->
            <div class="card-body p-3">
                <form method="POST" action="{{ route('transaction.store') }}" onsubmit="return validateTransactionForm()">
                    @csrf
                    <input type="hidden" name="transaksi" value="transaksi">
                    <input type="hidden" name="status_device" value="Digunakan">

                    <!-- Row 0: Jenis Transaksi -->
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label class="form-label small">Jenis Transaksi</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe_transaksi" id="prepaid" value="prepaid" checked onchange="toggleFields()">
                                <label class="form-check-label" for="prepaid">
                                    Paket
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe_transaksi" id="postpaid" value="postpaid" onchange="toggleFields()">
                                <label class="form-check-label" for="postpaid">
                                    Lost Time
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe_transaksi" id="custom_package" value="custom_package" onchange="toggleFields()">
                                <label class="form-check-label" for="custom_package">
                                    Custom Paket
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Debug output for devices -->
                

                    <!-- Row 1: Identitas Pelanggan -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="nama" class="form-label small">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="no_telepon" class="form-label small">No. Telepon (Opsional)</label>
                            <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" id="no_telepon"
                                name="no_telepon" value="{{ old('no_telepon') }}" placeholder="Contoh: 081234567890">
                            @error('no_telepon')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                     <!-- Row 2.5: Custom Package -->
                    <div class="row mb-2" id="custom-package-row" style="display: none;">
                        <div class="col-md-12">
                            <label for="custom_package_id" class="form-label small">Pilih Custom Paket</label>
                            <select class="form-control" id="custom_package_id" name="custom_package_id" onchange="loadCustomPackageDetails()">
                                <option value="" selected disabled hidden>Pilih custom paket</option>
                                @foreach ($customPackages as $package)
                                    <option value="{{ $package->id }}" data-details="{{ json_encode($package) }}">{{ $package->nama_paket }} - Rp {{ number_format($package->harga_total, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <!-- Row 2: Device -->
                    <div class="row mb-2" id="device-row">
                        <div class="col-md-12" id="device">
                            <label for="device_id" class="form-label small">Nama Perangkat</label>
                            <select class="form-control" id="device_id" name="device_id" onchange="handleDeviceChange()" {{ $noDevices ? 'disabled' : '' }}>
                                @if($noDevices)
                                    <option value="" selected>Semua perangkat sedang digunakan</option>
                                @else
                                    <option value="dummy" selected disabled hidden>Pilih nama perangkat</option>
                                    @foreach ($devices as $device)
                                        @php
                                            $displayText = $device->nama . ' - ' . ($device->playstation_names ?: 'Tidak Diketahui');
                                            $dataPlaystationId = $device->playstation_id;
                                            $dataPlaystations = json_encode($device->playstations->toArray());
                                        @endphp
                                        @if (old('device_id') == $device->id)
                                            <option value="{{ $device->id }}" data-playstation-id="{{ $dataPlaystationId }}" data-playstations="{{ $dataPlaystations }}" selected>{{ $displayText }}</option>
                                        @else
                                            <option value="{{ $device->id }}" data-playstation-id="{{ $dataPlaystationId }}" data-playstations="{{ $dataPlaystations }}">{{ $displayText }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted" id="device-help-text">Pilih perangkat yang tersedia</small>
                        </div>
                    </div>

                    <!-- Row 2.1: PlayStation Type (Conditional) -->
                    <div class="row mb-2" id="playstation-type-row" style="display: none;">
                        <div class="col-md-12">
                            <label for="playstation_id" class="form-label small">Pilih Jenis Perangkat [Jenis PlayStation - Harga]</label>
                            <select class="form-control" id="playstation_id" name="playstation_id" onchange="showPrice()">
                                <option value="" selected disabled hidden>Pilih jenis perangkat</option>
                            </select>
                            <small class="text-muted">Perangkat ini memiliki beberapa jenis tarif, silakan pilih salah satu</small>
                        </div>
                    </div>

                   
                    <!-- Row 2.6: Selected Custom Package Details -->
                    <div class="row mb-2" id="custom-package-details" style="display: none;">
                        <div class="col-md-12">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="m-0 font-weight-bold">Detail Paket Terpilih</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Nama Paket:</strong> <span id="selected-package-name">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Harga Total:</strong> <span id="selected-package-price">-</span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <strong>Jenis PlayStation:</strong>
                                            <ul id="selected-package-devices" class="list-unstyled mb-0"></ul>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>F&B:</strong>
                                            <ul id="selected-package-fnb" class="list-unstyled mb-0"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Harga and Jam Mulai -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="harga" class="form-label small">Harga dasar</label>
                            <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga"
                                name="harga" required readonly value="{{ old('harga') }}">
                            <small class="text-muted">Harga dasar dari PlayStation</small>
                            @error('harga')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="jam_main" class="form-label small">Jam Main</label>
                            <div class="row g-2">
                                <div class="col-12">
                                    <select class="form-control @error('jam_main') is-invalid @enderror" id="jam_main_select" name="jam_main_select" onchange="handleJamMainChange()">
                                        <option value="">Pilih jam yang tersedia</option>
                                        <option value="custom">Custom (input manual)</option>
                                    </select>
                                </div>
                                <div class="col-12" id="custom_jam_main_container" style="display: none;">
                                    <input type="number" class="form-control @error('jam_main') is-invalid @enderror" id="jam_main"
                                        name="jam_main" value="{{ old('jam_main') }}" oninput="showTotal()" onchange="handleJamMainChange()" min="1" max="24" placeholder="Masukkan jumlah jam">
                                    <small class="text-muted">Harga mengikuti harga dasar PlayStation</small>
                                </div>
                            </div>
                            @error('jam_main')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 4: Jam Mulai and Jam Selesai -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="waktu_mulai" class="form-label small">Jam Mulai</label>
                            <input type="text" class="form-control @error('waktu_mulai') is-invalid @enderror"
                                id="waktu_mulai" name="waktu_mulai" readonly value="{{ old('waktu_mulai', $currentTime ?? '') }}">
                            @error('waktu_mulai')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="waktu_Selesai" class="form-label small">Jam Selesai</label>
                            <input type="text" class="form-control @error('waktu_Selesai') is-invalid @enderror"
                                id="waktu_Selesai" name="waktu_Selesai" readonly value="{{ old('waktu_Selesai') }}">
                            @error('waktu_Selesai')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 5: Total PS and Total FnB -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="total_ps" class="form-label small">Total PS</label>
                            <input type="number" class="form-control @error('total') is-invalid @enderror" id="total_ps"
                                name="total_ps" required readonly value="{{ old('total') }}">
                            @error('total')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="fnb_total" class="form-label small">Total FnB</label>
                            <input type="number" class="form-control" id="fnb_total" readonly value="0">
                        </div>
                    </div>

                    <!-- FnB Section - Compact Horizontal Layout -->
                    <div class="row mb-3" id="fnb-section">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-primary mb-0">
                                    <span id="fnb-section-title">FnB Items (Opsional)</span>
                                    <small id="fnb-section-subtitle" class="text-muted d-block"></small>
                                </h6>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addFnbItem(currentPackageFnbs)">+ Tambah FnB</button>
                            </div>
                            <div id="fnb-container" class="border rounded">
                                <!-- FnB items will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- Row 5: Total Keseluruhan and Submit -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="total" class="form-label small">Total Keseluruhan</label>
                            <input type="number" class="form-control @error('total') is-invalid @enderror" id="total"
                                name="total" required readonly value="{{ old('total') }}">
                            @error('total')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button id="bayarBtn" class="btn btn-success flex-fill" type="submit" name="action" value="bayar">Bayar</button>
                                <button class="btn btn-primary flex-fill" type="submit" name="action" value="simpan">Simpan Transaksi</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        const nama = document.getElementById('nama');
        const member = document.getElementById('member');
        const d = new Date();
        let fnbIndex = 0;
        let currentPackageFnbs = null; // Store filtered FnBs for current package
        let packageFnbIds = new Set(); // Store F&B IDs that are included in package price
        const fnbs = @json($fnbs);
        const devices = @json($devices);

        // if (member) member.style.display = "none";
        if (nama) nama.style.display = "block";
        console.log(d.getHours() + " : " + d.getMinutes());

        document.getElementById('waktu_mulai').value =
            `${d.getHours() < 10 ? '0' + d.getHours() : d.getHours() }:${d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes()}`;

        // Always populate price on load for auto-selected device
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== DEBUGGING DEVICE DATA ===');
            console.log('User role: @if(auth()->user()){{ auth()->user()->role }}@else guest @endif');
            console.log('Devices data from server:', devices);
            console.log('Number of devices:', devices.length);
            console.log('Environment: {{ app()->environment() === 'local' ? 'LOCAL' : 'PRODUCTION' }}');
            
            // Check each device's status and playstation relationship
            devices.forEach((device, index) => {
                console.log(`Device ${index + 1}:`, {
                    id: device.id,
                    name: device.nama,
                    status: device.status,
                    playstation_id: device.playstation_id,
                    hasPlaystation: !!device.playstation,
                    playstationName: device.playstation ? device.playstation.nama : 'No playstation data'
                });
            });
            
            // Test custom package filtering
            console.log('=== TESTING CUSTOM PACKAGE FILTERING ===');
            const customPackages = @json($customPackages);
            if (customPackages && customPackages.length > 0) {
                const testPackage = customPackages[0];
                console.log('Test package:', testPackage);
                
                if (testPackage.playstations && testPackage.playstations.length > 0) {
                    const playstationIds = testPackage.playstations.map(ps => ps.id);
                    console.log('Package playstation IDs:', playstationIds);
                    
                    const filteredDevices = devices.filter(device => {
                        if (!device || !device.playstation_id) return false;
                        if (device.status !== 'Tersedia') return false;
                        return playstationIds.includes(device.playstation_id);
                    });
                    
                    console.log('Filtered devices for test package:', filteredDevices);
                    console.log('Filtered count:', filteredDevices.length);
                } else {
                    console.log('Package has no playstations');
                }
            } else {
                console.log('No custom packages available');
            }
            
            console.log('=== END DEBUGGING ===');
            
            showPrice();
            toggleFields();
        });

        function toggleFields() {
            const isPrepaid = document.getElementById('prepaid').checked;
            const isPostpaid = document.getElementById('postpaid').checked;
            const isCustomPackage = document.getElementById('custom_package').checked;
            const deviceRow = document.getElementById('device-row');
            const customPackageRow = document.getElementById('custom-package-row');
            const jamMainRow = document.querySelector('.row.mb-2:has(#jam_main)');
            const waktuSelesaiRow = document.querySelector('.row.mb-2:has(#waktu_Selesai)');
            const totalPsRow = document.querySelector('.row.mb-2:has(#total_ps)');
            const jamMainInput = document.getElementById('jam_main');
            const bayarBtn = document.getElementById('bayarBtn');
            const hargaLabel = document.querySelector('label[for="harga"]');
            const deviceSelect = document.getElementById('device_id');
            const deviceHelpText = document.getElementById('device-help-text');

            // Show/hide device and custom package rows
            if (deviceRow) deviceRow.style.display = 'block';
            if (customPackageRow) customPackageRow.style.display = isCustomPackage ? 'block' : 'none';

            // Handle device selection based on transaction type
            if (isCustomPackage) {
                // Enable device selection for custom package
                deviceSelect.disabled = false;
                deviceSelect.value = '';
                deviceHelpText.textContent = 'Pilih paket terlebih dahulu, lalu pilih perangkat';
                // Clear form fields
                document.getElementById('jam_main').value = '';
                document.getElementById('jam_main_select').value = '';
                document.getElementById('custom_jam_main_container').style.display = 'none';
                document.getElementById('waktu_Selesai').value = '';
                document.getElementById('total_ps').value = '';
                document.getElementById('harga').value = '';
                document.getElementById('total').value = '';
                document.getElementById('custom-package-details').style.display = 'none';
                // Clear FnB container and reset F&B section
                document.getElementById('fnb-container').innerHTML = '';
                document.getElementById('fnb-section-title').textContent = 'FnB Items (Opsional)';
                document.getElementById('fnb-section-subtitle').textContent = '';
                currentPackageFnbs = null; // Reset filtered FnBs
                packageFnbIds.clear(); // Reset package F&B IDs
                updateFnbTotal();
            } else {
                // Reset to normal device selection for prepaid/postpaid
                resetDeviceSelection();
                // Clear custom package selection
                document.getElementById('custom_package_id').value = '';
                document.getElementById('custom-package-details').style.display = 'none';
                // Clear FnB container and reset F&B section
                document.getElementById('fnb-container').innerHTML = '';
                document.getElementById('fnb-section-title').textContent = 'FnB Items (Opsional)';
                document.getElementById('fnb-section-subtitle').textContent = '';
                currentPackageFnbs = null; // Reset filtered FnBs
                packageFnbIds.clear(); // Reset package F&B IDs
                updateFnbTotal();
            }

            // Update harga label
            if (hargaLabel) {
                hargaLabel.textContent = isCustomPackage ? 'Total Paket' : 'Harga per Jam';
            }

            // Show/hide Bayar button based on transaction type
            if (bayarBtn) {
                bayarBtn.style.display = (isPrepaid || isCustomPackage) ? 'block' : 'none';
                // Adjust the width of Simpan button when Bayar is hidden
                const simpanBtn = document.querySelector('button[value="simpan"]');
                if (simpanBtn) {
                    simpanBtn.style.flex = (isPrepaid || isCustomPackage) ? '1' : '0 0 100%';
                }
            }

            if (isPrepaid) {
                if (jamMainRow) jamMainRow.style.display = 'flex';
                if (waktuSelesaiRow) waktuSelesaiRow.style.display = 'flex';
                if (totalPsRow) totalPsRow.style.display = 'flex';
                jamMainInput.setAttribute('required', 'required');
                updateGrandTotal();
            } else if (isCustomPackage) {
                if (jamMainRow) jamMainRow.style.display = 'none';
                if (waktuSelesaiRow) waktuSelesaiRow.style.display = 'none';
                if (totalPsRow) totalPsRow.style.display = 'none';
                jamMainInput.removeAttribute('required');
                // For custom package, total is set from package
                updateGrandTotal();
            } else {
                // Postpaid
                if (jamMainRow) jamMainRow.style.display = 'none';
                if (waktuSelesaiRow) waktuSelesaiRow.style.display = 'none';
                if (totalPsRow) totalPsRow.style.display = 'none';
                jamMainInput.removeAttribute('required');
                document.getElementById('total').value = document.getElementById('fnb_total').value;
            }
        }

        function onChangeWrapper() {
            showInput();
            showPrice();
        }

        function showChangeMaster() {
            showTime();
            // showTotal() is now called from handleJamMainChange() when needed
        }

        function showInput() {
            const statusEl = document.getElementById('status');
            const nama = document.getElementById('nama');
            const member = document.getElementById('member');

            if (!statusEl) return;
            const status = statusEl.value;

            if (status === 'member') {
                if (member) member.style.display = "block";
                if (nama) nama.style.display = "none";
            } else {
                if (member) member.style.display = "none";
                if (nama) nama.style.display = "block";
            }
        }

        function showPrice() {
            const isCustomPackage = document.getElementById('custom_package').checked;
            if (isCustomPackage) return; // In custom package mode, price is fixed by package

            const device = document.getElementById('device_id').value;
            const playstationSelect = document.getElementById('playstation_id');
            const harga = document.getElementById('harga');
            const totalField = document.getElementById('total_ps');
            const jamMainSelect = document.getElementById('jam_main_select');
            const jamMainInput = document.getElementById('jam_main');
            const jamMain = parseFloat(jamMainInput.value);

            if (!device || device === "dummy") {
                harga.value = '';
                totalField.value = '';
                jamMainSelect.innerHTML = '<option value="">Pilih jam yang tersedia</option><option value="custom">Custom (input manual)</option>';
                return;
            }

            console.log('Device:', device);
            
            // Check if a specific playstation type is selected
            const selectedPlaystationId = playstationSelect.value;
            
            if (selectedPlaystationId) {
                // Use specific playstation pricing
                console.log('Using specific playstation ID:', selectedPlaystationId);
                
                // Get price for specific playstation
                axios.get('/api/get-harga', {
                    params: {
                        device: device,
                        playstation_id: selectedPlaystationId
                    }
                })
                .then(function(response) {
                    console.log('Playstation price response:', response.data);
                    harga.value = response.data.harga;
                    
                    // Load hourly prices for this specific playstation
                    return axios.get('/api/get-hourly-prices', {
                        params: {
                            device: device,
                            playstation_id: selectedPlaystationId
                        }
                    });
                })
                .then(function(hourlyResponse) {
                    console.log('Hourly prices for playstation:', hourlyResponse.data);
                    const prices = hourlyResponse.data.prices || {};

                    // Update jam main select with hourly prices
                    jamMainSelect.innerHTML = '<option value="">Pilih jam yang tersedia</option>';

                    Object.keys(prices).sort((a, b) => parseInt(a) - parseInt(b)).forEach(hour => {
                        const option = document.createElement('option');
                        option.value = hour;
                        option.textContent = `${hour} jam - Rp ${new Intl.NumberFormat('id-ID').format(prices[hour])}`;
                        jamMainSelect.appendChild(option);
                    });

                    jamMainSelect.innerHTML += '<option value="custom">Custom (input manual)</option>';

                    // Calculate total if jam main is selected
                    if (!isNaN(jamMain) && jamMain > 0) {
                        showTotal();
                    } else {
                        totalField.value = '';
                    }
                })
                .catch(function(error) {
                    console.log('Error getting playstation pricing:', error);
                    harga.value = '';
                    totalField.value = '';
                    jamMainSelect.innerHTML = '<option value="">Pilih jam yang tersedia</option><option value="custom">Custom (input manual)</option>';
                });
            } else {
                // Use default device pricing (backward compatibility)
                console.log('Using default device pricing');

                // Get base price first - also check if specific playstation selected
                const params = {
                    device: device
                };

                // If a specific playstation type is selected, use that for pricing
                const specificPlaystationId = playstationSelect.value;
                if (specificPlaystationId) {
                    params.playstation_id = specificPlaystationId;
                }

                axios.get('/api/get-harga', {
                        params: params
                    })
                    .then(function(response) {
                        console.log(response.data);
                        harga.value = response.data.harga;

                        // Load hourly prices using the same parameters
                        return axios.get('/api/get-hourly-prices', {
                            params: params
                        });
                    })
                    .then(function(hourlyResponse) {
                        console.log('Hourly prices:', hourlyResponse.data);
                        const prices = hourlyResponse.data.prices || {};
                        
                        // Update jam main select with hourly prices
                        jamMainSelect.innerHTML = '<option value="">Pilih jam yang tersedia</option>';
                        
                        Object.keys(prices).sort((a, b) => parseInt(a) - parseInt(b)).forEach(hour => {
                            const option = document.createElement('option');
                            option.value = hour;
                            option.textContent = `${hour} jam - Rp ${new Intl.NumberFormat('id-ID').format(prices[hour])}`;
                            jamMainSelect.appendChild(option);
                        });
                        
                        jamMainSelect.innerHTML += '<option value="custom">Custom (input manual)</option>';
                        
                        // Calculate total if jam main is selected
                        if (!isNaN(jamMain) && jamMain > 0) {
                            showTotal(); // Use showTotal() to get correct pricing
                        } else {
                            totalField.value = '';
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                        harga.value = '';
                        totalField.value = '';
                        jamMainSelect.innerHTML = '<option value="">Pilih jam yang tersedia</option><option value="custom">Custom (input manual)</option>';
                    });
            }
        }

        function handleJamMainChange() {
            const isCustomPackage = document.getElementById('custom_package').checked;
            if (isCustomPackage) return; // In custom package mode, price is fixed

            const jamMainSelect = document.getElementById('jam_main_select');
            const jamMainInput = document.getElementById('jam_main');
            const customContainer = document.getElementById('custom_jam_main_container');
            const harga = document.getElementById('harga');
            const totalField = document.getElementById('total_ps');
            
            if (jamMainSelect.value === 'custom') {
                // Show custom input
                customContainer.style.display = 'block';
                jamMainInput.setAttribute('required', 'required');
                
                // Use base price for custom - call showTotal() to handle calculation
                showTotal();
            } else if (jamMainSelect.value) {
                // Hide custom input and use selected hourly price
                customContainer.style.display = 'none';
                jamMainInput.removeAttribute('required');
                jamMainInput.value = jamMainSelect.value;
                
                // Get hourly price for selected hour
                const device = document.getElementById('device_id').value;
                const playstationSelect = document.getElementById('playstation_id'); // Get the PlayStation selection
                if (device && device !== "dummy") {
                    const params = {
                        device: device
                    };

                    // If a specific playstation type is selected, include it in the request
                    if (playstationSelect && playstationSelect.value) {
                        params.playstation_id = playstationSelect.value;
                    }

                    axios.get('/api/get-hourly-prices', {
                        params: params
                    })
                    .then(function(response) {
                        const prices = response.data.prices || {};
                        const selectedHour = jamMainSelect.value;
                        
                        if (prices[selectedHour]) {
                            // Use hourly price - this is the TOTAL price for the selected duration
                            totalField.value = parseFloat(prices[selectedHour]);
                            updateGrandTotal();
                        } else {
                            // Fallback to base price calculation
                            const total = parseFloat(harga.value) * parseFloat(selectedHour);
                            totalField.value = total;
                            updateGrandTotal();
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                        // Fallback to base price
                        const total = parseFloat(harga.value) * parseFloat(jamMainSelect.value);
                        totalField.value = total;
                        updateGrandTotal();
                    });
                }
            } else {
                // Reset
                customContainer.style.display = 'none';
                jamMainInput.removeAttribute('required');
                jamMainInput.value = '';
                totalField.value = '';
                updateGrandTotal();
            }
            
            showChangeMaster();
        }

        function showTotal() {
            const isCustomPackage = document.getElementById('custom_package').checked;
            if (isCustomPackage) return; // In custom package mode, price is fixed by package

            const harga = parseFloat(document.getElementById('harga').value);
            const jamMainSelect = document.getElementById('jam_main_select');
            const jamMainInput = document.getElementById('jam_main');
            const totalField = document.getElementById('total_ps');
            let jamMain = 0;
            
            if (jamMainSelect.value && jamMainSelect.value !== 'custom') {
                jamMain = parseFloat(jamMainSelect.value);
            } else if (jamMainInput.value) {
                jamMain = parseFloat(jamMainInput.value);
            }
            
            // If hourly price is selected, get the specific hourly price
            if (jamMainSelect.value && jamMainSelect.value !== 'custom') {
                const device = document.getElementById('device_id').value;
                const playstationSelect = document.getElementById('playstation_id'); // Get the PlayStation selection
                if (device && device !== "dummy") {
                    const params = {
                        device: device
                    };

                    // If a specific playstation type is selected, include it in the request
                    if (playstationSelect && playstationSelect.value) {
                        params.playstation_id = playstationSelect.value;
                    }

                    axios.get('/api/get-hourly-prices', {
                        params: params
                    })
                    .then(function(response) {
                        const prices = response.data.prices || {};
                        const selectedHour = jamMainSelect.value;
                        
                        if (prices[selectedHour]) {
                            // Use hourly price - this is the TOTAL price for the selected duration
                            totalField.value = parseFloat(prices[selectedHour]);
                            updateGrandTotal();
                        } else {
                            // Fallback to base price calculation
                            const total = harga * jamMain;
                            totalField.value = total;
                            updateGrandTotal();
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                        // Fallback to base price calculation
                        const total = harga * jamMain;
                        totalField.value = total;
                        updateGrandTotal();
                    });
                }
            } else {
                // Use base price for custom or no selection
                const total = harga * jamMain;
                totalField.value = total;
                updateGrandTotal();
            }
        }

        function loadFnbsFromPriceGroups(priceGroupIds, packageFnbs = null) {
            // Fetch FnBs from multiple price groups
            axios.get('/api/get-fnbs-by-price-groups', {
                params: {
                    price_group_ids: priceGroupIds
                }
            })
            .then(function(response) {
                const priceGroupFnbs = response.data.fnbs || [];
                
                // Store filtered FnBs globally for this package
                currentPackageFnbs = priceGroupFnbs;
                
                // Add one empty F&B item with filtered options (manual addition by user)
                addFnbItem(priceGroupFnbs);

                // Update FNB total
                updateFnbTotal();
            })
            .catch(function(error) {
                console.log('Error loading FnBs from price groups:', error);
                // Fallback to all FnBs
                currentPackageFnbs = null;
                addFnbItem();
            });
        }

        function loadFnbsFromPriceGroup(priceGroupId) {
            // Fetch FnBs from the package's price group
            axios.get('/api/get-fnbs-by-price-group', {
                params: {
                    price_group_id: priceGroupId
                }
            })
            .then(function(response) {
                const priceGroupFnbs = response.data.fnbs || [];
                
                // Store filtered FnBs globally for this package
                currentPackageFnbs = priceGroupFnbs;
                
                // Add one empty F&B item with filtered options
                addFnbItem(priceGroupFnbs);
            })
            .catch(function(error) {
                console.log('Error loading FnBs from price group:', error);
                // Fallback to all FnBs
                currentPackageFnbs = null;
                addFnbItem();
            });
        }

        function addFnbItem(filteredFnbs = null, initialFnbId = null, initialQty = 1) {
            const container = document.getElementById('fnb-container');
            const itemDiv = document.createElement('div');
            itemDiv.className = 'fnb-item border-bottom p-3';
            
            // Use filtered FnBs if provided, otherwise use all FnBs
            const availableFnbs = filteredFnbs || fnbs;
            
            // Check if this is for package F&B items (has filteredFnbs and custom package is selected)
            const isCustomPackage = document.getElementById('custom_package').checked;
            const isPackageFnb = filteredFnbs && isCustomPackage;
            
            // Adjust column layout based on whether this is package F&B
            const barangCol = isPackageFnb ? 'col-md-6' : 'col-md-4';
            const qtyCol = isPackageFnb ? 'col-md-4' : 'col-md-2';
            const priceCol = isPackageFnb ? 'd-none' : 'col-md-3';
            const totalCol = isPackageFnb ? 'd-none' : 'col-md-2';
            const removeCol = isPackageFnb ? 'col-md-2 text-end' : 'col-md-1 text-end';
            
            const priceRequired = isPackageFnb ? '' : 'required';
            const priceValue = isPackageFnb ? 'value="0"' : '';
            
            const options = availableFnbs.map(fnb => {
                const stockText = fnb.stok == -1 ? 'Unlimited' : fnb.stok;
                const selected = (initialFnbId && parseInt(initialFnbId) === parseInt(fnb.id)) ? 'selected' : '';
                return `<option value="${fnb.id}" data-price="${fnb.harga_jual}" data-stock="${fnb.stok}" ${selected}>${fnb.nama} (Stok: ${stockText})</option>`;
            }).join('');

            itemDiv.innerHTML = `
                <div class="row g-2 align-items-end">
                    <div class="${barangCol}">
                        <label class="form-label small mb-1">Barang</label>
                        <select class="form-control form-control-sm fnb-select" name="fnb_ids[]" onchange="updateFnbPrice(this, ${fnbIndex})" required>
                            <option value="">Pilih Barang</option>
                            ${options}
                        </select>
                        <small class="text-muted fnb-stock-info" style="display: none; color: #28a745 !important; font-weight: bold;">
                            <i class="fas fa-infinity"></i> <strong>Stok Unlimited</strong> - Bisa dipesan tanpa batasan
                        </small>
                    </div>
                    <div class="${qtyCol}">
                        <label class="form-label small mb-1">Qty</label>
                        <input type="number" class="form-control form-control-sm fnb-qty" name="fnbs_qty[]" min="1" value="${initialQty}" onchange="updateFnbTotal()" required>
                    </div>
                    <div class="${priceCol}">
                        <label class="form-label small mb-1">Harga Jual</label>
                        <input type="number" class="form-control form-control-sm fnb-price" name="fnbs_harga[]" readonly ${priceRequired} ${priceValue}>
                    </div>
                    <div class="${totalCol}">
                        <label class="form-label small mb-1">Total</label>
                        <input type="number" class="form-control form-control-sm fnb-total" readonly>
                    </div>
                    <div class="${removeCol}">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeFnbItem(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(itemDiv);
            
            // If initial selection was provided, trigger update price and total
            const select = itemDiv.querySelector('.fnb-select');
            if (initialFnbId) {
                updateFnbPrice(select, fnbIndex);
            }
            
            fnbIndex++;
        }

        function updateFnbPrice(select, index) {
            const itemDiv = select.closest('.fnb-item');
            const priceInput = itemDiv.querySelector('.fnb-price');
            const qtyInput = itemDiv.querySelector('.fnb-qty');
            const stockInfo = itemDiv.querySelector('.fnb-stock-info');
            const selectedOption = select.options[select.selectedIndex];
            const stock = parseInt(selectedOption.getAttribute('data-stock'));
            
            // Only set price if price input exists (not hidden for package F&B)
            if (priceInput) {
                const isCustomPackage = document.getElementById('custom_package').checked;
                // If this is a custom package and we are in the filtered FnB section (implied by context if price input is hidden)
                // we set price to 0 for the form submission
                if (isCustomPackage && itemDiv.querySelector('.d-none .fnb-price')) {
                    priceInput.value = 0;
                } else {
                    priceInput.value = selectedOption.getAttribute('data-price') || 0;
                }
            }
            
            // Show/hide unlimited info and remove max limit for qty
            if (stock === -1) {
                if (stockInfo) stockInfo.style.display = 'block';
                qtyInput.removeAttribute('max');
                qtyInput.setAttribute('title', 'Stok unlimited - bisa dipesan tanpa batasan');
            } else {
                if (stockInfo) stockInfo.style.display = 'none';
                qtyInput.setAttribute('max', stock);
                qtyInput.setAttribute('title', `Stok tersisa: ${stock}`);
            }
            
            // Update F&B total to check if this item is package-included
            updateFnbTotal();
        }

        function updateFnbTotal() {
            let totalFnb = 0;
            const isCustomPackage = document.getElementById('custom_package').checked;
            
            document.querySelectorAll('.fnb-item').forEach(item => {
                const qtyInput = item.querySelector('.fnb-qty');
                const priceInput = item.querySelector('.fnb-price');
                const totalInput = item.querySelector('.fnb-total');
                const selectInput = item.querySelector('.fnb-select');

                if (qtyInput && selectInput) {
                    const qty = parseFloat(qtyInput.value) || 0;
                    const selectedFnbId = parseInt(selectInput.value);
                    
                    // For custom packages, all selected items from the price group are included
                    let isPackageIncluded = false;
                    if (isCustomPackage && selectedFnbId) {
                        isPackageIncluded = true;
                    }
                    
                    // Calculate total only for non-package items with existing price input
                    let total = 0;
                    if (priceInput && !isPackageIncluded && selectedFnbId) {
                        const price = parseFloat(priceInput.value) || 0;
                        total = qty * price;
                        
                        // Set the total display
                        if (totalInput) {
                            totalInput.value = total;
                        }
                    } else {
                        // For package items or missing data, set total to 0
                        if (totalInput) {
                            totalInput.value = 0;
                        }
                        
                        // Add visual indicator for package-included items
                        if (totalInput && isPackageIncluded) {
                            totalInput.style.backgroundColor = '#e8f5e8';
                            totalInput.setAttribute('title', 'Termasuk dalam paket - tidak dikenakan biaya tambahan');
                        } else if (totalInput) {
                            totalInput.style.backgroundColor = '';
                            totalInput.removeAttribute('title');
                        }
                    }
                    
                    // Only add to total if not package included
                    if (!isPackageIncluded) {
                        totalFnb += total;
                    }
                }
            });
            
            const fnbTotalElement = document.getElementById('fnb_total');
            if (fnbTotalElement) {
                fnbTotalElement.value = totalFnb;
            }
            updateGrandTotal();
        }

        function updateGrandTotal() {
            const isPrepaid = document.getElementById('prepaid').checked;
            const isCustomPackage = document.getElementById('custom_package').checked;
            const totalPs = parseFloat(document.getElementById('total_ps').value) || 0;
            const totalFnb = parseFloat(document.getElementById('fnb_total').value) || 0;
            
            if (isCustomPackage) {
                // For custom packages, only use package price
                const packagePrice = parseFloat(document.getElementById('harga').value) || 0;
                document.getElementById('total').value = packagePrice;
            } else if (isPrepaid) {
                // For regular prepaid, use device price + F&B items
                document.getElementById('total').value = totalPs + totalFnb;
            } else {
                // For postpaid, only F&B items
                document.getElementById('total').value = totalFnb;
            }
        }

        function removeFnbItem(button) {
            const item = button.closest('.fnb-item');
            item.style.transition = 'opacity 0.3s';
            item.style.opacity = '0';
            
            // Remove the item after the fade out animation
            setTimeout(() => {
                item.remove();
                updateFnbTotal();
                
                // Remove the border from the last item if it's the only one
                const items = document.querySelectorAll('.fnb-item');
                if (items.length > 0) {
                    items[items.length - 1].classList.remove('border-bottom');
                }
            }, 300);
        }



        function loadCustomPackageDetails() {
            const customPackageSelect = document.getElementById('custom_package_id');
            const selectedOption = customPackageSelect.options[customPackageSelect.selectedIndex];
            const packageData = selectedOption.getAttribute('data-details');

            if (!packageData) {
                document.getElementById('harga').value = 0;
                document.getElementById('total').value = 0;
                document.getElementById('custom-package-details').style.display = 'none';
                // Reset device selection when no package is selected
                resetDeviceSelection();
                // Clear package F&B IDs
                packageFnbIds.clear();
                // Update grand total to reset properly
                updateGrandTotal();
                return;
            }

            const package = JSON.parse(packageData);

            // Display package details
            document.getElementById('selected-package-name').textContent = package.nama_paket;
            document.getElementById('selected-package-price').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(package.harga_total);

            // Display playstations
            const devicesList = document.getElementById('selected-package-devices');
            devicesList.innerHTML = '';
            if (package.playstations && package.playstations.length > 0) {
                package.playstations.forEach(playstation => {
                    const li = document.createElement('li');
                    li.textContent = playstation.nama + ' - ' + playstation.pivot.lama_main + ' menit';
                    devicesList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'Tidak ada perangkat';
                devicesList.appendChild(li);
            }

            // Display F&B
            const fnbList = document.getElementById('selected-package-fnb');
            fnbList.innerHTML = '';
            if (package.fnbs && package.fnbs.length > 0) {
                package.fnbs.forEach(fnb => {
                    const li = document.createElement('li');
                    li.textContent = fnb.nama; // Only display name as requested
                    fnbList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'Tidak ada F&B';
                fnbList.appendChild(li);
            }

            // Store package F&B IDs to exclude them from pricing
            packageFnbIds.clear();
            if (package.fnbs && package.fnbs.length > 0) {
                package.fnbs.forEach(fnb => {
                    packageFnbIds.add(fnb.id);
                });
            }

            // Show the details section
            document.getElementById('custom-package-details').style.display = 'block';

            // Set the package total as harga (since it's the total for the package)
            document.getElementById('harga').value = package.harga_total;
            
            // Update the total using the grand total function to include F&B items
            updateGrandTotal();

            // Filter devices based on package playstations and enable device selection
            filterDevicesForCustomPackage(package);

            // Clear existing FnB items
            const fnbContainer = document.getElementById('fnb-container');
            fnbContainer.innerHTML = '';

            // Collect all price group IDs from the package (from direct relationship and from FnB items)
            let priceGroupIds = [];
            
            // 1. From many-to-many relationship
            if (package.price_groups && package.price_groups.length > 0) {
                package.price_groups.forEach(pg => priceGroupIds.push(pg.id));
            }
            
            // 2. From singular relationship (backward compatibility)
            if (package.price_group_id && !priceGroupIds.includes(package.price_group_id)) {
                priceGroupIds.push(package.price_group_id);
            }
            
            // 3. From F&B items attached to the package
            if (package.fnbs && package.fnbs.length > 0) {
                package.fnbs.forEach(fnb => {
                    if (fnb.price_group_id && !priceGroupIds.includes(fnb.price_group_id)) {
                        priceGroupIds.push(fnb.price_group_id);
                    }
                });
            }

            // Load FnB items from package's price groups for manual selection
            if (priceGroupIds.length > 0) {
                // Update F&B section title to show it's from price groups
                document.getElementById('fnb-section-title').textContent = 'FnB Items dari Kelompok Harga Paket';
                document.getElementById('fnb-section-subtitle').textContent = `Pilih F&B yang tersedia dalam ${priceGroupIds.length} kelompok harga (item dalam paket ini tidak dikenakan biaya tambahan)`;
                loadFnbsFromPriceGroups(priceGroupIds, package.fnbs);
            } else {
                // If no price groups, load all FnBs
                document.getElementById('fnb-section-title').textContent = 'FnB Items (Opsional)';
                document.getElementById('fnb-section-subtitle').textContent = '';
                addFnbItem(); // Add one empty F&B item
            }

            // Update FNB total
            updateFnbTotal();
        }

        function resetDeviceSelection() {
            const deviceSelect = document.getElementById('device_id');
            const deviceHelpText = document.getElementById('device-help-text');
            
            // Reset to original state for non-custom package transactions
            deviceSelect.innerHTML = '<option value="dummy" selected disabled hidden>Pilih nama perangkat</option>';
            
            // Re-populate with all available devices
            devices.forEach(device => {
                if (device.status === 'Tersedia') {
                    const option = document.createElement('option');
                    option.value = device.id;
                    option.textContent = `${device.nama} - ${device.playstation_names || 'Tidak Diketahui'}`;
                    // Add the playstations data attribute
                    if (device.playstations && device.playstations.length > 0) {
                        option.setAttribute('data-playstations', JSON.stringify(device.playstations));
                        option.setAttribute('data-playstation-id', device.playstation_id || '');
                    }
                    deviceSelect.appendChild(option);
                }
            });
            
            deviceSelect.disabled = {{ $noDevices ? 'true' : 'false' }};
            deviceHelpText.textContent = 'Pilih perangkat yang tersedia';
        }

        function handleDeviceChange() {
            const deviceSelect = document.getElementById('device_id');
            const selectedOption = deviceSelect.options[deviceSelect.selectedIndex];
            const playstationTypeRow = document.getElementById('playstation-type-row');
            const playstationSelect = document.getElementById('playstation_id');
            
            // Get playstations data from the selected option
            const playstationsData = selectedOption.getAttribute('data-playstations');
            
            if (!playstationsData || playstationsData === 'null' || playstationsData === '[]') {
                // No multiple playstations, hide the selection row
                playstationTypeRow.style.display = 'none';
                playstationSelect.innerHTML = '';
                showPrice(); // Show price for single playstation
                return;
            }
            
            try {
                const playstations = JSON.parse(playstationsData);
                
                if (playstations && playstations.length > 1) {
                    // Multiple playstations available, show selection row
                    playstationTypeRow.style.display = 'flex';
                    playstationSelect.innerHTML = '<option value="" selected disabled hidden>Pilih jenis playstation</option>';
                    
                    // Add each playstation as an option
                    playstations.forEach(playstation => {
                        const option = document.createElement('option');
                        option.value = playstation.id;
                        option.textContent = `${playstation.nama} - Rp ${new Intl.NumberFormat('id-ID').format(playstation.harga)}`;
                        playstationSelect.appendChild(option);
                    });
                    
                    // Clear price until user selects a playstation type
                    document.getElementById('harga').value = '';
                    document.getElementById('total_ps').value = '';
                } else if (playstations && playstations.length === 1) {
                    // Only one playstation, hide selection and use it directly
                    playstationTypeRow.style.display = 'none';
                    playstationSelect.innerHTML = '';
                    showPrice(); // Show price for the single playstation
                } else {
                    // No playstations, hide selection
                    playstationTypeRow.style.display = 'none';
                    playstationSelect.innerHTML = '';
                    showPrice();
                }
            } catch (error) {
                console.error('Error parsing playstations data:', error);
                playstationTypeRow.style.display = 'none';
                playstationSelect.innerHTML = '';
                showPrice();
            }
        }

        function filterDevicesForCustomPackage(package) {
            const deviceSelect = document.getElementById('device_id');
            const deviceHelpText = document.getElementById('device-help-text');

            // Clear existing options
            deviceSelect.innerHTML = '<option value="" selected disabled hidden>Pilih perangkat yang sesuai dengan paket</option>';

            console.log('=== DEBUGGING CUSTOM PACKAGE DEVICE FILTERING ===');
            console.log('Package data:', package);
            console.log('Devices array length:', devices.length);
            console.log('Devices array:', devices);

            if (!package.playstations || package.playstations.length === 0) {
                console.log('Package has no playstations');
                deviceSelect.disabled = false;
                deviceHelpText.textContent = 'Paket ini tidak memiliki perangkat yang tersedia';
                return;
            }

            const playstationIds = package.playstations.map(ps => parseInt(ps.id));
            console.log('Playstation IDs in package:', playstationIds);

            // Filter devices based on playstation types and availability
            const availableDevices = devices.filter(device => {
                console.log('Checking device:', device);
                console.log('Device playstation_id:', device.playstation_id);
                console.log('Device playstations:', device.playstations);
                console.log('Device status:', device.status);

                // Check if device exists
                if (!device) {
                    console.log('Device rejected: missing device');
                    return false;
                }

                // Check if device status is 'Tersedia'
                if (String(device.status).toLowerCase() !== 'tersedia') {
                    console.log('Device rejected: status not Tersedia');
                    return false;
                }

                // Check if device's playstations match any in the package
                // First check the new many-to-many relationship
                if (device.playstations && Array.isArray(device.playstations) && device.playstations.length > 0) {
                    const devicePsIds = device.playstations.map(ps => parseInt(ps.id));
                    const hasMatch = devicePsIds.some(psId => playstationIds.includes(psId));
                    console.log('Device playstation IDs:', devicePsIds);
                    console.log('Has match with package playstations:', hasMatch);
                    if (hasMatch) return true;
                }
                
                // Fallback to old single playstation_id relationship
                if (device.playstation_id) {
                    const psId = parseInt(device.playstation_id);
                    const matches = Number.isFinite(psId) && playstationIds.includes(psId);
                    console.log('Device playstation_id matches package (fallback):', matches);
                    return matches;
                }

                console.log('Device rejected: no playstation relationship found');
                return false;
            });

            console.log('Filtered available devices:', availableDevices);
            console.log('Number of available devices:', availableDevices.length);

            if (availableDevices.length === 0) {
                console.log('No devices available for this package');
                deviceSelect.disabled = false;
                deviceHelpText.textContent = 'Tidak ada perangkat tersedia yang sesuai dengan paket ini';
                return;
            }

            // Enable device selection and populate options
            deviceSelect.disabled = false;
            availableDevices.forEach((device, index) => {
                const option = document.createElement('option');
                
                // Get PlayStation name(s) for the device
                let playstationName = 'Tidak Diketahui';
                if (device.playstations && Array.isArray(device.playstations) && device.playstations.length > 0) {
                    // New many-to-many relationship - show all PlayStation types
                    const psNames = device.playstations.map(ps => ps.nama).join(', ');
                    playstationName = psNames;
                } else if (device.playstation && device.playstation.nama) {
                    // Fallback to old single relationship
                    playstationName = device.playstation.nama;
                }
                
                option.value = device.id;
                option.textContent = `${device.nama} - ${playstationName}`;
                deviceSelect.appendChild(option);

                // Auto-select the first device
                if (index === 0) {
                    deviceSelect.value = device.id;
                    // Trigger the price calculation
                    showPrice();
                }
            });

            deviceHelpText.textContent = `${availableDevices.length} perangkat tersedia yang sesuai dengan paket`;
            console.log('=== END DEBUGGING ===');
        }

        function showTime() {
            const waktu_mulai = document.getElementById('waktu_mulai').value;
            const waktu_selesai = document.getElementById('waktu_Selesai');
            const jamMainSelect = document.getElementById('jam_main_select');
            const jamMainInput = document.getElementById('jam_main');
            let jam_main = 0;
            
            if (jamMainSelect.value && jamMainSelect.value !== 'custom') {
                jam_main = jamMainSelect.value;
            } else if (jamMainInput.value) {
                jam_main = jamMainInput.value;
            }
            
            console.log(jam_main)
            const tanggal = new Date().toISOString().slice(0, 10);
            const date = new Date();

            const waktu_mulai_split = waktu_mulai.split(':');
            date.setHours(waktu_mulai_split[0]);
            date.setMinutes(waktu_mulai_split[1]);
            console.log(date)
            date.setHours(date.getHours() + parseInt(jam_main));

            waktu_selesai.value =
                `${date.getHours()}:${date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()}`;

            console.log(`${date.getHours()}:${date.getMinutes()}`)
        }

        function validateTransactionForm() {
            const tipeTransaksi = document.querySelector('input[name="tipe_transaksi"]:checked');
            const nama = document.getElementById('nama').value.trim();
            const deviceId = document.getElementById('device_id').value;
            const playstationId = document.getElementById('playstation_id').value;
            const customPackageId = document.getElementById('custom_package_id').value;
            const jamMainSelect = document.getElementById('jam_main_select').value;
            const jamMainInput = document.getElementById('jam_main').value.trim();
            const harga = document.getElementById('harga').value.trim();
            const total = document.getElementById('total').value.trim();

            let errors = [];

            // Check transaction type
            if (!tipeTransaksi) {
                errors.push('Silakan pilih jenis transaksi (Paket, Lost Time, atau Custom Paket)');
            }

            // Check required name
            if (!nama) {
                errors.push('Nama pelanggan wajib diisi');
            }

            // Check device selection
            if (!deviceId || deviceId === 'dummy' || deviceId === '') {
                errors.push('Silakan pilih perangkat yang tersedia');
            }

            // Type-specific validations
            if (tipeTransaksi && tipeTransaksi.value === 'custom_package') {
                if (!customPackageId) {
                    errors.push('Silakan pilih custom package terlebih dahulu');
                }
            } else if (tipeTransaksi && tipeTransaksi.value === 'prepaid') {
                // Check jam main for prepaid
                if (!jamMainSelect && !jamMainInput) {
                    errors.push('Silakan pilih jam main atau masukkan jam main secara manual');
                } else if (jamMainSelect === 'custom' && !jamMainInput) {
                    errors.push('Silakan masukkan jumlah jam main untuk custom input');
                } else if (jamMainInput && (isNaN(jamMainInput) || parseFloat(jamMainInput) <= 0)) {
                    errors.push('Jam main harus berupa angka positif');
                }

                // Check harga
                if (!harga || harga === '0' || isNaN(harga)) {
                    errors.push('Harga per jam harus terisi dengan benar');
                }
            }

            // Check for multi-playstation device without playstation selection
            // Check for multi-playstation device without playstation selection
            if (deviceId && deviceId !== 'dummy' && deviceId !== '') {
                // Check if device has multiple playstation types and playstation_id is not selected
                const deviceSelect = document.getElementById('device_id');
                const selectedOption = deviceSelect.options[deviceSelect.selectedIndex];
                const playstationsData = selectedOption && selectedOption.getAttribute('data-playstations');
                
                if (playstationsData) {
                    try {
                        const playstations = JSON.parse(playstationsData);
                        if (Array.isArray(playstations) && playstations.length > 1 && !playstationId) {
                            errors.push('Perangkat ini memiliki beberapa jenis perangkat. Silakan pilih jenis perangkat terlebih dahulu.');
                        }
                    } catch (e) {
                        console.error("Error parsing playstations data during validation", e);
                    }
                }
            }

            // Check total
            // Allow total to be 0 for postpaid transactions
            if (!tipeTransaksi || tipeTransaksi.value !== 'postpaid') {
                if (!total || total === '0' || isNaN(total)) {
                    errors.push('Total transaksi harus terisi dengan benar');
                }
            }

            // Show errors if any
            if (errors.length > 0) {
                let errorMessage = '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
                errorMessage += '<h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Form Belum Lengkap!</h6>';
                errorMessage += '<p class="mb-2">Mohon lengkapi field yang wajib diisi berikut:</p>';
                errorMessage += '<ul class="mb-0">';
                
                errors.forEach(error => {
                    errorMessage += `<li>${error}</li>`;
                });
                
                errorMessage += '</ul>';
                errorMessage += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                errorMessage += '</div>';

                // Remove any existing alerts
                const existingAlerts = document.querySelectorAll('.alert');
                existingAlerts.forEach(alert => alert.remove());

                // Insert the error message at the top of the form
                const form = document.querySelector('form');
                form.insertAdjacentHTML('afterbegin', errorMessage);

                // Scroll to top to show the error
                window.scrollTo({ top: 0, behavior: 'smooth' });

                return false;
            }

            return true;
        }
    </script>
@endsection