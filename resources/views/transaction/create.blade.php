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
                <form method="POST" action="{{ route('transaction.store') }}">
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
                            <label for="device" class="form-label small">Nama Perangkat</label>
                            <select class="form-control" id="device_id" name="device_id" onchange="showPrice()" {{ $noDevices ? 'disabled' : '' }}>
                                @if($noDevices)
                                    <option value="" selected>Semua perangkat sedang digunakan</option>
                                @else
                                    <option value="dummy" selected disabled hidden>Pilih nama perangkat</option>
                                    @foreach ($devices as $device)
                                        @php
                                            $playstationName = $device->playstation ? $device->playstation->nama : 'Tidak Diketahui';
                                            $displayText = $device->nama . ' - ' . $playstationName;
                                            $dataPlaystationId = $device->playstation ? $device->playstation->id : '';
                                        @endphp
                                        @if (old('device_id') == $device->id)
                                            <option value="{{ $device->id }}" data-playstation-id="{{ $dataPlaystationId }}" selected>{{ $displayText }}</option>
                                        @else
                                            <option value="{{ $device->id }}" data-playstation-id="{{ $dataPlaystationId }}">{{ $displayText }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <small class="text-muted" id="device-help-text">Pilih perangkat yang tersedia</small>
                        </div>
                    </div>

                   
                    <!-- Row 2.5.5: Device Selection for Custom Package -->
                    <div class="row mb-2" id="custom-package-device-row" style="display: none;">
                        <div class="col-md-12">
                            <label for="custom_package_device_id" class="form-label small">Pilih Perangkat</label>
                            <select class="form-control" id="custom_package_device_id">
                                <option value="" selected disabled hidden>Pilih perangkat yang tersedia</option>
                            </select>
                            <small class="text-muted">Hanya perangkat yang sesuai dengan jenis PlayStation dalam paket yang akan ditampilkan</small>
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
                            <label for="harga" class="form-label small">Harga per Jam</label>
                            <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga"
                                name="harga" required readonly value="{{ old('harga') }}">
                            @error('harga')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="jam_main" class="form-label small">Jam Main</label>
                            <input type="number" class="form-control @error('jam_main') is-invalid @enderror" id="jam_main"
                                name="jam_main" autofocus value="{{ old('jam_main') }}" onchange="showChangeMaster()" min="1" max="24">
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
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="text-primary mb-0">FnB Items (Opsional)</h6>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="addFnbItem()">+ Tambah FnB</button>
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
        const fnbs = @json($fnbs);
        const devices = @json($devices);

        // if (member) member.style.display = "none";
        if (nama) nama.style.display = "block";
        console.log(d.getHours() + " : " + d.getMinutes());

        document.getElementById('waktu_mulai').value =
            `${d.getHours() < 10 ? '0' + d.getHours() : d.getHours() }:${d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes()}`;

        // Always populate price on load for auto-selected device
        document.addEventListener('DOMContentLoaded', function() {
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
            // Hide custom package device row since we use main device select
            document.getElementById('custom-package-device-row').style.display = 'none';

            // Handle device selection based on transaction type
            if (isCustomPackage) {
                // Disable device selection initially for custom package
                deviceSelect.disabled = true;
                deviceHelpText.textContent = 'Pilih paket terlebih dahulu, lalu pilih perangkat';
                // Clear device selection
                deviceSelect.value = '';
                document.getElementById('jam_main').value = '';
                document.getElementById('waktu_Selesai').value = '';
                document.getElementById('total_ps').value = '';
            } else {
                // Enable device selection for prepaid/postpaid
                deviceSelect.disabled = {{ $noDevices ? 'true' : 'false' }};
                deviceHelpText.textContent = 'Pilih perangkat yang tersedia';
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
            showTotal();
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
            const device = document.getElementById('device_id').value;
            const harga = document.getElementById('harga');
            const totalField = document.getElementById('total_ps');
            const jamMain = parseFloat(document.getElementById('jam_main').value);

            if (!device || device === "dummy") {
                harga.value = '';
                totalField.value = '';
                return;
            }

            console.log(device);
            axios.get('/api/get-harga', {
                    params: {
                        device: device
                    }
                })
                .then(function(response) {
                    console.log(response.data);
                    harga.value = response.data.harga;
                    if (!isNaN(jamMain) && jamMain > 0) {
                        const total = parseFloat(harga.value) * jamMain;
                        totalField.value = total;
                        updateGrandTotal();
                    } else {
                        totalField.value = '';
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    harga.value = '';
                    totalField.value = '';
                });
        }

        function showTotal() {
            const harga = parseFloat(document.getElementById('harga').value);
            const jamMain = parseFloat(document.getElementById('jam_main').value);
            const total = harga * jamMain;
            document.getElementById('total_ps').value = total;
            updateGrandTotal();
        }

        function addFnbItem() {
            const container = document.getElementById('fnb-container');
            const itemDiv = document.createElement('div');
            itemDiv.className = 'fnb-item border-bottom p-3';
            itemDiv.innerHTML = `
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Barang</label>
                        <select class="form-control form-control-sm fnb-select" name="fnb_ids[]" onchange="updateFnbPrice(this, ${fnbIndex})" required>
                            <option value="">Pilih Barang</option>
                            ${fnbs.map(fnb => {
                                const stockText = fnb.stok == -1 ? 'Unlimited' : fnb.stok;
                                return `<option value="${fnb.id}" data-price="${fnb.harga_jual}" data-stock="${fnb.stok}">${fnb.nama} (Stok: ${stockText})</option>`;
                            }).join('')}
                        </select>
                        <small class="text-muted fnb-stock-info" style="display: none; color: #28a745 !important; font-weight: bold;">
                            <i class="fas fa-infinity"></i> <strong>Stok Unlimited</strong> - Bisa dipesan tanpa batasan
                        </small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Qty</label>
                        <input type="number" class="form-control form-control-sm fnb-qty" name="fnbs_qty[]" min="1" value="1" onchange="updateFnbTotal()" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Harga Jual</label>
                        <input type="number" class="form-control form-control-sm fnb-price" name="fnbs_harga[]" readonly required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Total</label>
                        <input type="number" class="form-control form-control-sm fnb-total" readonly>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeFnbItem(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(itemDiv);
            fnbIndex++;
        }

        function updateFnbPrice(select, index) {
            const priceInput = select.closest('.fnb-item').querySelector('.fnb-price');
            const qtyInput = select.closest('.fnb-item').querySelector('.fnb-qty');
            const stockInfo = select.closest('.fnb-item').querySelector('.fnb-stock-info');
            const selectedOption = select.options[select.selectedIndex];
            const stock = parseInt(selectedOption.getAttribute('data-stock'));
            
            priceInput.value = selectedOption.getAttribute('data-price') || 0;
            
            // Show/hide unlimited info and remove max limit for qty
            // Show/hide unlimited info and remove max limit for qty
            if (stock === -1) {
                if (stockInfo) stockInfo.style.display = 'block';
                qtyInput.removeAttribute('max');
                qtyInput.setAttribute('title', 'Stok unlimited - bisa dipesan tanpa batasan');
            } else {
                if (stockInfo) stockInfo.style.display = 'none';
                qtyInput.setAttribute('max', stock);
                qtyInput.removeAttribute('title');
            }
            
            updateFnbTotal();
        }

        function updateFnbTotal() {
            let totalFnb = 0;
            document.querySelectorAll('.fnb-item').forEach(item => {
                const qty = parseFloat(item.querySelector('.fnb-qty').value) || 0;
                const price = parseFloat(item.querySelector('.fnb-price').value) || 0;
                const total = qty * price;
                item.querySelector('.fnb-total').value = total;
                totalFnb += total;
            });
            document.getElementById('fnb_total').value = totalFnb;
            updateGrandTotal();
        }

        function updateGrandTotal() {
            const isPrepaid = document.getElementById('prepaid').checked;
            const totalPs = parseFloat(document.getElementById('total_ps').value) || 0;
            const totalFnb = parseFloat(document.getElementById('fnb_total').value) || 0;
            if (isPrepaid) {
                document.getElementById('total').value = totalPs + totalFnb;
            } else {
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
                    li.textContent = fnb.nama + ' (Qty: ' + fnb.pivot.quantity + ') - Rp ' + new Intl.NumberFormat('id-ID').format(fnb.harga_jual);
                    fnbList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'Tidak ada F&B';
                fnbList.appendChild(li);
            }

            // Show the details section
            document.getElementById('custom-package-details').style.display = 'block';

            // Set the package total as harga (since it's the total for the package)
            document.getElementById('harga').value = package.harga_total;
            // Set the total price from the package
            document.getElementById('total').value = package.harga_total;

            // Filter devices based on package playstations
            filterDevicesByPackage(package);

            // Clear existing FnB items
            const fnbContainer = document.getElementById('fnb-container');
            fnbContainer.innerHTML = '';

            // Add FnB items from the package (read-only)
            if (package.fnbs && package.fnbs.length > 0) {
                package.fnbs.forEach((fnb, index) => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'fnb-item border-bottom p-3 package-fnb-item';
                    itemDiv.innerHTML = `
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small mb-1">Barang</label>
                                <input type="text" class="form-control form-control-sm" value="${fnb.nama}" readonly>
                                <input type="hidden" name="fnb_ids[]" value="${fnb.id}">
                                <small class="text-info">
                                    <i class="fas fa-info-circle"></i> Item dari paket (tidak dapat diubah)
                                </small>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-1">Qty</label>
                                <input type="number" class="form-control form-control-sm" name="fnbs_qty[]" value="${fnb.pivot.quantity}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Harga Jual</label>
                                <input type="number" class="form-control form-control-sm" name="fnbs_harga[]" value="${fnb.harga_jual}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-1">Total</label>
                                <input type="number" class="form-control form-control-sm" value="${fnb.pivot.quantity * fnb.harga_jual}" readonly>
                            </div>
                            <div class="col-md-1 text-end">
                                <span class="badge bg-info">Paket</span>
                            </div>
                        </div>
                    `;
                    fnbContainer.appendChild(itemDiv);
                });
            }

            // Update FNB total
            updateFnbTotal();

            // Load devices for this custom package
            loadCustomPackageDevices(package);
            // Also update the main device select to show only devices for this package
            loadPackageSpecificDevices(package);
        }

        function loadAllCustomPackageDevices() {
            const deviceSelect = document.getElementById('device_id');

            // Clear existing options
            deviceSelect.innerHTML = '<option value="" selected disabled hidden>Pilih nama perangkat</option>';

            // Get all unique playstation IDs from all custom packages
            const allPlaystationIds = new Set();
            @json($customPackages).forEach(package => {
                if (package.playstations) {
                    package.playstations.forEach(ps => allPlaystationIds.add(ps.id));
                }
            });

            // Filter devices based on playstation types used in custom packages and availability
            const availableDevices = devices.filter(device =>
                allPlaystationIds.has(device.playstation_id) && device.status === 'Tersedia'
            );

            // Populate device options
            availableDevices.forEach(device => {
                const option = document.createElement('option');
                const playstationName = device.playstation ? device.playstation.nama : 'Tidak Diketahui';
                option.value = device.id;
                option.textContent = `${device.nama} - ${playstationName}`;
                deviceSelect.appendChild(option);
            });
        }

        function loadPackageSpecificDevices(package) {
            const deviceSelect = document.getElementById('device_id');

            // Clear existing options
            deviceSelect.innerHTML = '<option value="" selected disabled hidden>Pilih nama perangkat</option>';

            if (!package.playstations || package.playstations.length === 0) {
                return;
            }

            // Get playstation IDs from the selected package
            const playstationIds = package.playstations.map(ps => ps.id);

            // Filter devices based on playstation types from this package and availability
            const availableDevices = devices.filter(device =>
                playstationIds.includes(device.playstation_id) && device.status === 'Tersedia'
            );

            // Populate device options
            availableDevices.forEach(device => {
                const option = document.createElement('option');
                const playstationName = device.playstation ? device.playstation.nama : 'Tidak Diketahui';
                option.value = device.id;
                option.textContent = `${device.nama} - ${playstationName}`;
                deviceSelect.appendChild(option);
            });
        }

        function loadCustomPackageDevices(package) {
            const deviceSelect = document.getElementById('custom_package_device_id');
            const deviceRow = document.getElementById('custom-package-device-row');

            // Clear existing options
            deviceSelect.innerHTML = '<option value="" selected disabled hidden>Pilih perangkat yang tersedia</option>';

            if (!package.playstations || package.playstations.length === 0) {
                deviceRow.style.display = 'none';
                return;
            }

            // Get playstation IDs from package
            const playstationIds = package.playstations.map(ps => ps.id);

            // Filter devices based on playstation types and availability
            const availableDevices = devices.filter(device =>
                playstationIds.includes(device.playstation_id) && device.status === 'Tersedia'
            );

            if (availableDevices.length === 0) {
                deviceRow.style.display = 'none';
                return;
            }

            // Populate device options
            availableDevices.forEach(device => {
                const option = document.createElement('option');
                const playstationName = device.playstation ? device.playstation.nama : 'Tidak Diketahui';
                option.value = device.id;
                option.textContent = `${device.nama} - ${playstationName}`;
                deviceSelect.appendChild(option);
            });

            // Show device selection row
            deviceRow.style.display = 'block';
        }

        function filterDevicesByPackage(package) {
            const deviceSelect = document.getElementById('device_id');
            const deviceHelpText = document.getElementById('device-help-text');
            
            // Clear existing options
            deviceSelect.innerHTML = '<option value="" selected disabled hidden>Pilih perangkat yang tersedia</option>';

            if (!package.playstations || package.playstations.length === 0) {
                deviceSelect.disabled = true;
                deviceHelpText.textContent = 'Tidak ada perangkat yang tersedia untuk paket ini';
                return;
            }

            // Get playstation IDs from package
            const playstationIds = package.playstations.map(ps => ps.id);
            
            // Get all devices from the server data
            const allDevices = @json($devices);
            
            // Filter devices based on playstation types and availability
            const availableDevices = allDevices.filter(device => {
                return playstationIds.includes(device.playstation_id) && device.status === 'Tersedia';
            });

            if (availableDevices.length === 0) {
                deviceSelect.disabled = true;
                deviceHelpText.textContent = 'Tidak ada perangkat tersedia yang sesuai dengan paket ini';
                return;
            }

            // Enable device selection and populate options
            deviceSelect.disabled = false;
            availableDevices.forEach(device => {
                const option = document.createElement('option');
                const playstationName = device.playstation ? device.playstation.nama : 'Tidak Diketahui';
                option.value = device.id;
                option.textContent = `${device.nama} - ${playstationName}`;
                deviceSelect.appendChild(option);
            });

            deviceHelpText.textContent = `${availableDevices.length} perangkat tersedia yang sesuai dengan paket`;
        }

        function showTime() {
            const waktu_mulai = document.getElementById('waktu_mulai').value;
            const waktu_selesai = document.getElementById('waktu_Selesai');
            const jam_main = document.getElementById('jam_main').value;
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
    </script>
@endsection

