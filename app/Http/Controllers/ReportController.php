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
        
        // Get all PlayStation types for filtering
        $playstationTypes = \App\Models\Playstation::all();

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
        
        // Get selected PlayStation types from request
        $selectedPlaystationIds = $request->input('playstation_types', []);

        $periodIncome = 0;
        $chartData = [];
        $chartLabels = [];
        $chartDatasets = []; // For multiple datasets (one per PlayStation type)

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            // Build query with PlayStation type filter if selected
            $query = Transaction::where('payment_status', 'paid')
                ->whereBetween('created_at', [$start, $end])
                ->with('device.playstation'); // Eager load relationships
                
            if (!empty($selectedPlaystationIds)) {
                // Filter by PlayStation through device relationship
                $query->whereHas('device', function($q) use ($selectedPlaystationIds) {
                    $q->whereIn('playstation_id', $selectedPlaystationIds);
                });
            }
            
            $periodTransactions = $query->get();

            $periodIncome = $periodTransactions->sum('total');
            
            // Debug: Log data structure
            \Log::info('Period Transactions Count: ' . $periodTransactions->count());
            \Log::info('Selected PlayStation IDs: ' . json_encode($selectedPlaystationIds));
            \Log::info('Period Transactions: ' . $periodTransactions->take(3)->map(function($t) {
                return [
                    'id' => $t->id_transaksi,
                    'device_id' => $t->device_id,
                    'playstation_id' => $t->playstation_id,
                    'device_playstation_id' => $t->device->playstation_id ?? null,
                    'total' => $t->total,
                    'created_at' => $t->created_at
                ];
            }));

            // Construct Chart Data based on Period
            switch ($period) {
                case 'today':
                    // Group by Hour (00:00 - 23:00)
                    for ($i = 0; $i < 24; $i++) {
                        $label = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                        $chartLabels[] = $label;
                    }
                    
                    if (!empty($selectedPlaystationIds)) {
                        // Create dataset for each selected PlayStation type
                        $colors = ['rgba(78, 115, 223, 1)', 'rgba(28, 200, 138, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)', 'rgba(54, 162, 235, 1)'];
                        $bgColors = ['rgba(78, 115, 223, 0.05)', 'rgba(28, 200, 138, 0.05)', 'rgba(255, 99, 132, 0.05)', 'rgba(255, 206, 86, 0.05)', 'rgba(54, 162, 235, 0.05)'];
                        
                        foreach ($selectedPlaystationIds as $index => $psId) {
                            $playstation = $playstationTypes->find($psId);
                            $tempData = array_fill(0, 24, 0);
                            
                            $psTransactions = $periodTransactions->filter(function($t) use ($psId) {
                                return $t->device && $t->device->playstation_id == $psId;
                            });
                            foreach ($psTransactions as $t) {
                                $hour = (int)$t->created_at->format('H');
                                $tempData[$hour] += $t->total;
                            }
                            
                            $chartDatasets[] = [
                                'label' => $playstation ? $playstation->nama : 'Playstation ' . $psId,
                                'data' => $tempData,
                                'borderColor' => $colors[$index % count($colors)],
                                'backgroundColor' => $bgColors[$index % count($bgColors)],
                                'lineTension' => 0.3,
                                'pointRadius' => 3,
                                'pointBackgroundColor' => $colors[$index % count($colors)],
                                'pointBorderColor' => $colors[$index % count($colors)],
                                'pointHoverRadius' => 3,
                                'pointHoverBackgroundColor' => $colors[$index % count($colors)],
                                'pointHoverBorderColor' => $colors[$index % count($colors)],
                                'pointHitRadius' => 10,
                                'pointBorderWidth' => 2,
                            ];
                        }
                    } else {
                        // Single dataset for all types
                        $tempData = array_fill(0, 24, 0);
                        foreach ($periodTransactions as $t) {
                            $hour = (int)$t->created_at->format('H');
                            $tempData[$hour] += $t->total;
                        }
                        $chartData = $tempData;
                    }
                    break;

                case 'this_week':
                    // Group by Day (Senin - Minggu)
                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                    $chartLabels = $days;
                    
                    if (!empty($selectedPlaystationIds)) {
                        // Create dataset for each selected PlayStation type
                        $colors = ['rgba(78, 115, 223, 1)', 'rgba(28, 200, 138, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)', 'rgba(54, 162, 235, 1)'];
                        $bgColors = ['rgba(78, 115, 223, 0.05)', 'rgba(28, 200, 138, 0.05)', 'rgba(255, 99, 132, 0.05)', 'rgba(255, 206, 86, 0.05)', 'rgba(54, 162, 235, 0.05)'];
                        
                        foreach ($selectedPlaystationIds as $index => $psId) {
                            $playstation = $playstationTypes->find($psId);
                            $tempData = array_fill(0, 7, 0);
                            
                            $psTransactions = $periodTransactions->filter(function($t) use ($psId) {
                                return $t->device && $t->device->playstation_id == $psId;
                            });
                            foreach ($psTransactions as $t) {
                                $dayIndex = $t->created_at->dayOfWeekIso - 1; // 1-7 becomes 0-6
                                $tempData[$dayIndex] += $t->total;
                            }
                            
                            $chartDatasets[] = [
                                'label' => $playstation ? $playstation->nama : 'Playstation ' . $psId,
                                'data' => $tempData,
                                'borderColor' => $colors[$index % count($colors)],
                                'backgroundColor' => $bgColors[$index % count($bgColors)],
                                'lineTension' => 0.3,
                                'pointRadius' => 3,
                                'pointBackgroundColor' => $colors[$index % count($colors)],
                                'pointBorderColor' => $colors[$index % count($colors)],
                                'pointHoverRadius' => 3,
                                'pointHoverBackgroundColor' => $colors[$index % count($colors)],
                                'pointHoverBorderColor' => $colors[$index % count($colors)],
                                'pointHitRadius' => 10,
                                'pointBorderWidth' => 2,
                            ];
                        }
                    } else {
                        // Single dataset for all types
                        $chartData = array_fill(0, 7, 0);
                        foreach ($periodTransactions as $t) {
                            $index = $t->created_at->dayOfWeekIso - 1; 
                            $chartData[$index] += $t->total;
                        }
                    }
                    break;

                case 'this_month':
                    // Group by Week (Minggu ke-1 - Minggu ke-5)
                    for ($i = 1; $i <= 5; $i++) {
                        $label = 'Minggu ke-' . $i;
                        $chartLabels[] = $label;
                    }
                    
                    if (!empty($selectedPlaystationIds)) {
                        // Create dataset for each selected PlayStation type
                        $colors = ['rgba(78, 115, 223, 1)', 'rgba(28, 200, 138, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)', 'rgba(54, 162, 235, 1)'];
                        $bgColors = ['rgba(78, 115, 223, 0.05)', 'rgba(28, 200, 138, 0.05)', 'rgba(255, 99, 132, 0.05)', 'rgba(255, 206, 86, 0.05)', 'rgba(54, 162, 235, 0.05)'];
                        
                        foreach ($selectedPlaystationIds as $index => $psId) {
                            $playstation = $playstationTypes->find($psId);
                            $tempData = array_fill(0, 5, 0);
                            
                            $psTransactions = $periodTransactions->filter(function($t) use ($psId) {
                                return $t->device && $t->device->playstation_id == $psId;
                            });
                            foreach ($psTransactions as $t) {
                                $weekNum = $t->created_at->weekOfMonth;
                                $weekIndex = ($weekNum > 5) ? 4 : $weekNum - 1; // 1-5 becomes 0-4
                                $tempData[$weekIndex] += $t->total;
                            }
                            
                            $chartDatasets[] = [
                                'label' => $playstation ? $playstation->nama : 'Playstation ' . $psId,
                                'data' => $tempData,
                                'borderColor' => $colors[$index % count($colors)],
                                'backgroundColor' => $bgColors[$index % count($bgColors)],
                                'lineTension' => 0.3,
                                'pointRadius' => 3,
                                'pointBackgroundColor' => $colors[$index % count($colors)],
                                'pointBorderColor' => $colors[$index % count($colors)],
                                'pointHoverRadius' => 3,
                                'pointHoverBackgroundColor' => $colors[$index % count($colors)],
                                'pointHoverBorderColor' => $colors[$index % count($colors)],
                                'pointHitRadius' => 10,
                                'pointBorderWidth' => 2,
                            ];
                        }
                    } else {
                        // Single dataset for all types
                        $chartData = array_fill(0, 5, 0);
                        foreach ($periodTransactions as $t) {
                            $weekNum = $t->created_at->weekOfMonth;
                            $weekIndex = ($weekNum > 5) ? 4 : $weekNum - 1; // 1-5 becomes 0-4
                            $chartData[$weekIndex] += $t->total;
                        }
                    }
                    break;

                case 'this_year':
                    // Group by Month (Januari - Desember)
                    $months = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    $chartLabels = $months;
                    
                    if (!empty($selectedPlaystationIds)) {
                        // Create dataset for each selected PlayStation type
                        $colors = ['rgba(78, 115, 223, 1)', 'rgba(28, 200, 138, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)', 'rgba(54, 162, 235, 1)'];
                        $bgColors = ['rgba(78, 115, 223, 0.05)', 'rgba(28, 200, 138, 0.05)', 'rgba(255, 99, 132, 0.05)', 'rgba(255, 206, 86, 0.05)', 'rgba(54, 162, 235, 0.05)'];
                        
                        foreach ($selectedPlaystationIds as $index => $psId) {
                            $playstation = $playstationTypes->find($psId);
                            $tempData = array_fill(0, 12, 0);
                            
                            $psTransactions = $periodTransactions->filter(function($t) use ($psId) {
                                return $t->device && $t->device->playstation_id == $psId;
                            });
                            foreach ($psTransactions as $t) {
                                $monthIndex = $t->created_at->month - 1; // 1-12 becomes 0-11
                                $tempData[$monthIndex] += $t->total;
                            }
                            
                            $chartDatasets[] = [
                                'label' => $playstation ? $playstation->nama : 'Playstation ' . $psId,
                                'data' => $tempData,
                                'borderColor' => $colors[$index % count($colors)],
                                'backgroundColor' => $bgColors[$index % count($bgColors)],
                                'lineTension' => 0.3,
                                'pointRadius' => 3,
                                'pointBackgroundColor' => $colors[$index % count($colors)],
                                'pointBorderColor' => $colors[$index % count($colors)],
                                'pointHoverRadius' => 3,
                                'pointHoverBackgroundColor' => $colors[$index % count($colors)],
                                'pointHoverBorderColor' => $colors[$index % count($colors)],
                                'pointHitRadius' => 10,
                                'pointBorderWidth' => 2,
                            ];
                        }
                    } else {
                        // Single dataset for all types
                        $chartData = array_fill(0, 12, 0);
                        foreach ($periodTransactions as $t) {
                            $index = $t->created_at->month - 1; // 1-12 becomes 0-11
                            $chartData[$index] += $t->total;
                        }
                    }
                    break;

                case 'custom':
                default:
                    // Group by Date (Y-m-d)
                    if (!empty($selectedPlaystationIds)) {
                        // Create dataset for each selected PlayStation type
                        $colors = ['rgba(78, 115, 223, 1)', 'rgba(28, 200, 138, 1)', 'rgba(255, 99, 132, 1)', 'rgba(255, 206, 86, 1)', 'rgba(54, 162, 235, 1)'];
                        $bgColors = ['rgba(78, 115, 223, 0.05)', 'rgba(28, 200, 138, 0.05)', 'rgba(255, 99, 132, 0.05)', 'rgba(255, 206, 86, 0.05)', 'rgba(54, 162, 235, 0.05)'];
                        
                        // Get all unique dates first
                        $allDates = $periodTransactions->map(function ($transaction) {
                            return $transaction->created_at->format('Y-m-d');
                        })->unique()->sort()->values();
                        
                        $chartLabels = $allDates->toArray();
                        
                        foreach ($selectedPlaystationIds as $index => $psId) {
                            $playstation = $playstationTypes->find($psId);
                            $tempData = array_fill(0, count($allDates), 0);
                            
                            $psTransactions = $periodTransactions->filter(function($t) use ($psId) {
                                return $t->device && $t->device->playstation_id == $psId;
                            });
                            foreach ($psTransactions as $t) {
                                $date = $t->created_at->format('Y-m-d');
                                $dateIndex = $allDates->search($date);
                                if ($dateIndex !== false) {
                                    $tempData[$dateIndex] += $t->total;
                                }
                            }
                            
                            $chartDatasets[] = [
                                'label' => $playstation ? $playstation->nama : 'Playstation ' . $psId,
                                'data' => $tempData,
                                'borderColor' => $colors[$index % count($colors)],
                                'backgroundColor' => $bgColors[$index % count($bgColors)],
                                'lineTension' => 0.3,
                                'pointRadius' => 3,
                                'pointBackgroundColor' => $colors[$index % count($colors)],
                                'pointBorderColor' => $colors[$index % count($colors)],
                                'pointHoverRadius' => 3,
                                'pointHoverBackgroundColor' => $colors[$index % count($colors)],
                                'pointHoverBorderColor' => $colors[$index % count($colors)],
                                'pointHitRadius' => 10,
                                'pointBorderWidth' => 2,
                            ];
                        }
                    } else {
                        // Single dataset for all types
                        $dailyTotals = $periodTransactions->groupBy(function ($transaction) {
                            return $transaction->created_at->format('Y-m-d');
                        })->map->sum('total');

                        $chartLabels = $dailyTotals->keys()->toArray();
                        $chartData = $dailyTotals->values()->toArray();
                    }
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
            'chartDatasets' => $chartDatasets,
            'playstationTypes' => $playstationTypes,
            'selectedPlaystationIds' => $selectedPlaystationIds,
        ]);
    }

    public function generatePDF(Request $request)
    {
        list($startDate, $endDate, $period) = $this->getDatesFromRequest($request);
        $selectedPlaystationIds = $request->input('playstation_types', []);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $query = Transaction::with(['device.playstation', 'transactionFnbs.fnb', 'custom_package'])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end]);
            
        // Apply PlayStation type filter if selected
        if (!empty($selectedPlaystationIds)) {
            $query->whereHas('device', function($q) use ($selectedPlaystationIds) {
                $q->whereIn('playstation_id', $selectedPlaystationIds);
            });
        }
        
        $transactions = $query->get();
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
            'selectedPlaystationIds' => $selectedPlaystationIds,
        ]);
        return $pdf->download('laporan.pdf');
    }

    public function generateExcel(Request $request)
    {
        list($startDate, $endDate, $period) = $this->getDatesFromRequest($request);
        $selectedPlaystationIds = $request->input('playstation_types', []);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $query = Transaction::with(['device.playstation', 'transactionFnbs.fnb', 'custom_package'])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end]);
            
        // Apply PlayStation type filter if selected
        if (!empty($selectedPlaystationIds)) {
            $query->whereHas('device', function($q) use ($selectedPlaystationIds) {
                $q->whereIn('playstation_id', $selectedPlaystationIds);
            });
        }
        
        $transactions = $query->get();
        $total = $transactions->sum('total');

        // Daily statistics for summary
        $dailyTotals = $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map->sum('total');

        $excel = Excel::download(new TransactionsExport($transactions, $startDate, $endDate, $total, $dailyTotals), 'laporan.xlsx');

        return $excel;
    }

    public function transactionReport(Request $request)
    {
        $startDateTime = $request->input('start_datetime');
        $endDateTime = $request->input('end_datetime');

        $query = Transaction::with(['device.playstation', 'user'])
            ->where('payment_status', 'paid');

        if ($startDateTime) {
            $start = Carbon::parse($startDateTime);
            $query->where('created_at', '>=', $start);
        }

        if ($endDateTime) {
            $end = Carbon::parse($endDateTime);
            $query->where('updated_at', '<=', $end);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        return view('laporan.transaction-report', [
            'title' => 'Laporan Transaksi',
            'active' => 'report',
            'transactions' => $transactions,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
        ]);
    }
}
