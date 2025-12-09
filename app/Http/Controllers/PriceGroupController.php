<?php

namespace App\Http\Controllers;

use App\Models\PriceGroup;
use App\Models\Fnb;
use Illuminate\Http\Request;

class PriceGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $priceGroups = PriceGroup::withCount('fnbs')->latest()->paginate(10);
        return view('price-group.index', [
            'title' => 'Kelompok Harga',
            'active' => 'price-group',
            'priceGroups' => $priceGroups
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('price-group.create', [
            'title' => 'Tambah Kelompok Harga',
            'active' => 'price-group'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        PriceGroup::create($validatedData);

        return redirect('/price-group')->with('success', 'Kelompok harga berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Display list of FnB items for a specific price group.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFnbs($id)
    {
        $priceGroup = PriceGroup::findOrFail($id);
        $fnbs = Fnb::where('price_group_id', $id)->latest()->paginate(10);
        
        return view('price-group.fnbs', [
            'title' => 'Data Barang FnB - ' . $priceGroup->nama,
            'active' => 'price-group',
            'priceGroup' => $priceGroup,
            'fnbs' => $fnbs
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $priceGroup = PriceGroup::findOrFail($id);
        return view('price-group.edit', [
            'title' => 'Edit Kelompok Harga',
            'active' => 'price-group',
            'priceGroup' => $priceGroup
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $priceGroup = PriceGroup::findOrFail($id);

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $priceGroup->update($validatedData);

        return redirect('/price-group')->with('success', 'Kelompok harga berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $priceGroup = PriceGroup::findOrFail($id);
        
        // Check if price group is being used by any FnB
        if ($priceGroup->fnbs()->count() > 0) {
            return redirect('/price-group')->with('gagal', 'Kelompok harga tidak dapat dihapus karena masih digunakan oleh barang FnB.');
        }

        $priceGroup->delete();
        return redirect('/price-group')->with('success', 'Kelompok harga berhasil dihapus.');
    }
}
