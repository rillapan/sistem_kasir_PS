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
                                    Prepaid
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe_transaksi" id="postpaid" value="postpaid" onchange="toggleFields()">
                                <label class="form-check-label" for="postpaid">
                                    Postpaid
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Debug output for devices -->
                

                    <!-- Row 1: Nama and Device -->
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="nama" class="form-label small">Nama</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                name="nama" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6" id="device">
                            <label for="device" class="form-label small">Nama Perangkat</label>
                            <select class="form-control" id="device_id" name="device_id" onchange="showPrice()" {{ $noDevices ? 'disabled' : '' }}>
                                @if($noDevices)
                                    <option value="" selected>Semua perangkat sedang digunakan</option>
                                @else
                                    <option value="dummy" selected disabled hidden>Pilih nama perangkat</option>
                                    @foreach ($devices as $device)
                                        @if (old('device_id') == $device->id)
                                            <option value="{{ $device->id }}" selected>{{ $device->nama }}</option>
                                        @else
                                            <option value="{{ $device->id }}">{{ $device->nama }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Harga and Jam Mulai -->
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

                    <!-- Row 3: Jam Mulai and Jam Selesai -->
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

                    <!-- Row 4: Total PS and Total FnB -->
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
                            <h6 class="text-primary mb-2">FnB Items (Opsional)</h6>
                            <div id="fnb-container" class="border p-2" style="max-height: 150px; overflow-y: auto;">
                                <!-- FnB items will be added here -->
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm mt-1" onclick="addFnbItem()">+ Tambah FnB</button>
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

        member.style.display = "none";
        nama.style.display = "none";
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
            const jamMainRow = document.querySelector('.row.mb-2:has(#jam_main)');
            const waktuSelesaiRow = document.querySelector('.row.mb-2:has(#waktu_Selesai)');
            const totalPsRow = document.querySelector('.row.mb-2:has(#total_ps)');
            const jamMainInput = document.getElementById('jam_main');
            const bayarBtn = document.getElementById('bayarBtn');
            
            // Show/hide Bayar button based on transaction type
            if (bayarBtn) {
                bayarBtn.style.display = isPrepaid ? 'block' : 'none';
                // Adjust the width of Simpan button when Bayar is hidden
                const simpanBtn = document.querySelector('button[value="simpan"]');
                if (simpanBtn) {
                    simpanBtn.style.flex = isPrepaid ? '1' : '0 0 100%';
                }
            }

            if (isPrepaid) {
                if (jamMainRow) jamMainRow.style.display = 'flex';
                if (waktuSelesaiRow) waktuSelesaiRow.style.display = 'flex';
                if (totalPsRow) totalPsRow.style.display = 'flex';
                jamMainInput.setAttribute('required', 'required');
                updateGrandTotal();
            } else {
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
            const status = document.getElementById('status').value;
            const nama = document.getElementById('nama');
            const member = document.getElementById('member');

            if (status === 'member') {
                member.style.display = "block";
                nama.style.display = "none";
            } else {
                member.style.display = "none";
                nama.style.display = "block";
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
            itemDiv.className = 'fnb-item border p-3 mb-3';
            itemDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <label>Barang</label>
                        <select class="form-control fnb-select" name="fnb_ids[]" onchange="updateFnbPrice(this, ${fnbIndex})" required>
                            <option value="">Pilih Barang</option>
                            ${fnbs.map(fnb => `<option value="${fnb.id}" data-price="${fnb.harga_jual}" data-stock="${fnb.stok}">${fnb.nama} (Stok: ${fnb.stok})</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Qty</label>
                        <input type="number" class="form-control fnb-qty" name="fnbs_qty[]" min="1" onchange="updateFnbTotal()" required>
                    </div>
                    <div class="col-md-3">
                        <label>Harga Jual</label>
                        <input type="number" class="form-control fnb-price" name="fnbs_harga[]" readonly required>
                    </div>
                    <div class="col-md-2">
                        <label>Total</label>
                        <input type="number" class="form-control fnb-total" readonly>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm mt-4" onclick="removeFnbItem(this)">Hapus</button>
                    </div>
                </div>
            `;
            container.appendChild(itemDiv);
            fnbIndex++;
        }

        function updateFnbPrice(select, index) {
            const priceInput = select.closest('.fnb-item').querySelector('.fnb-price');
            const selectedOption = select.options[select.selectedIndex];
            priceInput.value = selectedOption.getAttribute('data-price') || 0;
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
            button.closest('.fnb-item').remove();
            updateFnbTotal();
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
