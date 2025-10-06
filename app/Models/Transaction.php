<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'device_id',
        'type',
        'amount',
        'created_at',
        'updated_at',
    ];

    public function device() {
        return $this->belongsTo(Device::class);
    }
}
