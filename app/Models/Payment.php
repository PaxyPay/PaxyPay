<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'due_date',
        'client_name',
        'description',
        'token',
        'product_id',
        'product_name',
        'active',
        'image',
        'product_price',
        'quantity',
        'user_id'
    ];

    public function products(){

       return $this->hasMany(Product::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function getStatusValues(){
        return ['active','paid', 'rejected', 'sospended'];
    }

}
