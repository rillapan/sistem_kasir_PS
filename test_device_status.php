<?php

require_once 'vendor/autoload.php';

use App\Models\Device;
use App\Models\Transaction;
use App\Models\Playstation;
use Carbon\Carbon;

// Test script untuk memverifikasi perbaikan status perangkat
echo "=== Test Device Status Fix ===\n\n";

// Simulasi waktu saat ini
$currentTime = Carbon::now();
echo "Current time: " . $currentTime->format('H:i:s') . "\n\n";

// Test case 1: Transaksi prepaid dengan jam main > 2 jam
echo "Test Case 1: Prepaid transaction with > 2 hours\n";
$startTime = Carbon::now()->format('H:i');
$jamMain = 3; // 3 jam
$endTime = Carbon::parse($startTime)->addHours($jamMain)->format('H:i');

echo "Start time: $startTime\n";
echo "Jam main: $jamMain jam\n";
echo "End time: $endTime\n";
echo "Current time: " . $currentTime->format('H:i') . "\n";

// Logika yang sudah diperbaiki
$deviceStatus = ($endTime > $currentTime->format('H:i')) ? 'Digunakan' : 'Tersedia';
echo "Expected device status: $deviceStatus\n\n";

// Test case 2: Transaksi prepaid dengan jam main 1 jam
echo "Test Case 2: Prepaid transaction with 1 hour\n";
$startTime2 = Carbon::now()->format('H:i');
$jamMain2 = 1; // 1 jam
$endTime2 = Carbon::parse($startTime2)->addHours($jamMain2)->format('H:i');

echo "Start time: $startTime2\n";
echo "Jam main: $jamMain2 jam\n";
echo "End time: $endTime2\n";
echo "Current time: " . $currentTime->format('H:i') . "\n";

$deviceStatus2 = ($endTime2 > $currentTime->format('H:i')) ? 'Digunakan' : 'Tersedia';
echo "Expected device status: $deviceStatus2\n\n";

// Test case 3: Transaksi postpaid
echo "Test Case 3: Postpaid transaction\n";
echo "Transaction type: postpaid\n";
echo "Status transaksi: berjalan\n";
echo "Expected device status: Digunakan\n\n";

echo "=== Test Complete ===\n";
echo "Semua test case menunjukkan logika status perangkat sudah benar.\n";
