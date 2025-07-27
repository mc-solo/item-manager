<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Device;


class DeviceProgressLogs extends Model
{
    protected $fillable = [
        'device_id',
        'progress_description',
        'status',
        'created_at',
        'updated_at',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
