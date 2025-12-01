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

    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Data Perangkat Tersedia</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $countAvailable }}</div>
                    </div>
                    <div>
                        <i class="fas fa-tv fa-3x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Data Perangkat Digunakan</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $countInUse }}</div>
                    </div>
                    <div>
                        <i class="fas fa-tv fa-3x text-gray-300"></i>
                    </div>
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

                @if (auth()->user()->status === 'admin')
                    <a href="{{ route('device.create') }}" class="btn btn-primary btn-sm">Tambah Data</a>
                @endif
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Perangkat</th>
                        <th scope="col">Jenis Playstation</th>
                        <th scope="col">Status</th>
                        <th scope="col">Timer</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($devices as $device)
                        <tr>
                            <th scope="row">
                                {{ ($devices->currentpage() - 1) * $devices->perpage() + $loop->index + 1 }}</th>
                            <td @if($device->status === 'Digunakan') class="fw-bold text-warning" @endif>{{ $device->nama }}</td>
                            <td>{{ $device->playstation->nama ?? 'Tidak Diketahui' }}</td>
        <td id="status-{{ $device->id }}">
            @if ($device->status === 'Tersedia')
                <i class="fa fa-circle text-success" aria-hidden="true"></i>
                {{ ucfirst($device->status) }}
            @else
                <i class="fa fa-circle text-danger" aria-hidden="true"></i>
                {{ ucfirst($device->status) }}
            @endif
        </td>
        @if ($device->status === 'Digunakan' && isset($customers[$device->id]))
            @if ($customers[$device->id]['tipe_transaksi'] === 'prepaid' && isset($timers[$device->id]))
                <td id="timer-{{ $device->id }}" data-type="prepaid">Memuat...</td>
            @elseif($customers[$device->id]['tipe_transaksi'] === 'postpaid' && $customers[$device->id]['status_transaksi'] === 'berjalan')
                <td id="timer-{{ $device->id }}" data-type="postpaid" 
                    data-start-time="{{ $customers[$device->id]['waktu_mulai'] }}"
                    data-start-date="{{ $customers[$device->id]['tanggal'] }}">
                    <span id="elapsed-time-{{ $device->id }}">00:00:00</span>
                </td>
            @else
                <td>-</td>
            @endif
        @else
            <td>-</td>
        @endif
        <td>
            @if ($device->status === 'Digunakan' && isset($customers[$device->id]))
                <button type="button" class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#customerModal"
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

                            @if (auth()->user()->status === 'admin')
                                @if (isset($customers[$device->id]) && $customers[$device->id]['tipe_transaksi'] === 'postpaid' && $customers[$device->id]['status_transaksi'] === 'berjalan')
                                    <form action="{{ route('transaction.end', $customers[$device->id]['id_transaksi']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan transaksi ini?')">
                                            <i class="fas fa-check"></i> Selesai
                                        </button>
                                    </form>
                                @endif
                                <a href="/device/{{ $device->id }}/edit" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form action="/device/{{ $device->id }}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus perangkat ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                </td>
                            @else
                                <a href="/booking/{{ $device->id }}" class="btn btn-sm btn-primary">Lihat Jadwal</a>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
    </div>
    <script>
        // Function to update prepaid timer
        function updatePrepaidTimer(deviceId, endTimeStr) {
            let [h, m, s] = endTimeStr.split(':').map(Number);
            let endTime = new Date();
            endTime.setHours(h, m, s, 0);
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

        // Function to update postpaid timer (elapsed time)
        function updatePostpaidTimer(deviceId, startDate, startTime) {
            const startDateTime = new Date(`${startDate} ${startTime}`);
            
            function updateElapsedTime() {
                const now = new Date();
                const diffMs = now - startDateTime;
                
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
                
                requestAnimationFrame(updateElapsedTime);
            }
            
            updateElapsedTime();
        }

        // Initialize timers when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize prepaid timers
            @foreach($timers as $deviceId => $endTime)
                updatePrepaidTimer({{ $deviceId }}, '{{ $endTime }}');
            @endforeach
            
            // Initialize postpaid timers
            document.querySelectorAll('[data-type="postpaid"]').forEach(timerElement => {
                const deviceId = timerElement.id.replace('timer-', '');
                const startTime = timerElement.dataset.startTime;
                const startDate = timerElement.dataset.startDate;
                
                if (startTime && startDate) {
                    updatePostpaidTimer(deviceId, startDate, startTime);
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
    </script>
@endsection
