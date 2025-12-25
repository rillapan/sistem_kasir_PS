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
                    <div class="mb-3">
                        <label for="status">Status Perangkat</label>
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-control" id="status" name="status">
                                <option value="tersedia" {{ old('status', $device->status) === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="digunakan" {{ old('status', $device->status) === 'digunakan' ? 'selected' : '' }}>Digunakan</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="toggleDeviceStatus()">
                                <i class="fas fa-exchange-alt"></i> Toggle Status
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
let currentServerStatus = "{{ $device->status }}";

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

function toggleDeviceStatus() {
    console.log('Toggle device status function called');
    
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        const currentStatus = statusSelect.value;
        const newStatus = currentStatus === 'tersedia' ? 'digunakan' : 'tersedia';
        const deviceId = {{ $device->id }};
        
        console.log('Current status:', currentStatus);
        console.log('New status:', newStatus);
        console.log('Device ID:', deviceId);
        
        // Check if changing from 'digunakan' to 'tersedia' and show confirmation
        if (currentStatus === 'digunakan' && newStatus === 'tersedia') {
            // Use simple confirm as fallback
            const confirmMessage = "Anda yakin ingin mengubah status perangkat ini dari digunakan [timer berjalan] ke 'tersedia', jika ya maka timer akan hilang dan perangkat menjadi tersedia";
            
            if (!confirm(confirmMessage)) {
                console.log('User cancelled the status change');
                return; // User cancelled the operation
            }
            console.log('User confirmed the status change');
        }
        
        // Visual feedback
        const toggleBtn = event.target.closest('button');
        const originalHtml = toggleBtn.innerHTML;
        toggleBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengubah...';
        toggleBtn.disabled = true;
        
        // Proceed with the status change
        executeStatusChange(deviceId, newStatus, toggleBtn, originalHtml);
    } else {
        console.error('Status select element not found!');
    }
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
            
            // Update the status dropdown if this device was updated
            const currentDeviceId = {{ $device->id }};
            const statusSelect = document.getElementById('status');
            if (statusSelect) {
                // Refresh current device status from server
                fetch(`/device/${currentDeviceId}`)
                    .then(response => response.json())
                    .then(deviceData => {
                        if (deviceData.status) {
                            statusSelect.value = deviceData.status;
                        }
                    })
                    .catch(error => console.error('Error fetching device status:', error));
            }
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

function executeStatusChange(deviceId, newStatus, toggleBtn, originalHtml) {
    console.log('Executing status change:', { deviceId, newStatus });
    
    // Check CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    console.log('CSRF Token found:', csrfToken ? csrfToken.getAttribute('content') : 'NOT FOUND');
    
    // Determine URL and Body based on newStatus
    let url = `/device/${deviceId}/update-status`;
    let body = JSON.stringify({ status: newStatus });

    if (newStatus === 'tersedia') {
        url = `/device/${deviceId}/force-stop`;
        // force-stop route uses updateStatusAjax which doesn't require a body for status
        body = JSON.stringify({}); 
    }

    // Make AJAX call to update device status
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        },
        body: body
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            const statusSelect = document.getElementById('status');
            statusSelect.value = newStatus;
            currentServerStatus = newStatus;
            toggleBtn.innerHTML = '<i class="fas fa-exchange-alt"></i> Toggle Status';
            toggleBtn.disabled = false;
            
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show mt-2';
            alertDiv.innerHTML = `
                Status berhasil diubah ke <strong>${newStatus === 'tersedia' ? 'Tersedia' : 'Digunakan'}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            statusSelect.closest('.mb-3').after(alertDiv);
            
            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        } else {
            throw new Error(data.message || 'Gagal mengubah status');
        }
    })
    .catch(error => {
        console.error('Error details:', error);
        
        toggleBtn.innerHTML = originalHtml;
        toggleBtn.disabled = false;
        
        // Show error message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-2';
        alertDiv.innerHTML = `
            Gagal mengubah status: ${error.message || 'Unknown error'}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const statusSelect = document.getElementById('status');
        statusSelect.closest('.mb-3').after(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('device-update-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const statusSelect = document.getElementById('status');
            const selectedStatus = statusSelect.value;
            
            // Check if changing from 'digunakan' to 'tersedia'
            // We use currentServerStatus to know the actual state in DB (or last validated state)
            if (currentServerStatus === 'digunakan' && selectedStatus === 'tersedia') {
                 const confirmMessage = "Anda yakin ingin mengubah status perangkat ini dari digunakan [timer berjalan] ke 'tersedia', jika ya maka timer akan hilang dan perangkat menjadi tersedia";
                 
                 if (!confirm(confirmMessage)) {
                     e.preventDefault(); // Stop submission
                 }
            }
        });
    }
});
</script>
@endsection
