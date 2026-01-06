@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    Form Edit Perangkat
                </h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <form method="POST" action="/device/{{ $device->id }}" id="device-update-form">
                    @method('put')
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label ">Nama Perangkat</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                            name="nama" required autofocus value="{{ old('nama', $device->nama) }}">
                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label>Jenis Playstation (bisa memilih lebih dari satu)</label>
                        <div class="playstation-selection">
                            @foreach ($playstations as $playstation)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="playstation_ids[]" value="{{ $playstation->id }}" id="playstation_{{ $playstation->id }}"
                                        @if(in_array($playstation->id, old('playstation_ids', $device->playstations->pluck('id')->toArray())))
                                            checked
                                        @endif>
                                    <label class="form-check-label" for="playstation_{{ $playstation->id }}">
                                        {{ $playstation->nama }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Pilih satu atau lebih jenis PlayStation untuk perangkat ini</small>
                        
                        <!-- Quick Actions -->
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPlaystations()">
                                <i class="fas fa-check-square"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllPlaystations()">
                                <i class="fas fa-square"></i> Hapus Pilihan
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function selectAllPlaystations() {
    const checkboxes = document.querySelectorAll('input[name="playstation_ids[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function clearAllPlaystations() {
    const checkboxes = document.querySelectorAll('input[name="playstation_ids[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Auto-update device statuses based on active timers
function updateAllDeviceStatuses() {
    fetch('/device/update-all-statuses', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(`Updated ${data.updated_count} device statuses based on active timers`);
        }
    })
    .catch(error => {
        console.error('Error updating device statuses:', error);
    });
}

// Call the update function every 30 seconds
setInterval(updateAllDeviceStatuses, 30000);

// Also call it immediately when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(updateAllDeviceStatuses, 2000); // Wait 2 seconds after page load
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('device-update-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Form submission logic without status check
        });
    }
});
</script>
@endsection
