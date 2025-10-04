<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Playstation;
use App\Models\Transaction;
use App\Models\Fnb;
use App\Models\TransactionFnb;
use App\Models\StockMutation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private function updateDeviceStatuses()
    {
        // This method is removed because DeviceController@index has more comprehensive status update logic
    }

    public function showPayment($id)
    {
        $transaction = Transaction::with(['device.playstation', 'transactionFnbs.fnb', 'user'])->findOrFail($id);

        return view('transaction.payment', [
            'title' => 'Pembayaran Transaksi',
            'active' => 'transaction',
            'transaction' => $transaction
        ]);
    }

    public function processPayment(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $request->validate([
            'amount_paid' => 'required|numeric|min:' . $transaction->total,
        ]);

        $amountPaid = $request->input('amount_paid');
        $change = $amountPaid - $transaction->total;

        $transaction->update([
            'payment_status' => 'paid',
            'status_transaksi' => 'selesai',
        ]);

        // Update device status to available
        $transaction->device->update(['status' => 'Tersedia']);

        return redirect('transaction')->with('success', 'Pembayaran berhasil. Kembalian: Rp ' . number_format($change, 0, ',', '.'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->updateDeviceStatuses();

        $type = $request->input('type', 'all');

        $query = Transaction::with('transactionFnbs');

        if (auth()->user()->status !== 'admin') {
            $query->where('user_id', auth()->user()->id);
        }

        if ($type !== 'all') {
            $query->where('tipe_transaksi', $type);
        }

        $transaction = $query->latest()->paginate(5);

        // Calculate counts for transaction types and payment statuses
        $postpaidCount = Transaction::where('tipe_transaksi', 'postpaid')->count();
        $prepaidCount = Transaction::where('tipe_transaksi', 'prepaid')->count();
        $paidCount = Transaction::where('payment_status', 'paid')->count();
        $unpaidCount = Transaction::where('payment_status', 'unpaid')->count();

        return view('transaction.index', [
            'title' => 'Data Tansaksi',
            'active' => 'transaction',
            'transactions' => $transaction,
            'currentType' => $type,
            'postpaidCount' => $postpaidCount,
            'prepaidCount' => $prepaidCount,
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Call DeviceController's status update logic to ensure device statuses are accurate
        app(\App\Http\Controllers\DeviceController::class)->index();

        $device = Device::where('status', 'Tersedia')->get();
        $noDevices = $device->isEmpty();
        $playstation = Playstation::all();
        $fnbs = Fnb::all();

        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        return view('transaction.create', [
            'title' => 'Create Transaction',
            'active' => 'transaction',
            'playstations' => $playstation,
            'devices' => $device,
            'noDevices' => $noDevices,
            'currentTime' => $currentTime,
            'fnbs' => $fnbs
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
        $tanggal = Carbon::now()->format('Y-m-d');
        $start_time = Carbon::now()->format('H:i');
        $jam_main = $request->jam_main;
        $end_time = Carbon::parse($start_time)->addHours((int)$jam_main)->format('H:i');
        if (auth()->user()->status !== 'admin' && ($start_time > '22:00' || $start_time < '08:00')) {
            $redirectUrl = $request->transaksi ? 'transaction' : 'booking/' . $request->device_id;
            return redirect($redirectUrl)->with('gagal', 'Maaf, rental sudah tutup!. Silahkan melakukan booking ketika rental sudah beroprasi kembali pada pukul 08:00.');
        }
        // Mengecek apakah waktu mulai dan waktu selesai sudah ada di database
        $existingTransaction = Transaction::where('device_id', $request->device_id)->whereDate('created_at', $tanggal)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->where(function ($query) use ($start_time) {
                    $query->where('waktu_mulai', '<=', $start_time)
                        ->where('waktu_Selesai', '>', $start_time);
                })
                    ->orWhere(function ($query) use ($end_time) {
                        $query->where('waktu_mulai', '<', $end_time)
                            ->where('waktu_Selesai', '>=', $end_time);
                    });
            })
            ->doesntExist();

        // Jika waktu mulai dan waktu selesai sudah ada di database, kembalikan response error
        // Removed notification to allow transactions for available devices


        $validatedData = $request->validate([
            'nama' => 'required',
            'device_id' => 'required',
            'user_id' => 'nullable|exists:users,id',
            'harga' => 'required',
            'jam_main' => 'nullable|max:2',
            'total' => 'nullable',
            'status_transaksi' => 'nullable',
            'tipe_transaksi' => 'required|in:prepaid,postpaid',
            'fnb_ids.*' => 'nullable|exists:fnbs,id',
            'fnbs_qty.*' => 'nullable|integer|min:1',
            'fnbs_harga.*' => 'nullable|integer|min:0'
        ]);

        $validatedData['status'] = 'user'; // Provide default value for status
        $validatedData['waktu_mulai'] = $start_time;
        $validatedData['tipe_transaksi'] = $request->tipe_transaksi;

        if ($request->tipe_transaksi === 'prepaid') {
            $validatedData['waktu_Selesai'] = $end_time;
            $validatedData['jam_main'] = $jam_main;
            $validatedData['total'] = $request->total;
            $validatedData['status_transaksi'] = 'sukses';
        } else {
            // Postpaid
            $validatedData['waktu_Selesai'] = null;
            $validatedData['jam_main'] = null;
            $validatedData['total'] = 0; // Will be calculated later
            $validatedData['status_transaksi'] = 'berjalan';
        }

        // Set user_id if not provided
        $validatedData['user_id'] = $validatedData['user_id'] ?? auth()->id();

        // Filter out FNB arrays for Transaction creation
        unset($validatedData['fnb_ids']);
        unset($validatedData['fnbs_qty']);
        unset($validatedData['fnbs_harga']);

        $transaction = Transaction::create($validatedData);

        // Handle FnB items
        if ($request->has('fnb_ids')) {
            foreach ($request->fnb_ids as $index => $fnb_id) {
                if ($fnb_id && $request->fnbs_qty[$index] > 0) {
                    $fnb = Fnb::findOrFail($fnb_id);
                    if ($fnb->stok < $request->fnbs_qty[$index]) {
                        return back()->with('gagal', 'Stok FnB tidak cukup untuk ' . $fnb->nama);
                    }

                    TransactionFnb::create([
                        'transaction_id' => $transaction->id,
                        'fnb_id' => $fnb_id,
                        'qty' => $request->fnbs_qty[$index],
                        'harga_jual' => $request->fnbs_harga[$index] ?? $fnb->harga_jual
                    ]);

                    // Update stock
                    $fnb->decrement('stok', $request->fnbs_qty[$index]);

                    // Create stock mutation
                    StockMutation::create([
                        'fnb_id' => $fnb_id,
                        'type' => 'out',
                        'qty' => $request->fnbs_qty[$index],
                        'date' => now()->toDateString(),
                        'note' => 'Penjualan transaksi #' . $transaction->id
                    ]);
                }
            }
        }

        $device = Device::find($request->device_id);
        $device->update(['status' => 'Digunakan']);

        if ($request->action === 'simpan') {
            // Save transaction for later payment
            return redirect('transaction')->with('success', 'Data transaksi berhasil disimpan untuk pembayaran nanti.');
        } elseif ($request->action === 'bayar') {
            // Redirect to payment page
            return redirect()->route('transaction.showPayment', $transaction->id);
        }

        if ($request->transaksi) {
            return redirect('transaction')->with('success', 'Data transaksi berhasil disimpan.');
        }

        return redirect('booking/' . $request->device_id)->with('success', 'Berhasil melakukan Booking.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::with(['device.playstation', 'transactionFnbs.fnb', 'user'])->findOrFail($id);

        return view('transaction.show', [
            'title' => 'Detail Transaksi',
            'active' => 'transaction',
            'transaction' => $transaction
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Transaction::findOrFail($id)->delete();

        return redirect('transaction')->with('success', 'Data transaksi berhasil dihapus.');
    }

    public function getHarga(Request $request)
    {
        $device = $request->query('device');

        $devices = Device::find($device);
        if (!$devices) {
            return response()->json(['harga' => 0]);
        }

        $harga = Playstation::find($devices->playstation_id);
        if (!$harga) {
            return response()->json(['harga' => 0]);
        }

        return response()->json(['harga' => $harga->harga]);
    }

    public function updateStatus(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['status_transaksi' => $request->status_transaksi]);

        $deviceStatus = $request->status;
        if ($request->status_transaksi == 'selesai') {
            $deviceStatus = 'Tersedia';
        }
        Device::where('id', $request->device_id)->update(['status' => $deviceStatus]);

        return redirect('transaction')->with('success', 'Status transaksi berhasil diupdate.');
    }

    public function endTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);

        if (auth()->user()->status !== 'admin' && $transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($transaction->tipe_transaksi !== 'postpaid' || $transaction->status_transaksi !== 'berjalan') {
            return redirect('transaction')->with('gagal', 'Transaksi tidak dapat diakhiri.');
        }

        $waktuMulai = Carbon::parse($transaction->created_at->toDateString() . ' ' . $transaction->waktu_mulai);
        $waktuSelesai = Carbon::now();
        $durationMinutes = $waktuMulai->diffInMinutes($waktuSelesai, false); // false to get float

        if ($durationMinutes < 0) {
            // If somehow negative, perhaps next day
            $durationMinutes = 24 * 60 + $durationMinutes;
        }

        $costPerMinute = $transaction->harga / 60;
        $totalPs = $durationMinutes * $costPerMinute;
        $roundedTotalPs = ceil($totalPs / 1000) * 1000; // Round up to nearest 1000
        $totalFnb = $transaction->getFnbTotalAttribute();
        $total = $roundedTotalPs + $totalFnb;

        $hours = floor($durationMinutes / 60);
        $minutes = $durationMinutes % 60;
        $formattedDuration = sprintf('%d jam %d menit', $hours, $minutes);

        $transaction->update([
            'waktu_Selesai' => $waktuSelesai->format('H:i'),
            'jam_main' => $formattedDuration, // Format duration as "X jam Y menit"
            'total' => $total,
            'status_transaksi' => 'selesai',
            'payment_status' => 'unpaid'
        ]);

        // Update device status to available
        $transaction->device->update(['status' => 'Tersedia']);

        return redirect()->route('transaction.showPayment', $transaction->id)->with('success', 'Transaksi postpaid berhasil diakhiri. Silakan lakukan pembayaran.');
    }
}
