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

    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-xl font-weight-bold text-primary text-uppercase">Data Perangkat Tersedia</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $countAvailable }}</div>
                    </div>
                    @if(!empty($availableDevices))
                        <div class="mt-2">
                            <small class="text-muted font-weight-bold">Perangkat:</small>
                            <div class="mt-1">
                                @foreach($availableDevices as $device)
                                    <span class="badge badge-success mr-1 mb-1">{{ $device }}</span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <small class="text-muted">Tidak ada perangkat tersedia</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-xsl font-weight-bold text-warning text-uppercase">Data Perangkat Digunakan</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $countInUse }}</div>
                    </div>
                    @if(!empty($inUseDevices))
                        <div class="mt-2">
                            <small class="text-muted font-weight-bold">Perangkat:</small>
                            <div class="mt-1">
                                @foreach($inUseDevices as $device)
                                    <span class="badge badge-warning mr-1 mb-1">{{ $device }}</span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <small class="text-muted">Tidak ada perangkat digunakan</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Data Perangkat
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                </a>

                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('device.create') }}" class="btn btn-primary btn-sm">Tambah Data</a>
                @endif
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="row">
                @foreach ($devices as $device)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">{{ $device->nama }}</h6>
                                @if ($device->status === 'Digunakan' && isset($customers[$device->id]))
                                    <span class="badge 
                                        @if($customers[$device->id]['tipe_transaksi'] === 'prepaid') badge-primary
                                        @elseif($customers[$device->id]['tipe_transaksi'] === 'custom_package') badge-warning
                                        @else badge-secondary
                                        @endif">
                                        @if($customers[$device->id]['tipe_transaksi'] === 'prepaid')
                                            Paket
                                        @elseif($customers[$device->id]['tipe_transaksi'] === 'custom_package')
                                            Custom Paket
                                        @else
                                            Lost Time
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Jenis Playstation:</strong> 
                                    @if($device->playstations->count() > 0)
                                        @foreach($device->playstations as $playstation)
                                            <span class="badge badge-info">{{ $playstation->nama }}</span>
                                        @endforeach
                                    @else
                                        {{ $device->playstation->nama ?? 'Tidak Diketahui' }}
                                    @endif
                                </div>
                                <div class="mb-2" id="status-{{ $device->id }}">
                                    <strong>Status:</strong>
                                    @if ($device->status === 'Tersedia')
                                        <i class="fa fa-circle text-success" aria-hidden="true"></i>
                                        {{ ucfirst($device->status) }}
                                    @else
                                        <i class="fa fa-circle text-danger" aria-hidden="true"></i>
                                        {{ ucfirst($device->status) }}
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <strong>Timer:</strong>
                                    @if ($device->status === 'Digunakan' && isset($customers[$device->id]))
                                        @if (($customers[$device->id]['tipe_transaksi'] === 'prepaid' || $customers[$device->id]['tipe_transaksi'] === 'custom_package') && isset($timers[$device->id]))
                                            <span id="timer-{{ $device->id }}" data-type="{{ $customers[$device->id]['tipe_transaksi'] }}">Memuat...</span>
                                        @elseif($customers[$device->id]['tipe_transaksi'] === 'postpaid' && $customers[$device->id]['status_transaksi'] === 'berjalan')
                                            <span id="timer-{{ $device->id }}" data-type="postpaid"
                                                data-lost-time-start="{{ isset($postpaidTransactions[$device->id]) ? $postpaidTransactions[$device->id]['lost_time_start'] : '' }}">
                                                <span id="elapsed-time-{{ $device->id }}">00:00:00</span>
                                            </span>
                                        @else
                                            -
                                        @endif
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    @if ($device->status === 'Digunakan' && isset($customers[$device->id]))
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#customerModal"
                                            data-id-transaksi="{{ $customers[$device->id]['id_transaksi'] }}"
                                            data-nama="{{ $customers[$device->id]['nama'] }}"
                                            data-nama-perangkat="{{ $customers[$device->id]['nama_perangkat'] }}"
                                            data-jenis-playstation="{{ $customers[$device->id]['jenis_playstation'] }}"
                                            data-jam-main="{{ $customers[$device->id]['jam_main'] }}"
                                            data-waktu-mulai="{{ $customers[$device->id]['waktu_mulai'] }}"
                                            data-waktu-selesai="{{ $customers[$device->id]['waktu_selesai'] }}"
                                            data-total="{{ $customers[$device->id]['total'] }}"
                                            data-tanggal="{{ $customers[$device->id]['tanggal'] }}">
                                            <i class="fas fa-eye"></i> Lihat Pelanggan
                                        </button>
                                    @endif

                                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'kasir')
                                        @if ($device->status === 'Digunakan')
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                onclick="confirmStopDevice({{ $device->id }}, '{{ $device->nama }}')"
                                                title="Hentikan timer dan ubah status menjadi tersedia">
                                                <i class="fas fa-stop"></i> Berhenti
                                            </button>
                                        @endif
                                        @if (isset($customers[$device->id]) && $customers[$device->id]['tipe_transaksi'] === 'postpaid' && $customers[$device->id]['status_transaksi'] === 'berjalan')
                                            <button type="button" class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#finishTransactionModal"
                                                data-action="{{ route('transaction.end', $customers[$device->id]['id_transaksi']) }}"
                                                data-device-id="{{ $device->id }}"
                                                data-device-info="{{ $device->playstation->nama ?? 'Unknown' }} | {{ $device->nama }}">
                                                <i class="fas fa-check"></i> Selesai
                                            </button>
                                        @endif
                                        <a href="/device/{{ $device->id }}/edit" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                        <form action="/device/{{ $device->id }}" method="post" class="d-inline">
                                            @method('delete')
                                            @csrf
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus perangkat ini?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="/booking/{{ $device->id }}" class="btn btn-sm btn-primary">Lihat Jadwal</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $devices->links() }}
        </div>
    </div>

    <!-- Customer Info Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Informasi Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID Transaksi:</strong> <span id="modal-id-transaksi">Tidak tersedia</span></p>
                    <p><strong>Nama Pelanggan:</strong> <span id="modal-nama">Tidak tersedia</span></p>
                    <p><strong>Nama Perangkat:</strong> <span id="modal-nama-perangkat">Tidak tersedia</span></p>
                    <p><strong>Jenis Playstation:</strong> <span id="modal-jenis-playstation">Tidak tersedia</span></p>
                    <p><strong>Jam Main:</strong> <span id="modal-jam-main">Tidak tersedia</span></p>
                    <p><strong>Waktu Mulai:</strong> <span id="modal-waktu-mulai">Tidak tersedia</span></p>
                    <p><strong>Waktu Selesai:</strong> <span id="modal-waktu-selesai">Tidak tersedia</span></p>
                    <p><strong>Total:</strong> <span id="modal-total">Tidak tersedia</span></p>
                    <p><strong>Tanggal:</strong> <span id="modal-tanggal">Tidak tersedia</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stop Device Confirmation Modal -->
    <div class="modal fade" id="stopDeviceModal" tabindex="-1" aria-labelledby="stopDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stopDeviceModalLabel">Konfirmasi Berhenti</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h5>Apakah Anda yakin ingin menghentikan timer perangkat ini?</h5>
                    <p class="mb-0"><strong id="stop-device-name">Nama Perangkat</strong></p>
                    <p class="text-muted">Timer akan dihentikan dan status perangkat akan diubah menjadi "Tersedia"</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="stopDeviceForm" method="POST" action="">
                        @csrf
                        <button type="submit" class="btn btn-warning"><i class="fas fa-stop"></i> Ya, Berhenti</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Finish Transaction Modal -->
    <div class="modal fade" id="finishTransactionModal" tabindex="-1" aria-labelledby="finishTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="finishTransactionModalLabel">Konfirmasi Selesai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h5>Apakah Anda yakin ingin menyelesaikan transaksi ini?</h5>
                    <h2 id="modal-finish-timer" class="text-primary my-3 font-weight-bold">00:00:00</h2>
                    <p class="mb-0 text-muted font-weight-bold" id="modal-finish-device">Jenis PS | Nama Perangkat</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="finishTransactionForm" method="POST" action="">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Ya, Selesai</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        // Function to update prepaid timer
        function updatePrepaidTimer(deviceId, timerData) {
            // Create proper end date with time
            let endTime = new Date(`${timerData.end_date} ${timerData.end_time}`);
            let now = new Date();
            
            if (endTime <= now) {
                let timerTd = document.getElementById('timer-' + deviceId);
                if (timerTd) {
                    timerTd.innerHTML = 'Selesai';
                }
                return;
            }
            
            function tick() {
                let diffMs = endTime.getTime() - new Date().getTime();
                if (diffMs <= 0) {
                    let statusTd = document.getElementById('status-' + deviceId);
                    if (statusTd) {
                        statusTd.innerHTML = '<i class="fa fa-circle text-success" aria-hidden="true"></i> Tersedia';
                    }
                    let timerTd = document.getElementById('timer-' + deviceId);
                    if (timerTd) {
                        timerTd.innerHTML = 'Selesai';
                    }
                    // Update DB via AJAX
                    fetch('/device/' + deviceId + '/update-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({})
                    });
                    return;
                }
                let diffSec = Math.floor(diffMs / 1000);
                let hh = Math.floor(diffSec / 3600).toString().padStart(2, '0');
                let mm = Math.floor((diffSec % 3600) / 60).toString().padStart(2, '0');
                let ss = (diffSec % 60).toString().padStart(2, '0');
                let timerTd = document.getElementById('timer-' + deviceId);
                if (timerTd) {
                    timerTd.innerHTML = `${hh}:${mm}:${ss}`;
                }
                setTimeout(tick, 1000);
            }
            tick();
        }

        // Function to update postpaid timer (elapsed time from 00:00:00)
        function updatePostpaidTimer(deviceId, lostTimeStart) {
            // Parse the lost_time_start timestamp
            const startTimestamp = new Date(lostTimeStart).getTime();
            
            function updateElapsedTime() {
                // Calculate elapsed time from lost_time_start
                const now = Date.now();
                const diffMs = now - startTimestamp;
                
                if (diffMs < 0) {
                    // If the start time is in the future, show 00:00:00
                    document.getElementById(`elapsed-time-${deviceId}`).textContent = '00:00:00';
                } else {
                    // Calculate hours, minutes, seconds
                    const totalSeconds = Math.floor(diffMs / 1000);
                    const hours = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                    const minutes = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                    const seconds = (totalSeconds % 60).toString().padStart(2, '0');
                    
                    document.getElementById(`elapsed-time-${deviceId}`).textContent = `${hours}:${minutes}:${seconds}`;
                }
                
                // Update every second
                setTimeout(updateElapsedTime, 1000);
            }
            
            updateElapsedTime();
        }

        // Initialize timers when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize prepaid and custom package timers
            document.querySelectorAll('[data-type="prepaid"], [data-type="custom_package"]').forEach(timerElement => {
                const deviceId = timerElement.id.replace('timer-', '');
                const timerData = @json($timers);
                
                if (timerData[deviceId]) {
                    updatePrepaidTimer(deviceId, timerData[deviceId]);
                }
            });
            
            // Initialize postpaid timers
            document.querySelectorAll('[data-type="postpaid"]').forEach(timerElement => {
                const deviceId = timerElement.id.replace('timer-', '');
                const lostTimeStart = timerElement.dataset.lostTimeStart;
                
                if (lostTimeStart) {
                    updatePostpaidTimer(deviceId, lostTimeStart);
                }
            });
        });

        // Modal population script
        var customerModal = document.getElementById('customerModal');
        if (customerModal) {
            customerModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var idTransaksi = button.getAttribute('data-id-transaksi');
                var nama = button.getAttribute('data-nama');
                var namaPerangkat = button.getAttribute('data-nama-perangkat');
                var jenisPlaystation = button.getAttribute('data-jenis-playstation');
                var jamMain = button.getAttribute('data-jam-main');
                var waktuMulai = button.getAttribute('data-waktu-mulai');
                var waktuSelesai = button.getAttribute('data-waktu-selesai');
                var total = button.getAttribute('data-total');
                var tanggal = button.getAttribute('data-tanggal');

                document.getElementById('modal-id-transaksi').textContent = idTransaksi || 'Tidak tersedia';
                document.getElementById('modal-nama').textContent = nama || 'Tidak tersedia';
                document.getElementById('modal-nama-perangkat').textContent = namaPerangkat || 'Tidak tersedia';
                document.getElementById('modal-jenis-playstation').textContent = jenisPlaystation || 'Tidak tersedia';
                document.getElementById('modal-jam-main').textContent = jamMain || 'Tidak tersedia';
                document.getElementById('modal-waktu-mulai').textContent = waktuMulai || 'Tidak tersedia';
                document.getElementById('modal-waktu-selesai').textContent = waktuSelesai || 'Tidak tersedia';
                document.getElementById('modal-total').textContent = total || 'Tidak tersedia';
                document.getElementById('modal-tanggal').textContent = tanggal || 'Tidak tersedia';
            });
        }

        // Finish Transaction Modal Script
        var finishTransactionModal = document.getElementById('finishTransactionModal');
        if (finishTransactionModal) {
            finishTransactionModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var actionUrl = button.getAttribute('data-action');
                var deviceId = button.getAttribute('data-device-id');
                var deviceInfo = button.getAttribute('data-device-info');

                // Update Form Action
                document.getElementById('finishTransactionForm').action = actionUrl;

                // Update Device Info
                document.getElementById('modal-finish-device').textContent = deviceInfo;

                // Update Timer Snapshot
                var timerElement = document.getElementById('timer-' + deviceId);
                var timerValue = '00:00:00';
                if (timerElement) {
                    timerValue = timerElement.innerText.trim();
                }
                document.getElementById('modal-finish-timer').textContent = timerValue;
            });
        }

        // Stop Device Confirmation Function
        function confirmStopDevice(deviceId, deviceName) {
            console.log('Stopping device:', deviceId, deviceName);
            
            // Update modal content
            document.getElementById('stop-device-name').textContent = deviceName;
            
            // Update form action
            document.getElementById('stopDeviceForm').action = `/device/${deviceId}/stop`;
            
            // Show modal
            var stopModal = new bootstrap.Modal(document.getElementById('stopDeviceModal'));
            stopModal.show();
        }

        // Handle Stop Device Form Submission
        document.getElementById('stopDeviceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            var form = e.target;
            var submitButton = form.querySelector('button[type="submit"]');
            var originalButtonText = submitButton.innerHTML;
            
            // Disable button and show loading
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            
            // Get CSRF token
            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token meta tag not found');
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                return;
            }
            
            var formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    // Close modal
                    var stopModal = bootstrap.Modal.getInstance(document.getElementById('stopDeviceModal'));
                    if (stopModal) {
                        stopModal.hide();
                    }
                    
                    // Show success message
                    var alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success col-lg-8';
                    alertDiv.role = 'alert';
                    alertDiv.textContent = data.message;
                    
                    // Insert alert at the top
                    var container = document.querySelector('.container-fluid');
                    if (container) {
                        container.insertBefore(alertDiv, container.firstChild);
                    }
                    
                    // Remove alert after 3 seconds
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.parentNode.removeChild(alertDiv);
                        }
                    }, 3000);
                    
                    // Update device status immediately
                    var deviceId = data.device_id;
                    var statusElement = document.getElementById('status-' + deviceId);
                    if (statusElement) {
                        statusElement.innerHTML = '<i class="fa fa-circle text-success" aria-hidden="true"></i> Tersedia';
                    }
                    
                    // Clear timer display
                    var timerElement = document.getElementById('timer-' + deviceId);
                    if (timerElement) {
                        timerElement.textContent = '-';
                    }
                    
                    // Remove stop button
                    var stopButton = document.querySelector(`button[onclick*="confirmStopDevice(${deviceId})"]`);
                    if (stopButton) {
                        stopButton.remove();
                    }
                    
                    // Update device counts in the summary cards
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    var alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger col-lg-8';
                    alertDiv.role = 'alert';
                    alertDiv.textContent = data.message || 'Gagal menghentikan perangkat';
                    
                    // Insert alert at the top
                    var container = document.querySelector('.container-fluid');
                    if (container) {
                        container.insertBefore(alertDiv, container.firstChild);
                    }
                    
                    // Remove alert after 5 seconds
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.parentNode.removeChild(alertDiv);
                        }
                    }, 5000);
                }
            })
            .catch(error => {
                console.error('Error stopping device:', error);
                
                // Show generic error message
                var alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger col-lg-8';
                alertDiv.role = 'alert';
                alertDiv.textContent = 'Terjadi kesalahan saat menghentikan perangkat. Silakan coba lagi.';
                
                // Insert alert at the top of the main content area
                var mainContent = document.querySelector('.container-fluid');
                if (mainContent) {
                    mainContent.insertBefore(alertDiv, mainContent.firstChild);
                } else {
                    // Fallback
                    var contentSection = document.querySelector('section');
                    if (contentSection && contentSection.parentNode) {
                        contentSection.parentNode.insertBefore(alertDiv, contentSection);
                    }
                }
                
                // Remove alert after 5 seconds
                setTimeout(() => {
                    if (alertDiv && alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 5000);
            })
            .finally(() => {
                // Restore button
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            });
        });
    </script>
@endsection
