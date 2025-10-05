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

    <!-- Summary Cards Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Postpaid Transactions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $postpaidCount }}</div>
                    </div>
                    <div>
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Prepaid Transactions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $prepaidCount }}</div>
                    </div>
                    <div>
                        <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Paid Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $paidCount }}</div>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unpaid Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unpaidCount }}</div>
                    </div>
                    <div>
                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <form method="GET" action="{{ route('transaction.index') }}" class="d-flex mr-2">
                        <select name="type" class="form-control form-control-sm mr-2">
                            <option value="all" {{ $currentType == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="prepaid" {{ $currentType == 'prepaid' ? 'selected' : '' }}>Prepaid</option>
                            <option value="postpaid" {{ $currentType == 'postpaid' ? 'selected' : '' }}>Postpaid</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </form>
                </div>
                
                <div class="d-flex align-items-center">
                    @if (auth()->user()->status === 'admin')
                        <a href="{{ route('transaction.create') }}" class="btn btn-primary btn-sm">Tambah Data</a>
                    @endif
                </div>
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID Transaksi</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Nama Perangkat</th>
                        <th scope="col">Jenis Playstation</th>
                        <th scope="col">Tipe</th>
                        <th scope="col">Jam Main</th>
                        <th scope="col">Waktu Mulai</th>
                        <th scope="col">Waktu Selesai</th>
                        <th scope="col">FnB</th>
                        <th scope="col">Total</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Status Transaksi</th>
                        <th scope="col">Status Pembayaran</th>
                        <th scope="col">Timer</th>

                        @if (auth()->user()->status === 'admin')
                            <th scope="col">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaksi)
                        <tr>
                            <td>{{ $transaksi->id }}</td>
                            <td>{{ $transaksi->nama }}</td>
                            <td>{{ $transaksi->device ? $transaksi->device->nama : 'N/A' }}</td>
                            <td>{{ $transaksi->device && $transaksi->device->playstation ? $transaksi->device->playstation->nama : 'N/A' }}</td>
                            <td>{{ ucfirst($transaksi->tipe_transaksi) }}</td>
                            <td>{{ $transaksi->jam_main ? $transaksi->jam_main . ' Jam' : '-' }}</td>
                            <td>{{ $transaksi->waktu_mulai }}</td>
                            <td>{{ $transaksi->waktu_Selesai ?: '-' }}</td>
                            <td>
                                @if($transaksi->transactionFnbs->isEmpty())
                                    -
                                @else
                                    @foreach($transaksi->transactionFnbs as $fnbItem)
                                        {{ $fnbItem->fnb->nama }} ({{ $fnbItem->qty }} x Rp {{ number_format($fnbItem->harga_jual, 0, ',', '.') }})
                                        @if(!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ 'Rp ' . number_format($transaksi->total, 0, ',', '.') }}</td>
                            <td>{{ $transaksi->created_at }}</td>
                            <td>{{ ucfirst($transaksi->status_transaksi) }}</td>
                            <td>{{ ucfirst($transaksi->payment_status) }}</td>
                            <td>
                                @if($transaksi->tipe_transaksi === 'postpaid' && $transaksi->status_transaksi === 'berjalan')
                                    <div class="timer" data-start="{{ $transaksi->created_at->toDateString() }} {{ $transaksi->waktu_mulai }}" data-transaction-id="{{ $transaksi->id }}">
                                        <span class="timer-display">Lama Main: 00:00:00 (0 jam 0 menit)</span>
                                    </div>
                                @elseif($transaksi->tipe_transaksi === 'postpaid' && $transaksi->status_transaksi === 'selesai')
                                    @php
                                        $startTime = \Carbon\Carbon::parse($transaksi->created_at->toDateString() . ' ' . $transaksi->waktu_mulai);
                                        $endTime = \Carbon\Carbon::parse($transaksi->created_at->toDateString() . ' ' . $transaksi->waktu_Selesai);
                                        $duration = $startTime->diff($endTime);
                                        $hours = $duration->h;
                                        $minutes = $duration->i;
                                        $seconds = $duration->s;
                                    @endphp
                                    Lama Main: {{ str_pad($hours, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($minutes, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }} ({{ $hours }} jam {{ $minutes }} menit)
                                @else
                                    -
                                @endif
                            </td>

                            @if (auth()->user()->status === 'admin' && $transaksi->device)
                                <td>
                                    <div class="btn-group" role="group" aria-label="Action Buttons">
                                        <a href="{{ route('transaction.show', $transaksi->id) }}" class="btn btn-info btn-sm" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($transaksi->payment_status === 'unpaid' && $transaksi->tipe_transaksi === 'prepaid')
                                            <a href="{{ route('transaction.showPayment', $transaksi->id) }}" class="btn btn-success btn-sm" title="Bayar">
                                                <i class="fas fa-credit-card"></i> Bayar
                                            </a>
                                        @endif
                                        <form action="/transaction/{{ $transaksi->id }}/update" method="post" class="d-inline">
                                            @method('put')
                                            @csrf
                                            <input type="hidden" name="device_id" value="{{ $transaksi->device->id }}">
                                            <input type="hidden" name="status" value="digunakan">
                                            <input type="hidden" name="status_transaksi" value="sukses">
                                            {{-- <button class="btn btn-success btn-sm" onclick="return confirm('Are you sure?')" title="Update Status">
                                                <i class="fa fa-check-square fa-lg" aria-hidden="true"></i>
                                            </button> --}}
                                        </form>
                                        @if($transaksi->status_transaksi === 'berjalan' && $transaksi->id)
                                            <form action="{{ route('transaction.end', $transaksi->id) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Apakah Anda yakin ingin mengakhiri transaksi ini?')" title="Selesai">Selesai</button>
                                            </form>
                                        @endif
                                        <form action="/transaction/{{ $transaksi->id }}" method="post" class="d-inline">
                                            @method('delete')
                                            @csrf
                                            {{-- <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" title="Hapus">
                                                <i class="fas fa-trash-alt fa-lg"></i>
                                            </button> --}}
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
    </div>
<style>
    .btn-group .btn {
        margin-right: 4px;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    .timer {
        font-weight: 600;
        color: #333;
    }
    .timer-display {
        font-family: monospace;
    }
    table.table-bordered {
        border: 1px solid #dee2e6;
    }
    table.table-bordered th,
    table.table-bordered td {
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }
</style>
<script>
    function updateTimers() {
        const timers = document.querySelectorAll('.timer');
        timers.forEach(timer => {
            const start = timer.getAttribute('data-start');
            const transactionId = timer.getAttribute('data-transaction-id');
            const displayElement = timer.querySelector('.timer-display');

            if (!start || !displayElement) {
                return;
            }

            const startTime = new Date(start);
            const now = new Date();
            let diff = Math.floor((now - startTime) / 1000); // difference in seconds

            if (diff < 0) {
                diff = 0;
            }

            const hours = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;

            const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            const durationString = `${hours} jam ${minutes} menit`;

            displayElement.textContent = `Lama Main: ${timeString} (${durationString})`;
        });
    }

    // Update timers every second
    setInterval(updateTimers, 1000);

    // Initial update when page loads
    document.addEventListener('DOMContentLoaded', updateTimers);

    // Handle STOP button clicks to stop timer immediately
    document.addEventListener('submit', function(e) {
        if (e.target.matches('form[action*="end"]')) {
            e.preventDefault();

            const form = e.target;
            const transactionId = form.action.split('/').pop();

            // Stop the timer immediately
            const timer = document.querySelector(`.timer[data-transaction-id="${transactionId}"]`);
            if (timer) {
                const displayElement = timer.querySelector('.timer-display');
                if (displayElement) {
                    displayElement.textContent = 'Menghentikan...';
                }
            }

            // Submit the form
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Terjadi kesalahan saat mengakhiri transaksi');
                    // Restart timer if failed
                    updateTimers();
                }
            }).catch(error => {
                alert('Terjadi kesalahan saat mengakhiri transaksi');
                // Restart timer if failed
                updateTimers();
            });
        }
    });
</script>
@endsection
