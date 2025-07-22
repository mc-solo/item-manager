<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    /** @use HasFactory<\Database\Factories\DeviceFactory> */
    use HasFactory;

    protected $fillable = [
        'device_type',
        'brand',
        'model',
        'imei',
        'serial_number',
        'description',
        'repair_status',
        'owner_name',
        'owner_number',
        'payment_method',
        'payment_status',
        'price',
        'received_at',
        'expected_delivery_at',
    ];
}
