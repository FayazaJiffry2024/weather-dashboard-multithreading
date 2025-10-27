<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'temp',
        'feels_like',
        'condition',
        'humidity',
        'wind_speed',
        'recorded_at',
    ];
}
