<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fnb extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga_beli',
        'harga_jual',
        'stok',
        'deskripsi',
    ];

    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class);
    }

    public function transactionFnbs()
    {
        return $this->hasMany(TransactionFnb::class);
    }
}
