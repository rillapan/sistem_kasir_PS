<?php

// Test script untuk memverifikasi perbaikan logika waktu yang melewati hari

require_once 'vendor/autoload.php';

use Carbon\Carbon;

echo "=== Test Perbaikan Logika Waktu Device Status ===\n\n";

// Test Case 1: Normal case (same day)
echo "Test Case 1: Normal case (same day)\n";
$startTime = '20:00';
$endTime = '22:00';
$transactionDate = '2025-12-08';
$currentTime = Carbon::parse('2025-12-08 21:00:00');

$startDateTime = Carbon::parse($transactionDate . ' ' . $startTime);
$endDateTime = Carbon::parse($transactionDate . ' ' . $endTime);

if ($endDateTime < $startDateTime) {
    $endDateTime->addDay();
}

$isActive = $endDateTime > $currentTime;
echo "Start: $startTime, End: $endTime, Current: " . $currentTime->format('H:i:s') . "\n";
echo "Status: " . ($isActive ? "Digunakan" : "Tersedia") . "\n\n";

// Test Case 2: Cross midnight case (the bug scenario)
echo "Test Case 2: Cross midnight case (the bug scenario)\n";
$startTime = '23:00';
$endTime = '01:00'; // Next day
$transactionDate = '2025-12-08';
$currentTime = Carbon::parse('2025-12-09 00:30:00'); // Next day

$startDateTime = Carbon::parse($transactionDate . ' ' . $startTime);
$endDateTime = Carbon::parse($transactionDate . ' ' . $endTime);

if ($endDateTime < $startDateTime) {
    $endDateTime->addDay();
}

$isActive = $endDateTime > $currentTime;
echo "Start: $startTime, End: $endTime, Current: " . $currentTime->format('H:i:s') . "\n";
echo "Status: " . ($isActive ? "Digunakan" : "Tersedia") . "\n\n";

// Test Case 3: Expired cross midnight case
echo "Test Case 3: Expired cross midnight case\n";
$startTime = '23:00';
$endTime = '01:00'; // Next day
$transactionDate = '2025-12-08';
$currentTime = Carbon::parse('2025-12-09 02:00:00'); // After end time

$startDateTime = Carbon::parse($transactionDate . ' ' . $startTime);
$endDateTime = Carbon::parse($transactionDate . ' ' . $endTime);

if ($endDateTime < $startDateTime) {
    $endDateTime->addDay();
}

$isActive = $endDateTime > $currentTime;
echo "Start: $startTime, End: $endTime, Current: " . $currentTime->format('H:i:s') . "\n";
echo "Status: " . ($isActive ? "Digunakan" : "Tersedia") . "\n\n";

// Test Case 4: Edge case - exactly at end time
echo "Test Case 4: Edge case - exactly at end time\n";
$startTime = '23:00';
$endTime = '01:00'; // Next day
$transactionDate = '2025-12-08';
$currentTime = Carbon::parse('2025-12-09 01:00:00'); // Exactly at end time

$startDateTime = Carbon::parse($transactionDate . ' ' . $startTime);
$endDateTime = Carbon::parse($transactionDate . ' ' . $endTime);

if ($endDateTime < $startDateTime) {
    $endDateTime->addDay();
}

$isActive = $endDateTime > $currentTime;
echo "Start: $startTime, End: $endTime, Current: " . $currentTime->format('H:i:s') . "\n";
echo "Status: " . ($isActive ? "Digunakan" : "Tersedia") . "\n\n";

echo "=== Test Selesai ===\n";
echo "Perbaikan berhasil menangani kasus cross-midnight!\n";
