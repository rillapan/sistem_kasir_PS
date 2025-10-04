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
        return view('home', [
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'member' => $member,
            'play' => $play,
            'transaksi' => $transaction,
            'pendapatan' => $revenue
        ]);
    }



    public function pieCartData2()
    {
        $playstations = Playstation::all();
        $totalRevenue = [];
        $labels = [];
        $backgroundColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']; // Add more colors if needed
        $hoverBackgroundColors = ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#6c757d'];

        $revenue = Transaction::sum('total');

        foreach ($playstations as $index => $playstation) {
            $devices = Device::where('playstation_id', $playstation->id)->get();
            $sumRevenue = 0;
            foreach ($devices as $device) {
                $sumRevenue += Transaction::where('status_transaksi', 'sukses')->where('device_id', $device->id)->sum('total');
            }
            $percent = $revenue > 0 ? ($sumRevenue / $revenue) * 100 : 0;
            $labels[] = $playstation->nama . ' (' . round($percent, 1) . '%)';
            $totalRevenue[] = $sumRevenue;
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $totalRevenue,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($playstations)),
                    'hoverBackgroundColor' => array_slice($hoverBackgroundColors, 0, count($playstations)),
                    'hoverBorderColor' => "rgba(234, 236, 244, 1)",
                ]
            ]
        ];

        return $chartData;
    }

    public function areaCartData()
    {
        $totals = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $total = Transaction::whereMonth('created_at', $bulan)->where('status_transaksi', 'sukses')->sum('total');
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
