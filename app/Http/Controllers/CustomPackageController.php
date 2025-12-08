<?php

namespace App\Http\Controllers;

use App\Models\CustomPackage;
use App\Models\Device;
use App\Models\Fnb;
use Illuminate\Http\Request;

class CustomPackageController extends Controller
{
    public function index()
    {
        $packages = CustomPackage::with(['devices', 'fnbs'])->get();
        return view('custom-package.index', [
            'title' => 'Custom Paket',
            'active' => 'custom-package',
            'packages' => $packages
        ]);
    }

    public function create()
    {
        $devices = Device::with('playstation')->get();
        $fnbs = Fnb::all();
        return view('custom-package.create', [
            'title' => 'Tambah Custom Paket',
            'active' => 'custom-package',
            'devices' => $devices,
            'fnbs' => $fnbs
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga_total' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'devices' => 'required|array|min:1',
            'devices.*.id' => 'required|exists:devices,id',
            'devices.*.lama_main' => 'required|integer|min:1',
            'fnbs' => 'array',
            'fnbs.*.id' => 'exists:fnbs,id',
            'fnbs.*.quantity' => 'integer|min:1',
        ]);

        $package = CustomPackage::create([
            'nama_paket' => $request->nama_paket,
            'harga_total' => $request->harga_total,
            'deskripsi' => $request->deskripsi,
            'is_active' => true,
        ]);

        // Attach devices
        if ($request->has('devices')) {
            foreach ($request->devices as $device) {
                $package->devices()->attach($device['id'], [
                    'lama_main' => $device['lama_main']
                ]);
            }
        }

        // Attach F&B items
        if ($request->has('fnbs')) {
            foreach ($request->fnbs as $fnb) {
                $package->fnbs()->attach($fnb['id'], [
                    'quantity' => $fnb['quantity'] ?? 1
                ]);
            }
        }

        return redirect()->route('custom-package.index')
            ->with('success', 'Paket kustom berhasil dibuat!');
    }

    public function show($id)
    {
        $package = CustomPackage::with(['devices.playstation', 'fnbs'])->findOrFail($id);
        
        // Check if this is an API request
        if (request()->expectsJson()) {
            return response()->json($package);
        }
        
        return view('custom-package.show', [
            'title' => 'Detail Custom Paket',
            'active' => 'custom-package',
            'package' => $package
        ]);
    }

    public function edit($id)
    {
        $package = CustomPackage::with(['devices', 'fnbs'])->findOrFail($id);
        $devices = Device::with('playstation')->get();
        $fnbs = Fnb::all();
        return view('custom-package.edit', [
            'title' => 'Edit Custom Paket',
            'active' => 'custom-package',
            'package' => $package,
            'devices' => $devices,
            'fnbs' => $fnbs
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga_total' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'devices' => 'required|array|min:1',
            'devices.*.id' => 'required|exists:devices,id',
            'devices.*.lama_main' => 'required|integer|min:1',
            'fnbs' => 'array',
            'fnbs.*.id' => 'exists:fnbs,id',
            'fnbs.*.quantity' => 'integer|min:1',
        ]);

        $package = CustomPackage::findOrFail($id);
        $package->update([
            'nama_paket' => $request->nama_paket,
            'harga_total' => $request->harga_total,
            'deskripsi' => $request->deskripsi,
        ]);

        // Sync devices
        $deviceData = [];
        foreach ($request->devices as $device) {
            $deviceData[$device['id']] = ['lama_main' => $device['lama_main']];
        }
        $package->devices()->sync($deviceData);

        // Sync F&B items
        $fnbData = [];
        if ($request->has('fnbs')) {
            foreach ($request->fnbs as $fnb) {
                $fnbData[$fnb['id']] = ['quantity' => $fnb['quantity'] ?? 1];
            }
        }
        $package->fnbs()->sync($fnbData);

        return redirect()->route('custom-package.index')
            ->with('success', 'Paket kustom berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $package = CustomPackage::findOrFail($id);
        $package->delete();

        return redirect()->route('custom-package.index')
            ->with('success', 'Paket kustom berhasil dihapus!');
    }

    public function toggleStatus($id)
    {
        $package = CustomPackage::findOrFail($id);
        $package->is_active = !$package->is_active;
        $package->save();

        return redirect()->route('custom-package.index')
            ->with('success', 'Status paket berhasil diperbarui!');
    }
}
