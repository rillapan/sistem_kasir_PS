<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Playstation;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $member = 0;
        $play = Playstation::count();
        $transaction = Transaction::count();
        $revenue = Transaction::sum('total');
        
        // Calculate today's revenue
        $todayRevenue = Transaction::whereDate('created_at', today())
            ->where(function($query) {
                $query->where('status_transaksi', 'sukses')
                      ->orWhere('payment_status', 'paid');
            })
            ->sum('total');
            
        return view('home', [
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'member' => $member,
            'play' => $play,
            'transaksi' => $transaction,
            'pendapatan' => $revenue,
            'today_pendapatan' => $todayRevenue
        ]);
    }



    public function pieCartData2()
    {
        $playstations = Playstation::with(['device.transaction' => function($query) {
            $query->where('status_transaksi', 'sukses')
                  ->orWhere('payment_status', 'paid');
        }])
        ->whereHas('device.transaction', function($query) {
            $query->where('status_transaksi', 'sukses')
                  ->orWhere('payment_status', 'paid');
        })
        ->get();
        
        $totalRevenue = [];
        $labels = [];
        $backgroundColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
        $hoverBackgroundColors = ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#6c757d'];

        foreach ($playstations as $index => $playstation) {
            $sumRevenue = $playstation->device->sum(function($device) {
                return $device->transaction->sum('total');
            });
            
            if ($sumRevenue > 0) {
                $labels[] = $playstation->nama . ' (' . number_format($sumRevenue, 0, ',', '.') . ')';
                $totalRevenue[] = $sumRevenue;
            }
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $totalRevenue,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($labels)),
                    'hoverBackgroundColor' => array_slice($hoverBackgroundColors, 0, count($labels)),
                    'hoverBorderColor' => "rgba(234, 236, 244, 1)",
                ]
            ]
        ];

        return $chartData;
    }
    
    public function popularFnbs()
    {
        $popularFnbs = \App\Models\TransactionFnb::selectRaw('fnbs.nama, SUM(transaction_fnbs.qty) as total_qty')
            ->join('fnbs', 'fnbs.id', '=', 'transaction_fnbs.fnb_id')
            ->join('transactions', 'transactions.id', '=', 'transaction_fnbs.transaction_id')
            ->where(function($query) {
                $query->where('transactions.status_transaksi', 'sukses')
                      ->orWhere('transactions.payment_status', 'paid');
            })
            ->groupBy('fnbs.id', 'fnbs.nama')
            ->orderBy('total_qty', 'DESC')
            ->limit(5)
            ->get();
            
        // If no data, return empty arrays for chart
        if ($popularFnbs->isEmpty()) {
            return [
                'labels' => [],
                'data' => []
            ];
        }
        
        return [
            'labels' => $popularFnbs->pluck('nama')->toArray(),
            'data' => $popularFnbs->pluck('total_qty')->map(function($item) {
                return (int)$item;
            })->toArray()
        ];
    }

    public function areaCartData()
    {
        $totals = [];
        $currentYear = date('Y');
        
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $total = Transaction::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $bulan)
                ->where(function($query) {
                    $query->where('status_transaksi', 'sukses')
                          ->orWhere('payment_status', 'paid');
                })
                ->sum('total');
            $totals[] = $total;
        }

        $chartData = [
            'datasets' => [
                [
                    "label" => "Pendapatan",
                    "lineTension" => 0.3,
                    "backgroundColor" => "rgba(78, 115, 223, 0.05)",
                    "borderColor" => "rgba(78, 115, 223, 1)",
                    "pointRadius" => 3,
                    "pointBackgroundColor" => "rgba(78, 115, 223, 1)",
                    "pointBorderColor" => "rgba(78, 115, 223, 1)",
                    "pointHoverRadius" => 3,
                    "pointHoverBackgroundColor" => "rgba(78, 115, 223, 1)",
                    "pointHoverBorderColor" => "rgba(78, 115, 223, 1)",
                    "pointHitRadius" => 10,
                    "pointBorderWidth" => 2,
                    "data" => $totals,
                ]
            ]
        ];

        return $chartData;
    }

    public function profile()
    {
        return view('profile.index', [
            'title' => 'Profile',
            'active' => ''
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $validatedData = $request->validate([
            'name' => 'required|min:3',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'required|min:8',
            'status' => 'required'
        ]);

        if ($request->password === $user->password) {
            unset($validatedData['password']);
        } else {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        User::where('id', $id)->update($validatedData);

        return redirect('/profile')->with('success', 'Profile berhasil di update.');
    }
}
