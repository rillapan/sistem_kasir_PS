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
    private function getDatesFromRequest(Request $request)
    {
        $period = $request->input('period');
        $startDate = $request->input('tanggal_awal');
        $endDate = $request->input('tanggal_akhir');

        if ($period) {
            $now = Carbon::now();
            switch ($period) {
                case 'today':
                    $startDate = $now->copy()->startOfDay()->toDateString();
                    $endDate = $now->copy()->endOfDay()->toDateString();
                    break;
                case 'this_week':
                    $startDate = $now->copy()->startOfWeek()->toDateString();
                    $endDate = $now->copy()->endOfWeek()->toDateString();
                    break;
                case 'this_month':
                    $startDate = $now->copy()->startOfMonth()->toDateString();
                    $endDate = $now->copy()->endOfMonth()->toDateString();
                    break;
                case 'this_year':
                    $startDate = $now->copy()->startOfYear()->toDateString();
                    $endDate = $now->copy()->endOfYear()->toDateString();
                    break;
                case 'custom':
                default:
                    // Use provided start/end dates
                    break;
            }
        }

        return [$startDate, $endDate, $period];
    }

    public function index(Request $request)
    {
        $now = Carbon::now();

        // Only include paid transactions in income calculations
        $dailyIncome = Transaction::where('payment_status', 'paid')
            ->whereDate('created_at', $now->toDateString())
            ->sum('total');

        $monthlyIncome = Transaction::where('payment_status', 'paid')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('total');

        $yearlyIncome = Transaction::where('payment_status', 'paid')
            ->whereYear('created_at', $now->year)
            ->sum('total');

        // Resolve dates
        list($startDate, $endDate, $period) = $this->getDatesFromRequest($request);

        $periodIncome = 0;
        $chartData = [];
        $chartLabels = [];

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $periodTransactions = Transaction::where('payment_status', 'paid')
                ->whereBetween('created_at', [$start, $end])
                ->get();

            $periodIncome = $periodTransactions->sum('total');

            // Construct Chart Data based on Period
            switch ($period) {
                case 'today':
                    // Group by Hour (00:00 - 23:00)
                    for ($i = 0; $i < 24; $i++) {
                        $label = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                        $chartLabels[] = $label;
                        $tempData[$label] = 0;
                    }
                    foreach ($periodTransactions as $t) {
                        $label = $t->created_at->format('H:00');
                        if (isset($tempData[$label])) {
                            $tempData[$label] += $t->total;
                        }
                    }
                    $chartData = array_values($tempData);
                    break;

                case 'this_week':
                    // Group by Day (Senin - Minggu)
                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                    $chartLabels = $days;
                    $chartData = array_fill(0, 7, 0);
                    
                    foreach ($periodTransactions as $t) {
                        // dayOfWeekIso returns 1 (Monday) to 7 (Sunday)
                        $index = $t->created_at->dayOfWeekIso - 1; 
                        $chartData[$index] += $t->total;
                    }
                    break;

                case 'this_month':
                    // Group by Week (Minggu ke-1 - Minggu ke-5)
                    for ($i = 1; $i <= 5; $i++) {
                        $label = 'Minggu ke-' . $i;
                        $chartLabels[] = $label;
                        $tempData[$i] = 0;
                    }
                    
                    foreach ($periodTransactions as $t) {
                        $weekNum = $t->created_at->weekOfMonth;
                        // Use weekOfMonth directly. If it goes up to 6, we might need adjustments, 
                        // but normally 1-5 covers it.
                        if (isset($tempData[$weekNum])) {
                            $tempData[$weekNum] += $t->total;
                        } elseif ($weekNum > 5) {
                            // Merge overflow into week 5 or create week 6? Lets strict to 5 for now 
                            // or dynamic. Better dynamic if > 5.
                             $tempData[5] += $t->total; 
                        }
                    }
                    $chartData = array_values($tempData);
                    break;

                case 'this_year':
                    // Group by Month (Januari - Desember)
                    $months = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    $chartLabels = $months;
                    $chartData = array_fill(0, 12, 0);

                    foreach ($periodTransactions as $t) {
                        $index = $t->created_at->month - 1; // 1-12 becomes 0-11
                        $chartData[$index] += $t->total;
                    }
                    break;

                case 'custom':
                default:
                    // Group by Date (Y-m-d)
                    $dailyTotals = $periodTransactions->groupBy(function ($transaction) {
                        return $transaction->created_at->format('Y-m-d');
                    })->map->sum('total');

                    $chartLabels = $dailyTotals->keys()->toArray();
                    $chartData = $dailyTotals->values()->toArray();
                    break;
            }
        }

        return view('laporan.index', [
            'title' => 'Laporan',
            'active' => 'report',
            'dailyIncome' => $dailyIncome,
            'monthlyIncome' => $monthlyIncome,
            'yearlyIncome' => $yearlyIncome,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
            'periodIncome' => $periodIncome,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ]);
    }

    public function generatePDF(Request $request)
    {
        list($startDate, $endDate, $period) = $this->getDatesFromRequest($request);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $transactions = Transaction::with(['device.playstation', 'transactionFnbs.fnb', 'custom_package'])
            ->where('payment_status', 'paid')
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
        list($startDate, $endDate, $period) = $this->getDatesFromRequest($request);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $transactions = Transaction::with(['device.playstation', 'transactionFnbs.fnb', 'custom_package'])
            ->where('payment_status', 'paid')
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
