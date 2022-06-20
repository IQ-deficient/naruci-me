<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'product_id',
        'order_id',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('H:i d-m-Y');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('H:i d-m-Y');
    }
}
