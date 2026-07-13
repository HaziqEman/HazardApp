<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hazard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'hazard_category',
        'hazard_description',
        'latitude',
        'longitude',
        'location_name',
        'device_info',
        'reported_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
    ];
}
