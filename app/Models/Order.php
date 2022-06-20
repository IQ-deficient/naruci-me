<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => 'boolean'
    ];

    protected $fillable = [
        'status',
        'user_id',
        'bill',
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
