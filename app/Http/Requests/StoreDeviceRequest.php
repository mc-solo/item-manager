<?php

namespace App\Http\Requests;

use App\Models\Device;
use App\Models\User;
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
            // Assignment: technician type first, then the specific
            // technician of that type. `assign_role` is a form-only
            // field, it is not a column on devices, so the controller
            // unsets it before creating the device.
            'assign_role' => 'nullable|string|in:' . implode(',', User::TECHNICIAN_ROLES),
            'assigned_to' => 'nullable|integer|exists:users,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $assignedTo = $this->input('assigned_to');

            if ($assignedTo) {
                $technician = User::find($assignedTo);

                if ($technician && ! $technician->isTechnician()) {
                    $validator->errors()->add('assigned_to', 'The assigned user must be a technician.');
                }

                // The assigned user must be of the chosen technician type.
                if ($technician && $this->has('assign_role') && $this->input('assign_role') && $technician->role !== $this->input('assign_role')) {
                    $validator->errors()->add('assigned_to', 'The assigned technician must be a ' . User::roleLabel($this->input('assign_role')) . '.');
                }
            }

            // Only the create form sends assign_role, so this required
            // check does not fire on edit, where the form has no
            // assignment fields. If technicians of the chosen type
            // exist, one must be chosen; if none exist yet, the order
            // may be created unassigned.
            if ($this->has('assign_role') && ! $assignedTo) {
                $assignRole = $this->input('assign_role', User::ROLE_HARDWARE_TECHNICIAN);

                if ($assignRole && User::where('role', $assignRole)->exists()) {
                    $validator->errors()->add('assigned_to', 'Please choose a ' . User::roleLabel($assignRole) . '.');
                }
            }
        });
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
