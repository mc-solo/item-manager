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

    public const STATUSES = ['pending', 'in_progress', 'completed', 'cancelled', 'delivered'];

    public const DEVICE_TYPES = ['mobile', 'laptop', 'tablet', 'pc', 'mac', 'other'];

    public const DATE_RANGES = ['today', 'yesterday', 'this_week', 'last_week', 'this_month', 'last_month', 'custom'];

    /**
     * Scope a query to only include devices within a given date range.
     *
     * Fixed: last_week used to return the current week and last_month
     * the current month. Now last_week is the previous Monday to Sunday
     * and last_month is the previous calendar month. Custom ranges use
     * $from / $to (Y-m-d dates, inclusive).
     */
    public function scopeDateRange($query, $dateRange, $from = null, $to = null)
    {
        if (!$dateRange) {
            return $query;
        }

        switch ($dateRange) {
            case 'today':
                return $query->whereDate('created_at', today());
            case 'yesterday':
                return $query->whereDate('created_at', today()->subDay());
            case 'this_week':
                return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            case 'last_week':
                $start = now()->subWeek()->startOfWeek();
                $end = now()->subWeek()->endOfWeek();

                return $query->whereBetween('created_at', [$start, $end]);
            case 'this_month':
                return $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            case 'last_month':
                $start = now()->subMonthNoOverflow()->startOfMonth();
                $end = now()->subMonthNoOverflow()->endOfMonth();

                return $query->whereBetween('created_at', [$start, $end]);
            case 'custom':
                if ($from) {
                    $query->whereDate('created_at', '>=', $from);
                }
                if ($to) {
                    $query->whereDate('created_at', '<=', $to);
                }

                return $query;
            default:
                return $query;
        }
    }

    /**
     * Advanced search across every field a shop user would look a
     * device up by: client, order number, model, serial, IMEI and issue.
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('client_number', 'like', "%{$search}%")
                ->orWhere('client_name', 'like', "%{$search}%")
                ->orWhere('order_number', 'like', "%{$search}%")
                ->orWhere('device_model', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhere('imei', 'like', "%{$search}%")
                ->orWhere('issue_description', 'like', "%{$search}%");
        });
    }

    public function scopeDeviceType($query, $deviceType)
    {
        if ($deviceType) {
            return $query->where('device_type', $deviceType);
        }

        return $query;
    }

    public function scopeAmountRange($query, $min = null, $max = null)
    {
        if ($min !== null && $min !== '') {
            $query->where('amount', '>=', $min);
        }
        if ($max !== null && $max !== '') {
            $query->where('amount', '<=', $max);
        }

        return $query;
    }

    public function transaction() {
        return $this->hasMany(Transaction::class, 'device_id');
    }
}
