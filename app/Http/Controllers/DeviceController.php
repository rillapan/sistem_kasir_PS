<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Member;
use App\Models\Playstation;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    /**
     * Display devices by playstation type.
     *
     * @param  int  $playstationId
     * @return \Illuminate\Http\Response
     */
    public function byPlaystation($playstationId)
    {
        $playstation = Playstation::findOrFail($playstationId);
        $devices = Device::whereHas('playstations', function ($query) use ($playstationId) {
            $query->where('playstations.id', $playstationId);
        })
            ->with(['playstations', 'playstation'])
            ->paginate(10);

        $title = 'Devices - ' . $playstation->nama;
        $active = 'device'; // This will highlight the 'Data Perangkat' menu in the sidebar
        
        return view('device.by_playstation', compact('devices', 'title', 'playstation', 'active'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        // Update status for all devices based on transactions today
        $allDevices = Device::all();
        foreach ($allDevices as $device) {
            $transaction = Transaction::where('device_id', $device->id)
                ->whereDate('created_at', $today)
                ->latest()
                ->first();

            $newStatus = 'Tersedia';
            if ($transaction) {
                if ($transaction->tipe_transaksi === 'prepaid') {
                    $endTime = $transaction->waktu_Selesai;
                    if ($endTime) {
                        // Get the transaction start time and date
                        $startTime = $transaction->waktu_mulai;
                        $transactionDate = $transaction->created_at->format('Y-m-d');

                        // Create proper datetime objects for start and end times
                        $startDateTime = Carbon::parse($transactionDate . ' ' . $startTime);
                        $endDateTime = Carbon::parse($transactionDate . ' ' . $endTime);

                        // If end time is earlier than start time, it means it's the next day
                        if ($endDateTime < $startDateTime) {
                            $endDateTime->addDay();
                        }

                        // Compare with current time
                        if ($endDateTime > $now) {
                            $newStatus = 'Digunakan';
                        } else {
                            $newStatus = 'Tersedia';
                        }
                    } else {
                        $newStatus = 'Tersedia';
                    }
                } elseif ($transaction->tipe_transaksi === 'postpaid') {
                    if ($transaction->status_transaksi === 'berjalan') {
                        $newStatus = 'Digunakan';
                    } else {
                        $newStatus = 'Tersedia';
                    }
                } elseif ($transaction->tipe_transaksi === 'custom_package') {
                    $endTime = $transaction->waktu_Selesai;
                    if ($endTime) {
                        // Get the transaction start time and date
                        $startTime = $transaction->waktu_mulai;
                        $transactionDate = $transaction->created_at->format('Y-m-d');

                        // Create proper datetime objects for start and end times
                        $startDateTime = Carbon::parse($transactionDate . ' ' . $startTime);
                        $endDateTime = Carbon::parse($transactionDate . ' ' . $endTime);

                        // If end time is earlier than start time, it means it's the next day
                        if ($endDateTime < $startDateTime) {
                            $endDateTime->addDay();
                        }

                        // Compare with current time
                        if ($endDateTime > $now) {
                            $newStatus = 'Digunakan';
                        } else {
                            $newStatus = 'Tersedia';
                        }
                    } else {
                        $newStatus = 'Tersedia';
                    }
                }
            }
            $device->update(['status' => $newStatus]);
        }

        $latestTransactionsSub = Transaction::select('device_id', \DB::raw('MAX(created_at) as latest_transaction_at'))
            ->groupBy('device_id');

        $devices = Device::select('devices.*')
            ->leftJoinSub($latestTransactionsSub, 'latest_transactions', 'devices.id', '=', 'latest_transactions.device_id')
            ->orderByRaw('CASE WHEN devices.status = "Digunakan" THEN 0 ELSE 1 END')
            ->orderBy('latest_transactions.latest_transaction_at', 'desc')
            ->with('playstations')
            ->paginate(12);

        // Count devices by status
        $countAvailable = Device::where('status', 'Tersedia')->count();
        $countInUse = Device::where('status', 'Digunakan')->count();

        // Get device names by status
        $availableDevices = Device::where('status', 'Tersedia')->pluck('nama')->toArray();
        $inUseDevices = Device::where('status', 'Digunakan')->pluck('nama')->toArray();

        // Collect end times and transaction info for in-use devices
        $timers = [];
        $customers = [];
        $postpaidTransactions = [];
        
        foreach ($devices as $dev) {
            if ($dev->status === 'Digunakan') {
                $transaction = Transaction::where('device_id', $dev->id)
                    ->whereDate('created_at', $today)
                    ->latest()
                    ->first();
                    
                if ($transaction) {
                    // For prepaid transactions
                    if ($transaction->tipe_transaksi === 'prepaid' && $transaction->waktu_Selesai) {
                        // Get the transaction start time and date
                        $startTime = $transaction->waktu_mulai;
                        $transactionDate = $transaction->created_at->format('Y-m-d');
                        
                        // Create proper datetime objects for start and end times
                        $startDateTime = Carbon::parse($transactionDate . ' ' . $startTime);
                        $endDateTime = Carbon::parse($transactionDate . ' ' . $transaction->waktu_Selesai);
                        
                        // If end time is earlier than start time, it means it's the next day
                        if ($endDateTime < $startDateTime) {
                            $endDateTime->addDay();
                        }
                        
                        // Only set timer if the transaction is still active
                        if ($endDateTime > $now) {
                            $timers[$dev->id] = [
                                'end_time' => $transaction->waktu_Selesai,
                                'end_date' => $endDateTime->format('Y-m-d'),
                                'start_date' => $transactionDate,
                                'start_time' => $startTime
                            ];
                        }
                    }
                    // For custom package transactions
                    elseif ($transaction->tipe_transaksi === 'custom_package' && $transaction->waktu_Selesai) {
                        // Get the transaction start time and date
                        $startTime = $transaction->waktu_mulai;
                        $transactionDate = $transaction->created_at->format('Y-m-d');
                        
                        // Create proper datetime objects for start and end times
                        $startDateTime = Carbon::parse($transactionDate . ' ' . $startTime);
                        $endDateTime = Carbon::parse($transactionDate . ' ' . $transaction->waktu_Selesai);
                        
                        // If end time is earlier than start time, it means it's the next day
                        if ($endDateTime < $startDateTime) {
                            $endDateTime->addDay();
                        }
                        
                        // Only set timer if the transaction is still active
                        if ($endDateTime > $now) {
                            $timers[$dev->id] = [
                                'end_time' => $transaction->waktu_Selesai,
                                'end_date' => $endDateTime->format('Y-m-d'),
                                'start_date' => $transactionDate,
                                'start_time' => $startTime
                            ];
                        }
                    }
                    // For postpaid transactions that are still running
                    elseif ($transaction->tipe_transaksi === 'postpaid' && $transaction->status_transaksi === 'berjalan') {
                        $postpaidTransactions[$dev->id] = [
                            'start_time' => $transaction->waktu_mulai,
                            'start_date' => $transaction->created_at->format('Y-m-d'),
                            'lost_time_start' => $transaction->lost_time_start
                        ];
                    }
                    
                    $customers[$dev->id] = [
                        'id_transaksi' => $transaction->id_transaksi,
                        'nama' => $transaction->nama ?? 'Tidak tersedia',
                        'nama_perangkat' => $dev->nama,
                        'jenis_playstation' => $dev->playstation_names ?? 'Tidak Diketahui',
                        'jam_main' => $transaction->jam_main ?? 'Tidak tersedia',
                        'waktu_mulai' => $transaction->waktu_mulai,
                        'waktu_selesai' => $transaction->waktu_Selesai,
                        'total' => $transaction->total ?? 'Tidak tersedia',
                        'tanggal' => $transaction->created_at->format('Y-m-d'),
                        'tipe_transaksi' => $transaction->tipe_transaksi,
                        'status_transaksi' => $transaction->status_transaksi
                    ];
                }
            }
        }

        return view('device.index', [
            'title' => 'Data Perangkat',
            'active' => 'device',
            'devices' => $devices,
            'timers' => $timers,
            'customers' => $customers,
            'postpaidTransactions' => $postpaidTransactions,
            'countAvailable' => $countAvailable,
            'countInUse' => $countInUse,
            'availableDevices' => $availableDevices,
            'inUseDevices' => $inUseDevices
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $play = Playstation::all();
        return view('device.create', [
            'title' => 'Tambah Perangkat',
            'active' => 'device',
            'plays' => $play
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
            'playstation_ids' => 'required|array|min:1',
            'playstation_ids.*' => 'exists:playstations,id',
            'status' => 'required'
        ]);

        try {
            // Create the device
            $device = Device::create([
                'nama' => $validatedData['nama'],
                'status' => $validatedData['status'],
                'playstation_id' => $validatedData['playstation_ids'][0] // Keep for backward compatibility
            ]);

            // Attach the device to multiple PlayStations
            $device->playstations()->attach($validatedData['playstation_ids']);

            return redirect('device')->with('success', 'Data perangkat berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan perangkat: ' . $e->getMessage());
        }
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
        $device = Device::with('playstations')->find($id);
        $playstation = Playstation::all();
        return view('device.edit', [
            'title' => 'Edit Perangkat',
            'active' => 'device',
            'device' => $device,
            'playstations' => $playstation
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
        $validatedData = $request->validate([
            'nama' => 'required|min:3',
            'playstation_ids' => 'required|array|min:1',
            'playstation_ids.*' => 'exists:playstations,id',
            'status' => 'required'
        ]);

        $device = Device::find($id);
        
        // Update device basic info
        $device->update([
            'nama' => $validatedData['nama'],
            'status' => $validatedData['status'],
            'playstation_id' => $validatedData['playstation_ids'][0] // Keep for backward compatibility
        ]);

        // Update PlayStation relationships
        $device->playstations()->sync($validatedData['playstation_ids']);

        return redirect('device')->with('success', 'Data perangkat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Device::destroy($id);

        return redirect('device')->with('success', 'Data perangkat berhasil dihapus.');
    }

    public function booking($id)
    {
        $tanggal = Carbon::now()->format('Y-m-d');
        $device = Device::find($id);
        $transactions = Transaction::where('device_id', $id)->whereDate('created_at', $tanggal)->paginate(5);
        // ddd($transactions);
        return view('device.booking', [
            'title' => 'Booking',
            'active' => 'device',
            'transactions' => $transactions,
            'device' => $device
        ]);
    }

    public function bookingAdd($id)
    {
        $device = Device::find($id);
        $playstation = Playstation::all();
        return view('device.booking-add', [
            'title' => 'Add Booking',
            'active' => 'device',
            'playstations' => $playstation,
            'device' => $device,
        ]);
    }

    public function updateStatusAjax($id)
    {
        $device = Device::find($id);
        if ($device) {
            $device->update(['status' => 'Tersedia']);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}
