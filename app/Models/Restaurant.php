<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Restaurant extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => 'boolean'
    ];

    protected $fillable = [
        'name',
        'address',
        'user_id',
        'status'
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
