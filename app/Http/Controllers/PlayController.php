<?php

namespace App\Http\Controllers;

use App\Models\Playstation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PlayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $play = Playstation::paginate(10);
        return view('playstation.index', [
            'title' => 'Jenis Playstation',
            'active' => 'play',
            'plays' => $play
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('playstation.create', [
            'title' => 'Create Playstation',
            'active' => 'play',

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
            'nama' => 'required|min:3',
            'harga' => 'required',
            
        ]);

        

        Playstation::create($validatedData);

        return redirect('playstation')->with('success', 'Data playstation berhasil ditambahkan.');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $play = Playstation::find($id);
        return view('playstation.edit', [
            'title' => 'Edit Playstation',
            'active' => 'play',
            'play' => $play
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
        $play = Playstation::find($id);
        $validatedData = $request->validate([
            'nama' => 'required|min:3',
            'harga' => 'required',
            
        ]);

        

        Playstation::where('id', $id)->update($validatedData);

        return redirect('playstation')->with('success', 'Data playstation berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $play = Playstation::find($id);
        if ($play->image) {
            Storage::delete($play->image);
        }
        Playstation::destroy($id);

        return redirect('playstation')->with('success', 'Data playstation berhasil dihapus.');
    }
}
