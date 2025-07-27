<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeviceProgressLogs;

class Device extends Model
{
    /** @use HasFactory<\Database\Factories\DeviceFactory> */
    use HasFactory;

    protected $fillable = [
        'order_number',
        'client_name',
        'client_number',
        'device_type',
        'device_model',
        'serial_number',
        'imei',
        'issue_description',
        'repair_status',
        'created_at',
        'updated_at',
    ];

    public function progressLogs()
    {
        return $this->hasMany(DeviceProgressLogs::class, 'device_id');
    }
}
