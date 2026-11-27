<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'client_name' => $this->client_name,
            'client_number' => $this->client_number,
            'device_type' => $this->device_type,
            'device_model' => $this->device_model,
            'serial_number' => $this->serial_number,
            'imei' => $this->imei,
            'issue_description' => $this->issue_description,
            'repair_status' => $this->repair_status,
            'amount' => $this->amount !== null ? (float) $this->amount : null,
            'assigned_to' => $this->assigned_to,
            'assigned_technician' => $this->whenLoaded('assignedTechnician', function () {
                return $this->assignedTechnician ? [
                    'id' => $this->assignedTechnician->id,
                    'name' => $this->assignedTechnician->name,
                    'username' => $this->assignedTechnician->username,
                    'role' => $this->assignedTechnician->role,
                ] : null;
            }),
            'progress_logs' => $this->whenLoaded('progressLogs', function () {
                return $this->progressLogs->map(fn ($log) => [
                    'id' => $log->id,
                    'progress_description' => $log->progress_description,
                    'status' => $log->status,
                    'created_at' => $log->created_at?->toDateTimeString(),
                    'updated_at' => $log->updated_at?->toDateTimeString(),
                ])->values()->all();
            }),
            'transactions' => $this->whenLoaded('transaction', function () {
                return $this->transaction->map(fn ($transaction) => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount !== null ? (float) $transaction->amount : null,
                    'created_at' => $transaction->created_at?->toDateTimeString(),
                    'updated_at' => $transaction->updated_at?->toDateTimeString(),
                ])->values()->all();
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
