<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'product_price',
        'quantity',
        'payment_id',
    ];

    public function payment(){

        return $this->belongsTo(Payment::class);
    }
}
