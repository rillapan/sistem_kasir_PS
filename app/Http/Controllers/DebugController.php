<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Playstation;
use App\Models\CustomPackage;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function deviceDebug()
    {
        $environment = app()->environment();
        $phpVersion = PHP_VERSION;
        
        // Get all devices
        $allDevices = Device::with('playstation')->get();
        $availableDevices = Device::with('playstation')->where('status', 'Tersedia')->get();
        $playstations = Playstation::all();
        $customPackages = CustomPackage::active()->with(['playstations', 'fnbs'])->get();
        
        return response()->json([
            'environment' => $environment,
            'php_version' => $phpVersion,
            'timestamp' => now()->toISOString(),
            'devices' => [
                'all_count' => $allDevices->count(),
                'available_count' => $availableDevices->count(),
                'all_devices' => $allDevices->toArray(),
                'available_devices' => $availableDevices->toArray()
            ],
            'playstations' => [
                'count' => $playstations->count(),
                'data' => $playstations->toArray()
            ],
            'custom_packages' => [
                'count' => $customPackages->count(),
                'data' => $customPackages->toArray()
            ]
        ]);
    }
}
