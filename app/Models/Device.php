<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeviceProgressLogs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    /**
     * Validate the client number format.
     *
     * @param  string  $value
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateClientNumber($value)
    {
        $validator = Validator::make(
            ['client_number' => $value],
            ['client_number' => 'required|string|regex:/^0[79]\d{8}$/'],
            ['client_number.regex' => 'The phone number must be a valid Ethiopian mobile number starting with 09 or 07 and 10 digits long.']
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Set the client number attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setClientNumberAttribute($value)
    {
        // Remove any non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $value);
        $this->attributes['client_number'] = $number;
    }
}
