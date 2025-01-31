<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paket extends Model
{
    /** @use HasFactory<\Database\Factories\PaketFactory> */
    use HasFactory;
    protected $fillable = ['jenis_paket', 'deskripsi', 'harga'];

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
