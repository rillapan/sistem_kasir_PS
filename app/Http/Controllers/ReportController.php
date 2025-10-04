<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        // Default incomes
        $dailyIncome = Transaction::where('status_transaksi', 'sukses')
            ->whereDate('created_at', $now->toDateString())
            ->sum('total');

        $monthlyIncome = Transaction::where('status_transaksi', 'sukses')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('total');

        $yearlyIncome = Transaction::where('status_transaksi', 'sukses')
            ->whereYear('created_at', $now->year)
            ->sum('total');

        // Custom date range
        $startDate = $request->input('tanggal_awal');
        $endDate = $request->input('tanggal_akhir');
        $periodIncome = 0;
        $chartData = [];
        $chartLabels = [];

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate)->endOfDay();

            $periodTransactions = Transaction::where('status_transaksi', 'sukses')
                ->whereBetween('created_at', [$start, $end])
                ->get();

            $periodIncome = $periodTransactions->sum('total');

            // Group by day for chart
            $dailyTotals = $periodTransactions->groupBy(function ($transaction) {
                return $transaction->created_at->format('Y-m-d');
            })->map->sum('total');

            $chartLabels = $dailyTotals->keys()->toArray();
            $chartData = $dailyTotals->values()->toArray();
        }

        return view('laporan.index', [
            'title' => 'Laporan',
            'active' => 'report',
            'dailyIncome' => $dailyIncome,
            'monthlyIncome' => $monthlyIncome,
            'yearlyIncome' => $yearlyIncome,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodIncome' => $periodIncome,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ]);
    }

    public function generatePDF(Request $request)
    {
        $startDate = $request->input('tanggal_awal');
        $endDate = $request->input('tanggal_akhir');

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate)->endOfDay();

        $transactions = Transaction::with('device.playstation')
            ->where('status_transaksi', 'sukses')
            ->whereBetween('created_at', [$start, $end])
            ->get();
        $total = $transactions->sum('total');

        // Daily statistics for graph-like summary
        $dailyTotals = $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map->sum('total');

        $pdf = PDF::loadView('laporan.cetak-pdf', [
            'title' => 'Laporan Transaksi',
            'active' => 'report',
            'transactions' => $transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total' => $total,
            'dailyTotals' => $dailyTotals,
        ]);
        return $pdf->download('laporan.pdf');
    }

    public function generateExcel(Request $request)
    {
        $startDate = $request->input('tanggal_awal');
        $endDate = $request->input('tanggal_akhir');

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate)->endOfDay();

        $transactions = Transaction::with('device.playstation')
            ->where('status_transaksi', 'sukses')
            ->whereBetween('created_at', [$start, $end])
            ->get();
        $total = $transactions->sum('total');

        // Daily statistics for summary
        $dailyTotals = $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map->sum('total');

        $excel = Excel::download(new TransactionsExport($transactions, $startDate, $endDate, $total, $dailyTotals), 'laporan.xlsx');

        return $excel;
    }
}
