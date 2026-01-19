<?php

namespace App\Http\Controllers;

use App\Models\HourlyPrice;
use App\Models\Playstation;
use Illuminate\Http\Request;

class HourlyPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $playstationId
     * @return \Illuminate\Http\Response
     */
    public function index($playstationId)
    {
        $playstation = Playstation::findOrFail($playstationId);
        $hourlyPrices = $playstation->hourlyPrices()->orderBy('hour')->get();

        return view('playstation.hourly-prices.index', [
            'title' => 'Harga per Jam - ' . $playstation->nama,
            'active' => 'play',
            'playstation' => $playstation,
            'hourlyPrices' => $hourlyPrices
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $playstationId
     * @return \Illuminate\Http\Response
     */
    public function create($playstationId)
    {
        $playstation = Playstation::findOrFail($playstationId);

        return view('playstation.hourly-prices.create', [
            'title' => 'Tambah Harga per Jam - ' . $playstation->nama,
            'active' => 'play',
            'playstation' => $playstation
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $playstationId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $playstationId)
    {
        $request->validate([
            'hour' => 'required|integer|min:1|unique:hourly_prices,hour,NULL,id,playstation_id,' . $playstationId,
            'price' => 'required|integer|min:0',
        ]);

        HourlyPrice::create([
            'playstation_id' => $playstationId,
            'hour' => $request->hour,
            'price' => $request->price,
        ]);

        return redirect()->route('hourly-prices.hourly-prices.index', $playstationId)->with('success', 'Harga per jam berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $playstationId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($playstationId, $id)
    {
        $playstation = Playstation::findOrFail($playstationId);
        $hourlyPrice = HourlyPrice::where('id', $id)->where('playstation_id', $playstationId)->firstOrFail();

        return view('playstation.hourly-prices.edit', [
            'title' => 'Edit Harga per Jam - ' . $playstation->nama,
            'active' => 'play',
            'playstation' => $playstation,
            'hourlyPrice' => $hourlyPrice
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $playstationId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $playstationId, $id)
    {
        $hourlyPrice = HourlyPrice::where('id', $id)->where('playstation_id', $playstationId)->firstOrFail();

        $request->validate([
            'hour' => 'required|integer|min:1|unique:hourly_prices,hour,' . $id . ',id,playstation_id,' . $playstationId,
            'price' => 'required|integer|min:0',
        ]);

        $hourlyPrice->update([
            'hour' => $request->hour,
            'price' => $request->price,
        ]);

        return redirect()->route('hourly-prices.hourly-prices.index', $playstationId)->with('success', 'Harga per jam berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $playstationId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($playstationId, $id)
    {
        $hourlyPrice = HourlyPrice::where('id', $id)->where('playstation_id', $playstationId)->firstOrFail();
        $hourlyPrice->delete();

        return redirect()->route('hourly-prices.hourly-prices.index', $playstationId)->with('success', 'Harga per jam berhasil dihapus.');
    }
}
