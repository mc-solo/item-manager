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
        'amount',
        'created_at',
        'updated_at',
    ];

    public function progressLogs()
    {
        return $this->hasMany(DeviceProgressLogs::class, 'device_id');
    }

    /**
     * Validate the client number format so that it's either from Safaricom or Ethio Telecom
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
        // remove any non-numeric characters, i hate to use a regex
        $number = preg_replace('/[^0-9]/', '', $value);
        $this->attributes['client_number'] = $number;
    }

    /**
     * Scope a query to only include devices of a given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('repair_status', $status);
        }
        return $query;
    }

    /**
     * Scope a query to only include devices within a given date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $dateRange
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $dateRange)
    {
        if (!$dateRange) {
            return $query;
        }

        switch ($dateRange) {
            case 'today':
                return $query->whereDate('created_at', today());
            case 'yesterday':
                return $query->whereDate('created_at', today()->subDay());
            case 'last_week':
                return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            case 'last_month':
                return $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            default:
                return $query;
        }
    }
}
