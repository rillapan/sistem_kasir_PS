@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Jam Kerja</h1>
        <a href="{{ route('work_shifts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Jam Kerja
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($workShifts->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Shift</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Durasi</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workShifts as $workShift)
                        <tr>
                            <td>{{ $workShift->nama_shift }}</td>
                            <td>{{ $workShift->jam_mulai }}</td>
                            <td>{{ $workShift->jam_selesai }}</td>
                            <td>{{ $workShift->getDurationInHours() }} jam</td>
                            <td>{{ $workShift->keterangan ?: '-' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('work_shifts.edit', $workShift->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('work_shifts.destroy', $workShift->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus jam kerja ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-clock fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Belum ada jam kerja</h5>
                <p class="text-gray-500">Tambahkan jam kerja untuk mengatur shift kasir</p>
                <a href="{{ route('work_shifts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Jam Kerja
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
