<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama',
        'device_id',
        'user_id',
        'harga',
        'jam_main',
        'total',
        'status_transaksi',
        'waktu_mulai',
        'waktu_Selesai',
        'status',
        'tipe_transaksi',
        'payment_status',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionFnbs()
    {
        return $this->hasMany(TransactionFnb::class, 'transaction_id', 'id');
    }

    public function getFnbTotalAttribute()
    {
        return $this->transactionFnbs->sum(function ($item) {
            return $item->qty * $item->harga_jual;
        });
    }
}
