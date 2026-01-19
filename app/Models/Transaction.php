<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_transaksi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'status',
        'member_id',
        'nama',
        'no_telepon',
        'device_id',
        'playstation_id',
        'user_id',
        'harga',
        'jam_main',
        'waktu_mulai',
        'waktu_Selesai',
        'total',
        'status_transaksi',
        'payment_status',
        'payment_method',
        'tipe_transaksi',
        'custom_package_id',
        'diskon',
        'lost_time_start',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function playstation()
    {
        return $this->belongsTo(Playstation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionFnbs()
    {
        return $this->hasMany(TransactionFnb::class, 'transaction_id', 'id_transaksi');
    }

    public function custom_package()
    {
        return $this->belongsTo(CustomPackage::class, 'custom_package_id');
    }

    public function getFnbTotalAttribute()
    {
        return $this->transactionFnbs->sum(function ($item) {
            return $item->qty * $item->harga_jual;
        });
    }
}
