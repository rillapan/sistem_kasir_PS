<?php

namespace App\Http\Controllers;

use App\Models\CustomPackage;
use App\Models\Playstation;
use App\Models\Fnb;
use App\Models\PriceGroup;
use Illuminate\Http\Request;

class CustomPackageController extends Controller
{
    public function index()
    {
        $packages = CustomPackage::with(['playstations', 'fnbs', 'priceGroup'])->get();
        return view('custom-package.index', [
            'title' => 'Custom Paket',
            'active' => 'custom-package',
            'packages' => $packages
        ]);
    }

    public function create()
    {
        $playstations = Playstation::all();
        $fnbs = Fnb::all();
        $priceGroups = \App\Models\PriceGroup::all();
        return view('custom-package.create', [
            'title' => 'Tambah Custom Paket',
            'active' => 'custom-package',
            'playstations' => $playstations,
            'fnbs' => $fnbs,
            'priceGroups' => $priceGroups
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga_total' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'price_group_id' => 'nullable|exists:price_groups,id',
            'playstations' => 'required|array|min:1',
            'playstations.*.id' => 'required|exists:playstations,id',
            'playstations.*.lama_main' => 'required|integer|min:1',
            'fnbs' => 'array',
            'fnbs.*.id' => 'exists:fnbs,id',
            'fnbs.*.quantity' => 'integer|min:1',
        ]);

        $package = CustomPackage::create([
            'nama_paket' => $request->nama_paket,
            'harga_total' => $request->harga_total,
            'deskripsi' => $request->deskripsi,
            'price_group_id' => $request->price_group_id,
            'is_active' => true,
        ]);

        // Attach playstations
        if ($request->has('playstations')) {
            foreach ($request->playstations as $playstation) {
                $package->playstations()->attach($playstation['id'], [
                    'lama_main' => $playstation['lama_main']
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
        return redirect()->route('custom-package.index')->with('success', 'Paket kustom berhasil dibuat!');
    }

    public function show($id)
    {
        $package = CustomPackage::with(['playstations', 'fnbs'])->findOrFail($id);
        
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
        $package = CustomPackage::with(['playstations', 'fnbs', 'priceGroup'])->findOrFail($id);
        $playstations = Playstation::all();
        $fnbs = Fnb::all();
        $priceGroups = \App\Models\PriceGroup::all();
        return view('custom-package.edit', [
            'title' => 'Edit Custom Paket',
            'active' => 'custom-package',
            'package' => $package,
            'playstations' => $playstations,
            'fnbs' => $fnbs,
            'priceGroups' => $priceGroups
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga_total' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'playstations' => 'required|array|min:1',
            'playstations.*.id' => 'required|exists:playstations,id',
            'playstations.*.lama_main' => 'required|integer|min:1',
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

        // Sync playstations
        $playstationData = [];
        foreach ($request->playstations as $playstation) {
            $playstationData[$playstation['id']] = ['lama_main' => $playstation['lama_main']];
        }
        $package->playstations()->sync($playstationData);

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
