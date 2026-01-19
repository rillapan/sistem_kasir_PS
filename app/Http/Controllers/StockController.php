<?php

namespace App\Http\Controllers;

use App\Models\Fnb;
use App\Models\StockMutation;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $fnbs = Fnb::with('stockMutations')->get();
        return view('stock.index', [
            'title' => 'Manajemen Stok',
            'active' => 'stock',
            'fnbs' => $fnbs
        ]);
    }

    public function addForm($id)
    {
        $fnb = Fnb::findOrFail($id);
        
        // Prevent access if stock is unlimited
        if ($fnb->stok == -1) {
            return redirect('/stock')->with('gagal', 'Tidak dapat menambah stok untuk barang dengan stok unlimited.');
        }
        
        return view('stock.add', [
            'title' => 'Tambah Stok',
            'active' => 'stock',
            'fnb' => $fnb
        ]);
    }

    public function addStock(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        $fnb = Fnb::findOrFail($id);
        
        // Prevent adding stock if stock is unlimited
        if ($fnb->stok == -1) {
            return redirect('/stock')->with('gagal', 'Tidak dapat menambah stok untuk barang dengan stok unlimited.');
        }
        
        $fnb->increment('stok', $request->qty);
        $fnb->save();

        StockMutation::create([
            'fnb_id' => $id,
            'type' => 'in',
            'qty' => $request->qty,
            'date' => now()->toDateString(),
            'note' => $request->note
        ]);

        return redirect('/stock')->with('success', 'Stok berhasil ditambahkan.');
    }

    public function reduceForm($id)
    {
        $fnb = Fnb::findOrFail($id);
        
        // Prevent access if stock is unlimited
        if ($fnb->stok == -1) {
            return redirect('/stock')->with('gagal', 'Tidak dapat mengurangi stok untuk barang dengan stok unlimited.');
        }
        
        return view('stock.reduce', [
            'title' => 'Kurangi Stok',
            'active' => 'stock',
            'fnb' => $fnb
        ]);
    }

    public function reduceStock(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
            'note' => 'nullable|string'
        ]);

        $fnb = Fnb::findOrFail($id);
        
        // Prevent reducing stock if stock is unlimited
        if ($fnb->stok == -1) {
            return redirect('/stock')->with('gagal', 'Tidak dapat mengurangi stok untuk barang dengan stok unlimited.');
        }
        
        // Check stock
        if ($fnb->stok < $request->qty) {
            return back()->with('gagal', 'Stok tidak cukup.');
        }

        $fnb->decrement('stok', $request->qty);

        StockMutation::create([
            'fnb_id' => $id,
            'type' => 'out',
            'qty' => $request->qty,
            'date' => now()->toDateString(),
            'note' => $request->note
        ]);

        return redirect('/stock')->with('success', 'Stok berhasil dikurangi.');
    }

    public function history($id)
    {
        $fnb = Fnb::findOrFail($id);
        $mutations = StockMutation::where('fnb_id', $id)->latest()->paginate(10);
        return view('stock.history', [
            'title' => 'Riwayat Mutasi Stok',
            'active' => 'stock',
            'fnb' => $fnb,
            'mutations' => $mutations
        ]);
    }
}
