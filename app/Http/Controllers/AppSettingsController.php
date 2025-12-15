<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionFnb;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppSettingsController extends Controller
{
    /**
     * Tampilkan halaman pengaturan aplikasi (khusus admin)
     */
    public function index()
    {
        return view('settings.index', [
            'title' => 'Pengaturan Aplikasi',
            'active' => 'settings',
            'appName' => 'Sistem Kasir rental playstation',
            'appVersion' => '01',
            'tools' => 'laravel 12, php 82'
        ]);
    }

    /**
     * Reset semua data transaksi yang tersedia
     */
    public function resetTransactions(Request $request)
    {
        $request->validate([
            'confirm_delete' => 'accepted'
        ]);

        DB::transaction(function () {
            // Hapus detail FnB terlebih dahulu untuk menghindari constraint
            TransactionFnb::query()->delete();
            // Hapus semua transaksi
            Transaction::query()->delete();
            // Set semua perangkat menjadi tersedia
            Device::query()->update(['status' => 'Tersedia']);
        });

        return redirect()->route('settings.index')->with('success', 'Seluruh data transaksi berhasil direset.');
    }
}
