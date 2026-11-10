<?php

namespace App\Http\Requests;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Device::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'client_number' => [
                'required',
                'string',
                'regex:/^0[79]\d{8}$/',
                'max:10',
            ],
            'amount' => 'required|numeric|min:0|max:999999.99',
            'device_type' => 'required|in:mobile,laptop,tablet,pc,mac,other',
            'device_model' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:100',
            'imei' => 'nullable|string|max:100',
            'issue_description' => 'nullable|string|max:500',
            'repair_status' => 'required|in:pending,in_progress,completed,cancelled,delivered',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_number.regex' => 'The phone number must be a valid Ethiopian mobile number starting with 09 or 07 and 10 digits long.',
        ];
    }
}
