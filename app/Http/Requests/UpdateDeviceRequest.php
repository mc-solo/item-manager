<?php

namespace App\Http\Requests;

class UpdateDeviceRequest extends StoreDeviceRequest
{
    // extend all validation rules from StoreDeviceRequest
    public function rules(): array
    {
        return parent::rules();
    }

    public function authorize(): bool
    {
        $device = $this->route('device');

        return $device
            ? ($this->user()?->can('update', $device) ?? false)
            : false;
    }
}
