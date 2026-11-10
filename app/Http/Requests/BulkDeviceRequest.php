<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1|max:500',
            'ids.*' => 'integer|exists:devices,id',
            'action' => 'required|string|in:mark_pending,mark_in_progress,mark_completed,mark_cancelled,mark_delivered,delete',
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Select at least one device first.',
            'ids.max' => 'Select at most 500 devices at a time.',
            'action.in' => 'That bulk action is not supported.',
        ];
    }

    /** The repair status a bulk action maps to, or null for delete. */
    public function targetStatus(): ?string
    {
        return match ($this->input('action')) {
            'mark_pending' => 'pending',
            'mark_in_progress' => 'in_progress',
            'mark_completed' => 'completed',
            'mark_cancelled' => 'cancelled',
            'mark_delivered' => 'delivered',
            default => null,
        };
    }
}
