<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends StoreDeviceRequest
{
    // extend all validation rules from StoreDeviceRequest
    public function rules(): array
    {
        $rules = parent::rules();
        return $rules;
    }
}
