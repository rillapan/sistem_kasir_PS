<?php

namespace App\Http\Controllers;

use App\Models\Fnb;
use App\Models\PriceGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Exports\FnbSalesExport;


class FnbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fnbs = Fnb::with('priceGroup')->latest()->paginate(10);
        return view('fnb.index', [
            'title' => 'Kelola Barang',
            'active' => 'fnb',
            'fnbs' => $fnbs
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $priceGroups = PriceGroup::all();
        return view('fnb.create', [
            'title' => 'Tambah Barang',
            'active' => 'fnb',
            'priceGroups' => $priceGroups
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
        $rules = [
            'nama' => 'required|string|max:255',
            'price_group_id' => 'required|exists:price_groups,id',
            'stok_type' => 'required|in:limit,unlimit',
            'deskripsi' => 'nullable|string',
        ];

        // If stok_type is limit, stok is required and must be >= 0
        if ($request->stok_type === 'limit') {
            $rules['stok'] = 'required|integer|min:0';
        }

        $validatedData = $request->validate($rules);

        // Get price from price group
        $priceGroup = PriceGroup::findOrFail($validatedData['price_group_id']);
        $validatedData['harga_jual'] = $priceGroup->harga;
        
        // Handle stok based on type
        if ($validatedData['stok_type'] === 'unlimit') {
            $validatedData['stok'] = -1; // Use -1 as marker for unlimited
        }
        
        // Remove stok_type from validated data as it's not a database field
        unset($validatedData['stok_type']);

        Fnb::create($validatedData);

        return redirect('/fnb')->with('success', 'Barang berhasil ditambahkan.');
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
        $fnb = Fnb::findOrFail($id);
        $priceGroups = PriceGroup::all();
        
        // Determine stok_type based on stok value
        $stokType = ($fnb->stok == -1) ? 'unlimit' : 'limit';
        
        return view('fnb.edit', [
            'title' => 'Edit Barang',
            'active' => 'fnb',
            'fnb' => $fnb,
            'priceGroups' => $priceGroups,
            'stokType' => $stokType
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
        $fnb = Fnb::findOrFail($id);

        $rules = [
            'nama' => 'required|string|max:255',
            'price_group_id' => 'required|exists:price_groups,id',
            'stok_type' => 'required|in:limit,unlimit',
            'deskripsi' => 'nullable|string',
        ];

        // If stok_type is limit, stok is required and must be >= 0
        if ($request->stok_type === 'limit') {
            $rules['stok'] = 'required|integer|min:0';
        }

        $validatedData = $request->validate($rules);

        // Get price from price group
        $priceGroup = PriceGroup::findOrFail($validatedData['price_group_id']);
        $validatedData['harga_jual'] = $priceGroup->harga;
        
        // Handle stok based on type
        if ($validatedData['stok_type'] === 'unlimit') {
            $validatedData['stok'] = -1; // Use -1 as marker for unlimited
        }
        
        // Keep existing harga_beli if it exists (for backward compatibility)
        if (!isset($validatedData['harga_beli'])) {
            $validatedData['harga_beli'] = $fnb->harga_beli;
        }
        
        // Remove stok_type from validated data as it's not a database field
        unset($validatedData['stok_type']);

        $fnb->update($validatedData);

        return redirect('/fnb')->with('success', 'Barang berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Fnb::findOrFail($id)->delete();
        return redirect('/fnb')->with('success', 'Barang berhasil dihapus.');
    }

    public function laporanPenjualan(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Set date range based on period
        switch ($period) {
            case 'today':
                $startDate = Carbon::now()->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
                $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfMonth();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        // Query aggregated sales data
        $salesData = DB::table('transaction_fnbs')
            ->join('fnbs', 'transaction_fnbs.fnb_id', '=', 'fnbs.id')
            ->join('transactions', 'transaction_fnbs.transaction_id', '=', 'transactions.id_transaksi')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select(
                'fnbs.id',
                'fnbs.nama',
                DB::raw('SUM(transaction_fnbs.qty) as jumlah_terjual'),
                DB::raw('SUM(fnbs.harga_beli * transaction_fnbs.qty) as total_modal'),
                DB::raw('SUM(transaction_fnbs.harga_jual * transaction_fnbs.qty) as total_penjualan'),
                DB::raw('SUM((transaction_fnbs.harga_jual - fnbs.harga_beli) * transaction_fnbs.qty) as laba_rugi')
            )
            ->groupBy('fnbs.id', 'fnbs.nama')
            ->get();

        // Data for charts
        $chartData = [
            'sales_over_time' => $this->getSalesOverTime($startDate, $endDate),
            'product_distribution' => $salesData->pluck('jumlah_terjual', 'nama')->toArray(),
            'profit_loss' => $salesData->pluck('laba_rugi', 'nama')->toArray(),
        ];

        return view('fnb.laporan-penjualan', [
            'title' => 'Laporan Penjualan FnB',
            'active' => 'fnb-laporan',
            'salesData' => $salesData,
            'chartData' => $chartData,
            'period' => $period,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }

    private function getSalesOverTime($startDate, $endDate)
    {
        return DB::table('transaction_fnbs')
            ->join('transactions', 'transaction_fnbs.transaction_id', '=', 'transactions.id_transaksi')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(transaction_fnbs.qty) as total_qty')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_qty', 'date')
            ->toArray();
    }

    public function exportExcel(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Similar date logic as above
        switch ($period) {
            case 'today':
                $startDate = Carbon::now()->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
                $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfMonth();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        $salesData = DB::table('transaction_fnbs')
            ->join('fnbs', 'transaction_fnbs.fnb_id', '=', 'fnbs.id')
            ->join('transactions', 'transaction_fnbs.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select(
                'fnbs.nama',
                DB::raw('SUM(transaction_fnbs.qty) as jumlah_terjual'),
                DB::raw('SUM(fnbs.harga_beli * transaction_fnbs.qty) as total_modal'),
                DB::raw('SUM(transaction_fnbs.harga_jual * transaction_fnbs.qty) as total_penjualan'),
                DB::raw('SUM((transaction_fnbs.harga_jual - fnbs.harga_beli) * transaction_fnbs.qty) as laba_rugi')
            )
            ->groupBy('fnbs.id', 'fnbs.nama')
            ->get();

        return Excel::download(new FnbSalesExport($salesData, $startDate, $endDate), 'laporan_penjualan_fnb.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Similar date logic
        switch ($period) {
            case 'today':
                $startDate = Carbon::now()->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
                $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfMonth();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }

        $salesData = DB::table('transaction_fnbs')
            ->join('fnbs', 'transaction_fnbs.fnb_id', '=', 'fnbs.id')
            ->join('transactions', 'transaction_fnbs.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select(
                'fnbs.nama',
                DB::raw('SUM(transaction_fnbs.qty) as jumlah_terjual'),
                DB::raw('SUM(fnbs.harga_beli * transaction_fnbs.qty) as total_modal'),
                DB::raw('SUM(transaction_fnbs.harga_jual * transaction_fnbs.qty) as total_penjualan'),
                DB::raw('SUM((transaction_fnbs.harga_jual - fnbs.harga_beli) * transaction_fnbs.qty) as laba_rugi')
            )
            ->groupBy('fnbs.id', 'fnbs.nama')
            ->get();

        $pdf = PDF::loadView('fnb.laporan-pdf', compact('salesData', 'startDate', 'endDate'));
        return $pdf->download('laporan_penjualan_fnb.pdf');
    }
}
