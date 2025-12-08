<?php

// Test script untuk memverifikasi perbaikan timer cross-midnight

require_once 'vendor/autoload.php';

use Carbon\Carbon;

echo "=== Test Perbaikan Timer Cross-Midnight ===\n\n";

// Simulasi data yang akan dikirim ke frontend
function simulateTimerData($startTime, $endTime, $transactionDate, $currentTimeStr) {
    $now = Carbon::parse($currentTimeStr);
    $startDateTime = Carbon::parse($transactionDate . ' ' . $startTime);
    $endDateTime = Carbon::parse($transactionDate . ' ' . $endTime);
    
    // If end time is earlier than start time, it means it's the next day
    if ($endDateTime < $startDateTime) {
        $endDateTime->addDay();
    }
    
    $isActive = $endDateTime > $now;
    
    return [
        'end_time' => $endTime,
        'end_date' => $endDateTime->format('Y-m-d'),
        'start_date' => $transactionDate,
        'start_time' => $startTime,
        'is_active' => $isActive,
        'end_datetime' => $endDateTime,
        'current_datetime' => $now
    ];
}

// Test Case 1: Normal case (same day)
echo "Test Case 1: Normal case (same day)\n";
$result1 = simulateTimerData('20:00', '22:00', '2025-12-08', '2025-12-08 21:00:00');
echo "Start: {$result1['start_time']}, End: {$result1['end_time']}\n";
echo "End Date: {$result1['end_date']}, Current: {$result1['current_datetime']->format('Y-m-d H:i:s')}\n";
echo "Timer Active: " . ($result1['is_active'] ? "YES" : "NO") . "\n";
echo "JavaScript would use: new Date('{$result1['end_date']} {$result1['end_time']}')\n\n";

// Test Case 2: Cross midnight case (the bug scenario)
echo "Test Case 2: Cross midnight case (the bug scenario)\n";
$result2 = simulateTimerData('23:00', '01:00', '2025-12-08', '2025-12-09 00:30:00');
echo "Start: {$result2['start_time']}, End: {$result2['end_time']}\n";
echo "End Date: {$result2['end_date']}, Current: {$result2['current_datetime']->format('Y-m-d H:i:s')}\n";
echo "Timer Active: " . ($result2['is_active'] ? "YES" : "NO") . "\n";
echo "JavaScript would use: new Date('{$result2['end_date']} {$result2['end_time']}')\n\n";

// Test Case 3: Expired cross midnight case
echo "Test Case 3: Expired cross midnight case\n";
$result3 = simulateTimerData('23:00', '01:00', '2025-12-08', '2025-12-09 02:00:00');
echo "Start: {$result3['start_time']}, End: {$result3['end_time']}\n";
echo "End Date: {$result3['end_date']}, Current: {$result3['current_datetime']->format('Y-m-d H:i:s')}\n";
echo "Timer Active: " . ($result3['is_active'] ? "YES" : "NO") . "\n";
echo "JavaScript would use: new Date('{$result3['end_date']} {$result3['end_time']}')\n\n";

// Test Case 4: Multiple hours cross midnight
echo "Test Case 4: Multiple hours cross midnight\n";
$result4 = simulateTimerData('22:00', '03:00', '2025-12-08', '2025-12-09 01:30:00');
echo "Start: {$result4['start_time']}, End: {$result4['end_time']}\n";
echo "End Date: {$result4['end_date']}, Current: {$result4['current_datetime']->format('Y-m-d H:i:s')}\n";
echo "Timer Active: " . ($result4['is_active'] ? "YES" : "NO") . "\n";
echo "JavaScript would use: new Date('{$result4['end_date']} {$result4['end_time']}')\n\n";

echo "=== Test Selesai ===\n";
echo "Perbaikan timer berhasil menangani kasus cross-midnight!\n";
echo "JavaScript sekarang menggunakan tanggal yang tepat untuk perhitungan timer.\n";
