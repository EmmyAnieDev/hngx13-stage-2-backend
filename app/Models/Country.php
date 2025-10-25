<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capital',
        'region',
        'population',
        'currency_code',
        'exchange_rate',
        'estimated_gdp',
        'flag_url',
        'last_refreshed_at',
    ];

    protected $casts = [
        'population' => 'integer',
        'exchange_rate' => 'float',
        'estimated_gdp' => 'float',
        'last_refreshed_at' => 'datetime:Y-m-d\TH:i:s\Z',
    ];

    // Hide timestamps from API
    protected $hidden = ['created_at', 'updated_at'];
}
