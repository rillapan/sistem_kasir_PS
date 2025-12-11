<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Playstation;
use App\Models\Transaction;
use App\Models\Fnb;
use App\Models\TransactionFnb;
use App\Models\StockMutation;
use App\Models\CustomPackage;
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
        $transaction = Transaction::with(['device.playstation', 'transactionFnbs.fnb', 'user', 'custom_package.playstations', 'custom_package.fnbs'])->findOrFail($id);

        return view('transaction.payment', [
            'title' => 'Pembayaran Transaksi',
            'active' => 'transaction',
            'transaction' => $transaction
        ]);
    }

    public function printReceipt($id)
    {
        $transaction = Transaction::with(['device.playstation', 'transactionFnbs.fnb', 'user', 'custom_package.playstations', 'custom_package.fnbs'])->findOrFail($id);
        
        // Retrieve temporary payment data from session if available
        $amountPaid = session('amount_paid');
        $change = session('change');
        
        // If viewing from history (no session data), assume exact payment or calculate if we had the field
        if ($amountPaid === null) {
            // For older transactions or re-prints, we don't have the exact amount paid recorded on this table structure yet
            // So we display standard info
        } else {
            // Inject into transaction object temporarily for the view
            $transaction->bayar_nominal = $amountPaid;
            $transaction->kembalian = $change;
        }

        // Preserve flash messages for the next redirect (to index)
        session()->keep(['success', 'gagal']);

        return view('transaction.receipt', [
            'transaction' => $transaction
        ]);
    }

    public function processPayment(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        // Calculate final total after discount
        $discount = $request->input('discount', 0);
        
        // Only save discount if it's greater than 0
        $discountToSave = $discount > 0 ? $discount : null;
        $finalTotal = $transaction->total - ($transaction->total * $discount / 100);
        
        $request->validate([
            'payment_method' => 'required|in:tunai,e-wallet,transfer_bank',
            'amount_paid' => 'required|numeric|min:' . $finalTotal,
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $amountPaid = $request->input('amount_paid');
        $change = $amountPaid - $finalTotal;
        $paymentMethod = $request->input('payment_method');

        $updateData = [
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'status_transaksi' => 'selesai',
        ];

        // Only add diskon to update data if discount > 0
        if ($discount > 0) {
            $updateData['diskon'] = $discount;
        }

        $transaction->update($updateData);

        // Update device status to available
        if ($transaction->device) {
            $transaction->device->update(['status' => 'Tersedia']);
        }

        return redirect()->route('transaction.print', ['id' => $transaction->id_transaksi, 'action' => 'payment'])
            ->with([
                'success' => 'Pembayaran berhasil. Kembalian: Rp ' . number_format($change, 0, ',', '.'),
                'amount_paid' => $amountPaid,
                'change' => $change
            ]);
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

        $transaction = $query->latest()->paginate(10);

        // Calculate counts for transaction types and payment statuses
        $postpaidCount = Transaction::where('tipe_transaksi', 'postpaid')->count();
        $prepaidCount = Transaction::where('tipe_transaksi', 'prepaid')->count();
        $customPackageCount = Transaction::where('tipe_transaksi', 'custom_package')->count();
        $paidCount = Transaction::where('payment_status', 'paid')->count();
        $unpaidCount = Transaction::where('payment_status', 'unpaid')->count();

        // Calculate payment method breakdown for paid transactions
        $paymentMethodCounts = [
            'tunai' => Transaction::where('payment_status', 'paid')->where('payment_method', 'tunai')->count(),
            'e-wallet' => Transaction::where('payment_status', 'paid')->where('payment_method', 'e-wallet')->count(),
            'transfer_bank' => Transaction::where('payment_status', 'paid')->where('payment_method', 'transfer_bank')->count(),
        ];

        $paymentMethodTotals = [
            'tunai' => Transaction::where('payment_status', 'paid')->where('payment_method', 'tunai')->sum('total'),
            'e-wallet' => Transaction::where('payment_status', 'paid')->where('payment_method', 'e-wallet')->sum('total'),
            'transfer_bank' => Transaction::where('payment_status', 'paid')->where('payment_method', 'transfer_bank')->sum('total'),
        ];

        return view('transaction.index', [
            'title' => 'Data Tansaksi',
            'active' => 'transaction',
            'transactions' => $transaction,
            'currentType' => $type,
            'postpaidCount' => $postpaidCount,
            'prepaidCount' => $prepaidCount,
            'customPackageCount' => $customPackageCount,
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
            'paymentMethodCounts' => $paymentMethodCounts,
            'paymentMethodTotals' => $paymentMethodTotals,
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

        $device = Device::with('playstation')->where('status', 'Tersedia')->get();
        $noDevices = $device->isEmpty();
        $playstation = Playstation::all();
        $fnbs = Fnb::all();
        $customPackages = CustomPackage::active()->with(['playstations', 'fnbs'])->get();

        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        return view('transaction.create', [
            'title' => 'Create Transaction',
            'active' => 'transaction',
            'playstations' => $playstation,
            'devices' => $device,
            'noDevices' => $noDevices,
            'currentTime' => $currentTime,
            'fnbs' => $fnbs,
            'customPackages' => $customPackages
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
            'no_telepon' => 'nullable|string|max:20',
            'device_id' => 'required_if:tipe_transaksi,prepaid,postpaid,custom_package|exists:devices,id',
            'custom_package_id' => 'required_if:tipe_transaksi,custom_package|exists:custom_packages,id',
            'user_id' => 'nullable|exists:users,id',
            'harga' => 'required_if:tipe_transaksi,prepaid,custom_package|nullable',
            'jam_main' => 'required_if:tipe_transaksi,prepaid|nullable|max:2',
            'total' => 'required',
            'status_transaksi' => 'nullable',
            'tipe_transaksi' => 'required|in:prepaid,postpaid,custom_package',
            'fnb_ids.*' => 'nullable|exists:fnbs,id',
            'fnbs_qty.*' => 'nullable|integer|min:1',
            'fnbs_harga.*' => 'nullable|integer|min:0'
        ]);

        $validatedData['status'] = 'user'; // Provide default value for status
        $validatedData['waktu_mulai'] = $start_time;
        $validatedData['tipe_transaksi'] = $request->tipe_transaksi;

        if ($request->tipe_transaksi === 'custom_package') {
            $customPackage = CustomPackage::with(['playstations', 'fnbs'])->findOrFail($request->custom_package_id);

            // Validate that device_id is provided and exists
            if (!$request->device_id) {
                return redirect()->back()->with('gagal', 'Silakan pilih perangkat terlebih dahulu.');
            }

            // Get the selected device and validate it belongs to the package's PlayStation types
            $selectedDevice = Device::findOrFail($request->device_id);
            $playstationIds = $customPackage->playstations->pluck('id');
            
            if (!$playstationIds->contains($selectedDevice->playstation_id)) {
                return redirect()->back()->with('gagal', 'Perangkat yang dipilih tidak sesuai dengan paket custom.');
            }

            if ($selectedDevice->status !== 'Tersedia') {
                return redirect()->back()->with('gagal', 'Perangkat yang dipilih tidak tersedia.');
            }

            $validatedData['device_id'] = $selectedDevice->id;
            $validatedData['custom_package_id'] = $customPackage->id;
            $validatedData['harga'] = $customPackage->harga_total;
            $validatedData['total'] = $customPackage->harga_total;
            $validatedData['status_transaksi'] = 'sukses';

            // Calculate total playtime from package
            $totalJamMain = $customPackage->playstations->sum('pivot.lama_main');
            $validatedData['jam_main'] = $totalJamMain;
            $validatedData['waktu_Selesai'] = Carbon::parse($start_time)->addMinutes($totalJamMain)->format('H:i');

        } elseif ($request->tipe_transaksi === 'prepaid') {
            $validatedData['waktu_Selesai'] = $end_time;
            $validatedData['jam_main'] = $jam_main;
            $validatedData['total'] = $request->total;
            $validatedData['status_transaksi'] = 'sukses';
        } elseif ($request->tipe_transaksi === 'postpaid') {
            // Postpaid (Lost Time) - device is now required
            $device = Device::findOrFail($request->device_id);
            if ($device->status !== 'Tersedia') {
                return redirect()->back()->with('gagal', 'Perangkat yang dipilih tidak tersedia.');
            }
            
            $validatedData['device_id'] = $device->id;
            $validatedData['harga'] = $device->playstation->harga ?? 0;
            $validatedData['waktu_Selesai'] = null;
            $validatedData['jam_main'] = null;
            $validatedData['status_transaksi'] = 'berjalan';
            $validatedData['lost_time_start'] = Carbon::now();
            // For postpaid, total is calculated from FnB items only (no device cost initially)
            $validatedData['total'] = $request->total ?? 0;
        }

        // Set user_id if not provided
        $validatedData['user_id'] = $validatedData['user_id'] ?? auth()->id();

        // Filter out FNB arrays for Transaction creation
        unset($validatedData['fnb_ids']);
        unset($validatedData['fnbs_qty']);
        unset($validatedData['fnbs_harga']);

        try {
            $transaction = Transaction::create($validatedData);
        } catch (\Exception $e) {
            return redirect()->back()->with('gagal', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }

        // Handle FnB items
        if ($request->tipe_transaksi === 'custom_package') {
            // For custom packages, add FnB items from the package
            $customPackage = CustomPackage::with('fnbs')->findOrFail($request->custom_package_id);
            foreach ($customPackage->fnbs as $fnb) {
                $qty = $fnb->pivot->quantity;

                // Check stock only if not unlimited (stok != -1)
                if ($fnb->stok != -1 && $fnb->stok < $qty) {
                    return back()->with('gagal', 'Stok FnB tidak cukup untuk ' . $fnb->nama . ' dalam paket custom');
                }

                TransactionFnb::create([
                    'transaction_id' => $transaction->id_transaksi,
                    'fnb_id' => $fnb->id,
                    'qty' => $qty,
                    'harga_jual' => $fnb->harga_jual
                ]);

                // Update stock only if not unlimited (stok != -1)
                if ($fnb->stok != -1) {
                    $fnb->decrement('stok', $qty);
                }

                // Create stock mutation
                StockMutation::create([
                    'fnb_id' => $fnb->id,
                    'type' => 'out',
                    'qty' => $qty,
                    'date' => now()->toDateString(),
                    'note' => 'Penjualan transaksi custom package #' . $transaction->id_transaksi
                ]);
            }
        } elseif ($request->has('fnb_ids')) {
            // For regular transactions, handle FnB from form
            foreach ($request->fnb_ids as $index => $fnb_id) {
                if ($fnb_id && $request->fnbs_qty[$index] > 0) {
                    $fnb = Fnb::findOrFail($fnb_id);

                    // Check stock only if not unlimited (stok != -1)
                    if ($fnb->stok != -1 && $fnb->stok < $request->fnbs_qty[$index]) {
                        return back()->with('gagal', 'Stok FnB tidak cukup untuk ' . $fnb->nama);
                    }

                    TransactionFnb::create([
                        'transaction_id' => $transaction->id_transaksi,
                        'fnb_id' => $fnb_id,
                        'qty' => $request->fnbs_qty[$index],
                        'harga_jual' => $request->fnbs_harga[$index] ?? $fnb->harga_jual
                    ]);

                    // Update stock only if not unlimited (stok != -1)
                    if ($fnb->stok != -1) {
                        $fnb->decrement('stok', $request->fnbs_qty[$index]);
                    }

                    // Create stock mutation
                    StockMutation::create([
                        'fnb_id' => $fnb_id,
                        'type' => 'out',
                        'qty' => $request->fnbs_qty[$index],
                        'date' => now()->toDateString(),
                        'note' => 'Penjualan transaksi #' . $transaction->id_transaksi
                    ]);
                }
            }
        }

        $device = Device::find($validatedData['device_id']);
        if ($device) {
            $device->update(['status' => 'Digunakan']);
        }

        // Handle different actions
        if ($request->action === 'bayar') {
            // Redirect to payment page for immediate payment
            return redirect()->route('transaction.showPayment', $transaction->id_transaksi);
        } else {
            // Save transaction and redirect to transaction list
            return redirect('transaction')->with('success', 'Data transaksi berhasil disimpan.');
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
        $transaction = Transaction::with(['device.playstation', 'custom_package'])->findOrFail($id);
        
        // Only allow editing unpaid transactions
        if ($transaction->payment_status !== 'unpaid') {
            return redirect()->route('transaction.index')->with('gagal', 'Hanya transaksi yang belum dibayar yang dapat diedit.');
        }
        
        // Get only available devices + the current device being used by this transaction
        $devices = Device::with('playstation')
            ->where(function($query) use ($transaction) {
                $query->where('status', 'Tersedia')
                      ->orWhere('id', $transaction->device_id);
            })
            ->get();
        
        // Get all custom packages for custom_package type
        $customPackages = CustomPackage::active()->with(['playstations', 'fnbs'])->get();
        
        return view('transaction.edit', [
            'title' => 'Edit Transaksi',
            'active' => 'transaction',
            'transaction' => $transaction,
            'devices' => $devices,
            'customPackages' => $customPackages
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
        $transaction = Transaction::findOrFail($id);
        
        // Only allow editing unpaid transactions
        if ($transaction->payment_status !== 'unpaid') {
            return redirect()->route('transaction.index')->with('gagal', 'Hanya transaksi yang belum dibayar yang dapat diedit.');
        }
        
        $validatedData = $request->validate([
            'tipe_transaksi' => 'required|in:prepaid,postpaid,custom_package',
            'device_id' => 'required|exists:devices,id',
            'jam_main' => 'nullable|integer|min:1',
            'custom_package_id' => 'nullable|exists:custom_packages,id'
        ]);
        
        $oldDeviceId = $transaction->device_id;
        $newDeviceId = $validatedData['device_id'];
        $oldTipeTransaksi = $transaction->tipe_transaksi;
        $newTipeTransaksi = $validatedData['tipe_transaksi'];
        
        // Get the new device
        $newDevice = Device::with('playstation')->findOrFail($newDeviceId);
        
        // Check if new device is available (or if it's the same device)
        if ($newDevice->status !== 'Tersedia' && $newDeviceId != $oldDeviceId) {
            return redirect()->back()->with('gagal', 'Perangkat yang dipilih tidak tersedia.');
        }
        
        // Prepare update data
        $updateData = [
            'device_id' => $newDeviceId,
            'tipe_transaksi' => $newTipeTransaksi,
        ];
        
        // Handle transaction type changes
        if ($newTipeTransaksi === 'postpaid') {
            // Changed to Lost Time
            $updateData['harga'] = $newDevice->playstation->harga ?? 0;
            $updateData['jam_main'] = null;
            $updateData['waktu_Selesai'] = null;
            $updateData['status_transaksi'] = 'berjalan';
            
            // Keep the original lost_time_start if it exists, otherwise set it based on waktu_mulai
            // This ensures timer continues from the original start time
            if (!$transaction->lost_time_start) {
                // Calculate lost_time_start from waktu_mulai to maintain consistency
                $waktuMulaiCarbon = Carbon::parse($transaction->created_at->toDateString() . ' ' . $transaction->waktu_mulai);
                $updateData['lost_time_start'] = $waktuMulaiCarbon;
            }
            // If lost_time_start already exists, don't change it - timer continues
            
            // For postpaid, total is FnB only initially (device cost calculated on end)
            $fnbTotal = $transaction->getFnbTotalAttribute();
            $updateData['total'] = $fnbTotal;

        } elseif ($newTipeTransaksi === 'prepaid') {
            // Changed to Paket OR updating existing Paket
            $jamMain = $validatedData['jam_main'] ?? 1;
            $hargaPerJam = $newDevice->playstation->harga ?? 0;
            $totalPs = $hargaPerJam * $jamMain;
            $fnbTotal = $transaction->getFnbTotalAttribute();
            
            $updateData['jam_main'] = $jamMain;
            $updateData['harga'] = $hargaPerJam;
            $updateData['total'] = $totalPs + $fnbTotal;
            $updateData['status_transaksi'] = 'sukses';
            
            // Calculate elapsed time from waktu_mulai
            $waktuMulaiCarbon = Carbon::parse($transaction->created_at->toDateString() . ' ' . $transaction->waktu_mulai);
            $now = Carbon::now();
            $elapsedMinutes = $waktuMulaiCarbon->diffInMinutes($now);
            
            // Calculate new end time: now + (jam_main in minutes - elapsed minutes)
            // This makes the timer show remaining time (countdown)
            $jamMainMinutes = $jamMain * 60;
            $remainingMinutes = $jamMainMinutes - $elapsedMinutes;
            
            if ($remainingMinutes > 0) {
                // There's still time remaining
                $updateData['waktu_Selesai'] = $now->copy()->addMinutes($remainingMinutes)->format('H:i');
            } else {
                // Time already exceeded, set end time to now (will be marked as overtime)
                $updateData['waktu_Selesai'] = $now->format('H:i');
            }
            
        } elseif ($newTipeTransaksi === 'custom_package') {
            // Changed to Custom Package
            $customPackageId = $validatedData['custom_package_id'] ?? null;
            
            if (!$customPackageId) {
                return redirect()->back()->with('gagal', 'Silakan pilih paket custom.');
            }
            
            $customPackage = CustomPackage::with(['playstations', 'fnbs'])->findOrFail($customPackageId);
            
            // Validate device is compatible with package
            $playstationIds = $customPackage->playstations->pluck('id');
            if (!$playstationIds->contains($newDevice->playstation_id)) {
                return redirect()->back()->with('gagal', 'Perangkat yang dipilih tidak sesuai dengan paket custom.');
            }
            
            $totalJamMain = $customPackage->playstations->sum('pivot.lama_main');
            
            $updateData['custom_package_id'] = $customPackageId;
            $updateData['harga'] = $customPackage->harga_total;
            $updateData['total'] = $customPackage->harga_total;
            $updateData['jam_main'] = $totalJamMain;
            $updateData['status_transaksi'] = 'sukses';
            
            // Calculate elapsed time from waktu_mulai
            $waktuMulaiCarbon = Carbon::parse($transaction->created_at->toDateString() . ' ' . $transaction->waktu_mulai);
            $now = Carbon::now();
            $elapsedMinutes = $waktuMulaiCarbon->diffInMinutes($now);
            
            // Calculate new end time: now + (package duration - elapsed time)
            $remainingMinutes = $totalJamMain - $elapsedMinutes;
            
            if ($remainingMinutes > 0) {
                $updateData['waktu_Selesai'] = $now->copy()->addMinutes($remainingMinutes)->format('H:i');
            } else {
                $updateData['waktu_Selesai'] = $now->format('H:i');
            }
        }

        
        // Update the transaction
        $transaction->update($updateData);
        
        // Handle device status changes
        if ($oldDeviceId != $newDeviceId) {
            // Release old device
            $oldDevice = Device::find($oldDeviceId);
            if ($oldDevice) {
                $oldDevice->update(['status' => 'Tersedia']);
            }
            
            // Mark new device as in use
            $newDevice->update(['status' => 'Digunakan']);
        }
        
        return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil diupdate. Timer tetap berjalan.');
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

        if ($transaction->status_transaksi !== 'berjalan') {
            return redirect('transaction')->with('gagal', 'Transaksi tidak dapat diakhiri.');
        }

        $waktuSelesai = Carbon::now();
        $totalFnb = $transaction->getFnbTotalAttribute();

        if ($transaction->tipe_transaksi === 'postpaid') {
            // Calculate duration and cost for postpaid (Lost Time) transactions
            $waktuMulai = Carbon::parse($transaction->created_at->toDateString() . ' ' . $transaction->waktu_mulai);
            $durationMinutes = $waktuMulai->diffInMinutes($waktuSelesai, false);

            if ($durationMinutes < 0) {
                // If somehow negative, perhaps next day
                $durationMinutes = 24 * 60 + $durationMinutes;
            }

            $costPerMinute = $transaction->harga / 60;
            $totalPs = $durationMinutes * $costPerMinute;
            
            // Apply specific rounding rules for lost time
            $roundedTotalPs = $this->applyLostTimeRounding($totalPs);
            $total = $roundedTotalPs + $totalFnb;

            $hours = floor($durationMinutes / 60);
            $minutes = $durationMinutes % 60;
            $formattedDuration = sprintf('%d jam %d menit', $hours, $minutes);

            $updateData = [
                'waktu_Selesai' => $waktuSelesai->format('H:i'),
                'jam_main' => $formattedDuration,
                'total' => $total,
            ];
        } else {
            // For prepaid transactions, just update the end time and status
            $updateData = [
                'waktu_Selesai' => $waktuSelesai->format('H:i'),
                'total' => $transaction->harga + $totalFnb,
            ];
        }

        // Common updates for both transaction types
        $updateData = array_merge($updateData, [
            'status_transaksi' => 'selesai',
            'payment_status' => 'unpaid'
        ]);

        $transaction->update($updateData);

        // Update device status to available
        $transaction->device->update(['status' => 'Tersedia']);

        // Redirect to payment page
        return redirect()->route('transaction.showPayment', $transaction->id_transaksi)
            ->with('success', 'Transaksi berhasil diakhiri. Silakan lakukan pembayaran.');
    }

    // app/Http/Controllers/TransactionController.php

    public function addOrder($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->payment_status !== 'unpaid') {
            return redirect()->route('transaction.index')->with('error', 'Hanya bisa menambahkan pesanan untuk transaksi yang belum dibayar.');
        }

        $fnbs = Fnb::where(function($query) {
            $query->where('stok', '>=', 1)
                  ->orWhere('stok', -1);
        })->get();

        // Calculate existing total (PS cost + existing FNB cost)
        $existingFnbTotal = $transaction->getFnbTotalAttribute();
        $existingTotal = $transaction->harga + $existingFnbTotal;

        return view('transaction.add-order', [
            'title' => 'Tambah Pesanan',
            'active' => 'transaction',
            'transaction' => $transaction,
            'fnbs' => $fnbs,
            'existingTotal' => $existingTotal
        ]);
    }

    public function storeOrder(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->payment_status !== 'unpaid') {
            return redirect()->route('transaction.index')->with('error', 'Hanya bisa menambahkan pesanan untuk transaksi yang belum dibayar.');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.fnb_id' => 'required|exists:fnbs,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $totalAdded = 0;
        
        // Process each item in the order
        foreach ($request->items as $item) {
            $fnb = Fnb::findOrFail($item['fnb_id']);
            $qty = (int)$item['qty'];
            
            // Check stock only if not unlimited (stok != -1)
            if ($fnb->stok != -1 && $fnb->stok < $qty) {
                return redirect()->back()
                    ->with('error', 'Stok ' . $fnb->nama . ' tidak mencukupi. Stok tersedia: ' . $fnb->stok);
            }

            // Check if this FNB is already in the transaction
            $transactionFnb = TransactionFnb::where('transaction_id', $transaction->id_transaksi)
                ->where('fnb_id', $fnb->id)
                ->first();

            if ($transactionFnb) {
                // Update existing FNB in the transaction
                $transactionFnb->qty += $qty;
                $transactionFnb->save();
            } else {
                // Create new transaction FNB
                $transactionFnb = new TransactionFnb([
                    'transaction_id' => $transaction->id_transaksi,
                    'fnb_id' => $fnb->id,
                    'qty' => $qty,
                    'harga_jual' => $fnb->harga_jual,
                    'harga_beli' => $fnb->harga_beli
                ]);
                $transactionFnb->save();
            }

            // Update FNB stock only if not unlimited (stok != -1)
            if ($fnb->stok != -1) {
                $fnb->stok -= $qty;
                $fnb->save();
            }

            // Create stock mutation
            StockMutation::create([
                'fnb_id' => $fnb->id,
                'type' => 'out',
                'qty' => $qty,
                'date' => now()->toDateString(),
                'note' => 'Penjualan - Transaksi #' . $transaction->id_transaksi
            ]);

            $totalAdded++;
        }

        // Recalculate the total for the transaction (PS cost + FNB total)
        $fnbTotal = TransactionFnb::where('transaction_id', $transaction->id_transaksi)
            ->selectRaw('SUM(qty * harga_jual) as total')
            ->value('total') ?? 0;

        $transaction->total = $transaction->harga + $fnbTotal;
        $transaction->save();

        return redirect()->route('transaction.showPayment', $transaction->id_transaksi)
            ->with('success', $totalAdded . ' item pesanan berhasil ditambahkan');
    }

    /**
     * Apply specific rounding rules for lost time pricing
     * If difference <= 500, round to 500
     * If difference > 500, round to 1000
     */
    private function applyLostTimeRounding($total)
    {
        // Find the nearest lower thousand
        $lowerThousand = floor($total / 1000) * 1000;
        
        // Calculate difference from the lower thousand
        $difference = $total - $lowerThousand;
        
        // Apply rounding rules
        if ($difference == 0) {
            // Already a perfect thousand
            return $total;
        } elseif ($difference <= 500) {
            // Round up to 500 (including exactly 500)
            return $lowerThousand + 500;
        } else {
            // Round up to next thousand
            return $lowerThousand + 1000;
        }
    }
}