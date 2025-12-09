@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Pembayaran Transaksi #{{ $transaction->id_transaksi }}</h1>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Informasi Detail Transaksi</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Informasi Dasar</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>ID Transaksi:</strong></td>
                            <td>{{ $transaction->id_transaksi }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $transaction->nama }}</td>
                        </tr>
                        <tr>
                            <td><strong>No Telf :</strong></td>
                            <td>{{ $transaction->no_telepon }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tipe Transaksi:</strong></td>
                            <td>{{ $transaction->tipe_transaksi === 'prepaid' ? 'Paket' : 'Lost Time' }}</td>
                        </tr>
                       
                        <tr>
                            <td><strong>Tanggal:</strong></td>
                            <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Informasi Perangkat</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama Perangkat:</strong></td>
                            <td>{{ $transaction->device ? $transaction->device->nama : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis Playstation:</strong></td>
                            <td>{{ $transaction->device && $transaction->device->playstation ? $transaction->device->playstation->nama : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h6>Waktu Bermain</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Waktu Mulai:</strong></td>
                            <td>{{ $transaction->waktu_mulai }}</td>
                        </tr>
                        <tr>
                            <td><strong>Waktu Selesai:</strong></td>
                            <td>{{ $transaction->waktu_Selesai ?: '-' }}</td>
                        </tr>
                        @if($transaction->tipe_transaksi === 'postpaid' && $transaction->status_transaksi === 'selesai')
                            @php
                                $startTime = \Carbon\Carbon::parse($transaction->created_at->toDateString() . ' ' . $transaction->waktu_mulai);
                                $endTime = \Carbon\Carbon::parse($transaction->created_at->toDateString() . ' ' . $transaction->waktu_Selesai);
                                $duration = $startTime->diff($endTime);
                                $hours = $duration->h;
                                $minutes = $duration->i;
                                $seconds = $duration->s;
                            @endphp
                            <tr>
                                <td><strong>Durasi Bermain:</strong></td>
                                <td>{{ $hours }} jam {{ $minutes }} menit {{ $seconds }} detik</td>
                            </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Biaya</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Harga per Jam:</strong></td>
                            <td>Rp {{ number_format($transaction->harga, 0, ',', '.') }}</td>
                        </tr>
                        @if($transaction->jam_main)
                            <tr>
                                <td><strong>Lama Main:</strong></td>
                                <td>{{ $transaction->jam_main }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td><strong>Biaya Playstation:</strong></td>
                            <td>Rp {{ number_format($transaction->total - $transaction->getFnbTotalAttribute(), 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Biaya FnB:</strong></td>
                            <td>Rp {{ number_format($transaction->getFnbTotalAttribute(), 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-success">
                            <td class="text-white font-weight-bold"><h5>Total Biaya:</h5></td>
                            <td class="text-white font-weight-bold"><h5>Rp {{ number_format($transaction->total, 0, ',', '.') }}</h5></td>
                        </tr>

                    </table>
                </div>
            </div>

            @if($transaction->transactionFnbs->isNotEmpty())
                <hr>
                <h6>Detail FnB</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama FnB</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->transactionFnbs as $fnbItem)
                                <tr>
                                    <td>{{ $fnbItem->fnb->nama }}</td>
                                    <td>{{ $fnbItem->qty }}</td>
                                    <td>Rp {{ number_format($fnbItem->harga_jual, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($fnbItem->qty * $fnbItem->harga_jual, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Apakah ada pesanan tambahan?</h5>
        </div>
        <div class="card-body">
            <p class="mb-3">Tambahkan makanan atau minuman untuk transaksi ini</p>
            <a href="{{ route('transaction.add-order', $transaction->id_transaksi) }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Pesanan
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Proses Pembayaran</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('transaction.processPayment', ['id' => $transaction->id_transaksi]) }}">
                @csrf
                <div class="form-group mb-3">
                    <label for="payment_method">Metode Pembayaran <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="tunai" required>
                        <label class="form-check-label" for="payment_cash">
                            Pembayaran Tunai
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_ewallet" value="e-wallet" required>
                        <label class="form-check-label" for="payment_ewallet">
                            Pembayaran Menggunakan E-Wallet
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_transfer" value="transfer_bank" required>
                        <label class="form-check-label" for="payment_transfer">
                            Transfer Bank
                        </label>
                    </div>
                    @error('payment_method')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="amount_paid">Jumlah Uang Dibayar</label>
                    <input type="number" class="form-control @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid" required min="{{ $transaction->total }}" oninput="calculateChange()">
                    @error('amount_paid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3" id="change-group">
                    <label for="change">Kembalian</label>
                    <input type="number" class="form-control" id="change" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Bayar</button>
                <a href="{{ route('transaction.index') }}" class="btn btn-secondary">Kembali ke Transaksi</a>
                <a href="{{ route('transaction.index') }}" class="btn btn-danger">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
    const total = {{ $transaction->total }};
    
    function calculateChange() {
        const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
        const change = amountPaid - total;
        document.getElementById('change').value = change > 0 ? change : 0;
    }
    
    // Handle payment method change
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const amountPaidInput = document.getElementById('amount_paid');
        const changeGroup = document.getElementById('change-group');
        
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                if (this.value === 'tunai') {
                    // Cash payment - allow manual input and show change
                    amountPaidInput.readOnly = false;
                    amountPaidInput.value = '';
                    changeGroup.style.display = 'block';
                } else {
                    // E-wallet or Bank Transfer - set to total, no change
                    amountPaidInput.readOnly = true;
                    amountPaidInput.value = total;
                    document.getElementById('change').value = 0;
                    changeGroup.style.display = 'block';
                }
            });
        });
    });
</script>
@endsection
