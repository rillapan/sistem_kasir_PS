@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Transaksi #{{ $transaction->id_transaksi }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('transaction.update', $transaction->id_transaksi) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="tipe_transaksi">Tipe Transaksi</label>
                            <select class="form-control" id="tipe_transaksi" name="tipe_transaksi" required>
                                <option value="prepaid" {{ $transaction->tipe_transaksi === 'prepaid' ? 'selected' : '' }}>Paket</option>
                                <option value="postpaid" {{ $transaction->tipe_transaksi === 'postpaid' ? 'selected' : '' }}>Lost Time</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="device_id">Perangkat</label>
                            <select class="form-control" id="device_id" name="device_id" required>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}" 
                                        {{ $transaction->device_id == $device->id ? 'selected' : '' }}>
                                        {{ $device->nama }} ({{ $device->playstation->nama }})
                                        @if($device->status !== 'Tersedia' && $transaction->device_id != $device->id)
                                            - {{ ucfirst($device->status) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih perangkat yang ingin digunakan</small>
                        </div>

                        <div id="jam_main_container" style="display: {{ $transaction->tipe_transaksi === 'prepaid' ? 'block' : 'none' }}">
                            <div class="form-group">
                                <label for="jam_main">Jam Main</label>
                                <select class="form-control" id="jam_main" name="jam_main">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $transaction->jam_main == $i ? 'selected' : '' }}>{{ $i }} Jam</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('transaction.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('tipe_transaksi').addEventListener('change', function() {
    const jamMainContainer = document.getElementById('jam_main_container');
    if (this.value === 'prepaid') {
        jamMainContainer.style.display = 'block';
        document.getElementById('jam_main').setAttribute('required', 'required');
    } else {
        jamMainContainer.style.display = 'none';
        document.getElementById('jam_main').removeAttribute('required');
    }
});
</script>
@endsection
