@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Transaksi #{{ $transaction->id_transaksi }}</h5>
                    <small class="text-muted">Hanya transaksi yang belum dibayar yang dapat diedit</small>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error') || session('gagal'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') ?? session('gagal') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('transaction.update', $transaction->id_transaksi) }}" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nama" class="form-label">Nama Pelanggan</label>
                                    <input type="text" class="form-control" id="nama" value="{{ $transaction->nama }}" disabled>
                                    <small class="text-muted">Nama pelanggan tidak dapat diubah</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                                    <input type="text" class="form-control" id="waktu_mulai" value="{{ $transaction->waktu_mulai }}" disabled>
                                    <small class="text-muted">Waktu mulai tidak dapat diubah</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tipe_transaksi" class="form-label">Tipe Transaksi <span class="text-danger">*</span></label>
                            <select class="form-control" id="tipe_transaksi" name="tipe_transaksi" required>
                                <option value="postpaid" {{ $transaction->tipe_transaksi === 'postpaid' ? 'selected' : '' }}>Lost Time</option>
                                <option value="prepaid" {{ $transaction->tipe_transaksi === 'prepaid' ? 'selected' : '' }}>Paket</option>
                                <option value="custom_package" {{ $transaction->tipe_transaksi === 'custom_package' ? 'selected' : '' }}>Custom Paket</option>
                            </select>
                            <small class="text-muted">Anda dapat mengubah tipe transaksi. Timer akan tetap berjalan.</small>
                        </div>

                        <!-- Timer Preview Section -->
                        <div class="alert alert-info" id="timer_preview_section">
                            <h6 class="mb-3"><i class="fas fa-clock"></i> Preview Timer</h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-2">Timer Saat Ini</h6>
                                            <div class="timer-display-large" id="current_timer">
                                                <span class="timer-value">00:00:00</span>
                                            </div>
                                            <small class="text-muted" id="current_timer_type">
                                                {{ $transaction->tipe_transaksi === 'postpaid' ? 'Lost Time (Count Up)' : 'Paket (Countdown)' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card bg-success text-white" id="new_timer_card">
                                        <div class="card-body">
                                            <h6 class="mb-2">Timer Setelah Edit</h6>
                                            <div class="timer-display-large" id="new_timer">
                                                <span class="timer-value">00:00:00</span>
                                            </div>
                                            <small id="new_timer_type">-</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3" id="timer_explanation">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    <span id="explanation_text">Timer akan disesuaikan berdasarkan waktu yang sudah terpakai.</span>
                                </small>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="device_id" class="form-label">Perangkat <span class="text-danger">*</span></label>
                            <select class="form-control" id="device_id" name="device_id" required>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}" 
                                        {{ $transaction->device_id == $device->id ? 'selected' : '' }}
                                        data-harga="{{ $device->playstation->harga ?? 0 }}"
                                        data-playstation-id="{{ $device->playstation_id }}">
                                        {{ $device->nama }} - {{ $device->playstation->nama ?? 'N/A' }}
                                        @if($transaction->device_id == $device->id)
                                            (Sedang Digunakan)
                                        @else
                                            (Tersedia)
                                        @endif
                                        - Rp {{ number_format($device->playstation->harga ?? 0, 0, ',', '.') }}/jam
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya menampilkan perangkat yang tersedia. Anda dapat mengubah perangkat dan timer akan tetap berjalan.</small>
                        </div>

                        <!-- Custom Package Selection (only shown for custom_package type) -->
                        <div id="custom_package_container" style="display: {{ $transaction->tipe_transaksi === 'custom_package' ? 'block' : 'none' }}">
                            <div class="form-group mb-3">
                                <label for="custom_package_id" class="form-label">Pilih Custom Paket <span class="text-danger">*</span></label>
                                <select class="form-control" id="custom_package_id" name="custom_package_id">
                                    <option value="">-- Pilih Custom Paket --</option>
                                    @foreach($customPackages as $package)
                                        <option value="{{ $package->id }}" 
                                            {{ $transaction->custom_package_id == $package->id ? 'selected' : '' }}
                                            data-harga="{{ $package->harga_total }}"
                                            data-lama-main="{{ $package->playstations->sum('pivot.lama_main') }}"
                                            data-playstation-ids="{{ $package->playstations->pluck('id')->implode(',') }}">
                                            {{ $package->nama_paket }} - Rp {{ number_format($package->harga_total, 0, ',', '.') }}
                                            ({{ $package->playstations->sum('pivot.lama_main') }} menit)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Jam Main (only shown for prepaid type) -->
                        <div id="jam_main_container" style="display: {{ $transaction->tipe_transaksi === 'prepaid' ? 'block' : 'none' }}">
                            <div class="form-group mb-3">
                                <label for="jam_main" class="form-label">Jam Main <span class="text-danger">*</span></label>
                                <select class="form-control" id="jam_main" name="jam_main">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $transaction->jam_main == $i ? 'selected' : '' }}>
                                            {{ $i }} Jam
                                        </option>
                                    @endfor
                                </select>
                                <small class="text-muted">Mengubah jam main akan menyesuaikan harga secara otomatis</small>
                            </div>

                            <!-- Price Preview for Prepaid -->
                            <div class="alert alert-info" id="price_preview">
                                <strong>Estimasi Harga PS:</strong> <span id="estimated_price">Rp 0</span>
                            </div>
                        </div>

                        <!-- Info for Lost Time -->
                        <div id="lost_time_info" style="display: {{ $transaction->tipe_transaksi === 'postpaid' ? 'block' : 'none' }}">
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Lost Time:</strong> Harga akan dihitung saat transaksi selesai berdasarkan durasi bermain.
                                Timer tetap berjalan dari waktu mulai awal.
                            </div>
                        </div>

                        <hr>

                        <div class="alert alert-primary">
                            <h6 class="mb-2"><i class="fas fa-utensils"></i> Informasi FnB</h6>
                            @if($transaction->transactionFnbs->isEmpty())
                                <p class="mb-0">Tidak ada FnB dalam transaksi ini</p>
                            @else
                                <ul class="mb-0">
                                    @foreach($transaction->transactionFnbs as $fnbItem)
                                        <li>{{ $fnbItem->fnb->nama }} - {{ $fnbItem->qty }} x Rp {{ number_format($fnbItem->harga_jual, 0, ',', '.') }}</li>
                                    @endforeach
                                </ul>
                                <p class="mb-0 mt-2"><strong>Total FnB:</strong> Rp {{ number_format($transaction->getFnbTotalAttribute(), 0, ',', '.') }}</p>
                            @endif
                            <small class="text-muted d-block mt-2">Untuk mengubah FnB, gunakan fitur "Tambah Pesanan" di halaman transaksi</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('transaction.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeTransaksiSelect = document.getElementById('tipe_transaksi');
    const jamMainContainer = document.getElementById('jam_main_container');
    const jamMainSelect = document.getElementById('jam_main');
    const deviceSelect = document.getElementById('device_id');
    const customPackageContainer = document.getElementById('custom_package_container');
    const customPackageSelect = document.getElementById('custom_package_id');
    const lostTimeInfo = document.getElementById('lost_time_info');
    const pricePreview = document.getElementById('price_preview');
    const estimatedPrice = document.getElementById('estimated_price');

    // Timer data from server
    const waktuMulai = '{{ $transaction->waktu_mulai }}';
    const createdAt = '{{ $transaction->created_at->toDateString() }}';
    const currentTipe = '{{ $transaction->tipe_transaksi }}';
    const currentJamMain = {{ $transaction->jam_main ?? 0 }};
    
    // Calculate start time
    const startTime = new Date(createdAt + ' ' + waktuMulai);

    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    function updateTimers() {
        const now = new Date();
        const elapsedSeconds = Math.floor((now - startTime) / 1000);
        const elapsedMinutes = Math.floor(elapsedSeconds / 60);
        
        // Update current timer
        const currentTimerEl = document.getElementById('current_timer').querySelector('.timer-value');
        const currentTimerTypeEl = document.getElementById('current_timer_type');
        
        if (currentTipe === 'postpaid') {
            // Lost Time - Count Up
            currentTimerEl.textContent = formatTime(elapsedSeconds);
            currentTimerTypeEl.textContent = 'Lost Time (Count Up ⏱️)';
        } else if (currentTipe === 'prepaid') {
            // Paket - Countdown
            const totalSeconds = currentJamMain * 3600;
            const remainingSeconds = Math.max(0, totalSeconds - elapsedSeconds);
            currentTimerEl.textContent = formatTime(remainingSeconds);
            currentTimerTypeEl.textContent = 'Paket (Countdown ⏳)';
        } else {
            // Custom Package
            const totalSeconds = currentJamMain * 60; // jam_main is in minutes for custom package
            const remainingSeconds = Math.max(0, totalSeconds - elapsedSeconds);
            currentTimerEl.textContent = formatTime(remainingSeconds);
            currentTimerTypeEl.textContent = 'Custom Paket (Countdown ⏳)';
        }

        // Update new timer preview
        updateNewTimerPreview(elapsedSeconds, elapsedMinutes);
    }

    function updateNewTimerPreview(elapsedSeconds, elapsedMinutes) {
        const selectedTipe = tipeTransaksiSelect.value;
        const newTimerEl = document.getElementById('new_timer').querySelector('.timer-value');
        const newTimerTypeEl = document.getElementById('new_timer_type');
        const explanationEl = document.getElementById('explanation_text');
        const newTimerCard = document.getElementById('new_timer_card');

        // Check if type is changing
        if (selectedTipe === currentTipe && selectedTipe !== 'prepaid') {
            // No change in type (except prepaid which might change jam_main)
            newTimerCard.classList.remove('bg-success', 'bg-warning', 'bg-info');
            newTimerCard.classList.add('bg-secondary');
            newTimerEl.textContent = 'Tidak ada perubahan';
            newTimerTypeEl.textContent = 'Tipe transaksi sama';
            explanationEl.textContent = 'Pilih tipe transaksi berbeda untuk melihat perubahan timer.';
            return;
        }

        newTimerCard.classList.remove('bg-secondary');
        
        if (selectedTipe === 'postpaid') {
            // Changing to Lost Time - Count Up
            newTimerCard.classList.remove('bg-warning', 'bg-info');
            newTimerCard.classList.add('bg-success');
            newTimerEl.textContent = formatTime(elapsedSeconds);
            newTimerTypeEl.textContent = 'Lost Time (Count Up ⏱️)';
            
            const hours = Math.floor(elapsedMinutes / 60);
            const mins = elapsedMinutes % 60;
            explanationEl.innerHTML = `<strong>Lost Time → Count Up:</strong> Timer melanjutkan dari ${hours} jam ${mins} menit yang sudah terpakai. Harga dihitung saat selesai.`;
            
        } else if (selectedTipe === 'prepaid') {
            // Changing to Paket - Countdown
            const jamMain = parseInt(jamMainSelect.value) || 1;
            const totalSeconds = jamMain * 3600;
            const remainingSeconds = Math.max(0, totalSeconds - elapsedSeconds);
            
            newTimerCard.classList.remove('bg-success', 'bg-info');
            if (remainingSeconds === 0) {
                newTimerCard.classList.add('bg-danger');
            } else {
                newTimerCard.classList.add('bg-warning');
            }
            
            newTimerEl.textContent = formatTime(remainingSeconds);
            newTimerTypeEl.textContent = 'Paket (Countdown ⏳)';
            
            const hours = Math.floor(elapsedMinutes / 60);
            const mins = elapsedMinutes % 60;
            const remHours = Math.floor(remainingSeconds / 3600);
            const remMins = Math.floor((remainingSeconds % 3600) / 60);
            
            if (remainingSeconds === 0) {
                explanationEl.innerHTML = `<strong class="text-danger">⚠️ Overtime!</strong> Waktu sudah terpakai ${hours} jam ${mins} menit, melebihi paket ${jamMain} jam. Timer akan menunjukkan 00:00:00.`;
            } else {
                explanationEl.innerHTML = `<strong>Paket → Countdown:</strong> Dari ${jamMain} jam, sudah terpakai ${hours} jam ${mins} menit. Sisa waktu: ${remHours} jam ${remMins} menit.`;
            }
            
        } else if (selectedTipe === 'custom_package') {
            // Changing to Custom Package
            const selectedPackage = customPackageSelect.options[customPackageSelect.selectedIndex];
            if (!selectedPackage.value) {
                newTimerEl.textContent = 'Pilih paket';
                newTimerTypeEl.textContent = 'Custom Paket';
                explanationEl.textContent = 'Pilih custom paket untuk melihat preview timer.';
                return;
            }
            
            const lamaMain = parseInt(selectedPackage.getAttribute('data-lama-main')) || 0; // in minutes
            const totalSeconds = lamaMain * 60;
            const remainingSeconds = Math.max(0, totalSeconds - elapsedSeconds);
            
            newTimerCard.classList.remove('bg-success', 'bg-warning');
            newTimerCard.classList.add('bg-info');
            newTimerEl.textContent = formatTime(remainingSeconds);
            newTimerTypeEl.textContent = 'Custom Paket (Countdown ⏳)';
            
            const remMins = Math.floor(remainingSeconds / 60);
            explanationEl.innerHTML = `<strong>Custom Paket:</strong> Total ${lamaMain} menit, sudah terpakai ${elapsedMinutes} menit. Sisa: ${remMins} menit.`;
        }
    }

    function updateFormDisplay() {
        const tipeTransaksi = tipeTransaksiSelect.value;
        
        // Hide all conditional sections first
        jamMainContainer.style.display = 'none';
        customPackageContainer.style.display = 'none';
        lostTimeInfo.style.display = 'none';
        pricePreview.style.display = 'none';
        
        if (tipeTransaksi === 'prepaid') {
            jamMainContainer.style.display = 'block';
            jamMainSelect.setAttribute('required', 'required');
            customPackageSelect.removeAttribute('required');
            updatePricePreview();
        } else if (tipeTransaksi === 'postpaid') {
            lostTimeInfo.style.display = 'block';
            jamMainSelect.removeAttribute('required');
            customPackageSelect.removeAttribute('required');
        } else if (tipeTransaksi === 'custom_package') {
            customPackageContainer.style.display = 'block';
            customPackageSelect.setAttribute('required', 'required');
            jamMainSelect.removeAttribute('required');
        }
        
        // Update timer preview
        updateTimers();
    }

    function updatePricePreview() {
        const selectedDevice = deviceSelect.options[deviceSelect.selectedIndex];
        const hargaPerJam = parseInt(selectedDevice.getAttribute('data-harga')) || 0;
        const jamMain = parseInt(jamMainSelect.value) || 1;
        const totalPs = hargaPerJam * jamMain;
        
        estimatedPrice.textContent = 'Rp ' + totalPs.toLocaleString('id-ID');
        pricePreview.style.display = 'block';
        
        // Update timer preview when jam_main changes
        updateTimers();
    }

    function validateCustomPackageDevice() {
        const tipeTransaksi = tipeTransaksiSelect.value;
        if (tipeTransaksi !== 'custom_package') return true;

        const selectedPackage = customPackageSelect.options[customPackageSelect.selectedIndex];
        const selectedDevice = deviceSelect.options[deviceSelect.selectedIndex];
        
        if (!selectedPackage.value) return true; // No package selected yet
        
        const packagePlaystationIds = selectedPackage.getAttribute('data-playstation-ids').split(',');
        const devicePlaystationId = selectedDevice.getAttribute('data-playstation-id');
        
        if (!packagePlaystationIds.includes(devicePlaystationId)) {
            alert('Perangkat yang dipilih tidak sesuai dengan paket custom yang dipilih!');
            return false;
        }
        
        return true;
    }

    // Event listeners
    tipeTransaksiSelect.addEventListener('change', updateFormDisplay);
    jamMainSelect.addEventListener('change', updatePricePreview);
    deviceSelect.addEventListener('change', function() {
        updatePricePreview();
        validateCustomPackageDevice();
    });
    customPackageSelect.addEventListener('change', function() {
        validateCustomPackageDevice();
        updateTimers();
    });

    // Form submission validation
    document.getElementById('editForm').addEventListener('submit', function(e) {
        if (!validateCustomPackageDevice()) {
            e.preventDefault();
            return false;
        }
    });

    // Initial display
    updateFormDisplay();
    
    // Update timers every second
    setInterval(updateTimers, 1000);
});
</script>

<style>
.form-label {
    font-weight: 600;
}
.text-danger {
    color: #dc3545;
}
.timer-display-large {
    text-align: center;
    padding: 15px 0;
}
.timer-display-large .timer-value {
    font-size: 2.5rem;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    letter-spacing: 3px;
}
#timer_preview_section .card {
    transition: all 0.3s ease;
    min-height: 150px;
}
#timer_preview_section .card-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
#new_timer_card.bg-danger .timer-value {
    animation: pulse 1s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
</style>
@endsection
