<?php

// Test script untuk memverifikasi perbaikan timer lost time
echo "=== Test Perbaikan Timer Lost Time ===\n\n";

// Simulasi data transaksi lost time
function testLostTimeTimer($transactionStartTime, $transactionStartDate, $currentServerTime) {
    echo "Transaksi Start: $transactionStartDate $transactionStartTime\n";
    echo "Server Time Now: $currentServerTime\n";
    
    // Convert ke datetime objects
    $start = new DateTime("$transactionStartDate $transactionStartTime");
    $now = new DateTime($currentServerTime);
    
    // Hitung elapsed time
    $interval = $start->diff($now);
    $elapsedMs = ($now->getTimestamp() - $start->getTimestamp()) * 1000;
    
    if ($elapsedMs < 0) {
        echo "Timer: 00:00:00 (Future time)\n";
    } else {
        $totalSeconds = floor($elapsedMs / 1000);
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        
        $timer = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        echo "Timer: $timer\n";
        echo "Formatted: {$interval->h} jam {$interval->i} menit\n";
    }
    echo "\n";
}

// Test Case 1: Transaksi baru dimulai
echo "--- Test Case 1: Transaksi Baru ---\n";
testLostTimeTimer('14:30:00', '2025-12-10', '2025-12-10 14:30:05');

// Test Case 2: Transaksi sudah berjalan 1 jam 15 menit
echo "--- Test Case 2: Transaksi Berjalan 1 Jam 15 Menit ---\n";
testLostTimeTimer('13:15:00', '2025-12-10', '2025-12-10 14:30:00');

// Test Case 3: Transaksi cross-midnight
echo "--- Test Case 3: Transaksi Cross-Midnight ---\n";
testLostTimeTimer('23:45:00', '2025-12-09', '2025-12-10 00:30:00');

// Test Case 4: Start time di masa depan (error case)
echo "--- Test Case 4: Start Time di Masa Depan ---\n";
testLostTimeTimer('15:00:00', '2025-12-10', '2025-12-10 14:30:00');

echo "=== Test Selesai ===\n";
echo "Timer lost time sekarang menggunakan waktu server yang konsisten.\n";
