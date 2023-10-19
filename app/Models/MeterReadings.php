<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReadings extends Model
{
    use HasFactory;

    public function meter()
    {
        return $this->belongsTo(Meter::class, 'meter_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
