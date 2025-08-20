<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Device;
use Illuminate\Support\Facades\Redirect;

class DeviceActions extends Component
{
    public $deviceId;
    public $devices;
    public $device;
    public $view = 'index'; // index, show, edit, create
    public $search = '';

    public $form = [
        'client_name' => '',
        'client_number' => '',
        'device_type' => '',
        'device_model' => '',
        'serial_number' => '',
        'imei' => '',
        'issue_description' => '',
        'repair_status' => '',
    ];

    public function mount($device = null)
    {
        if (request()->routeIs('device.show')) {
            $this->device = Device::with('progressLogs')->findOrFail(request()->route('device'));
            $this->view = 'show';
        } elseif (request()->routeIs('device.edit')) {
            $this->device = Device::findOrFail(request()->route('device'));
            $this->form = $this->device->toArray();
            $this->view = 'edit';
        } elseif (request()->routeIs('device.create')) {
            $this->view = 'create';
        } else {
            $this->devices = Device::with('progressLogs')->get();
            $this->view = 'index';
        }
        // search init
        $this->search = '';
    }

    public function updatedSearch()
    {
        $this->devices = Device::with('progressLogs')
            ->where('client_number', 'like', "%{$this->search}%")
            ->get();
    }

    public function store()
    {
        $validated = $this->validate([
            'form.client_name' => 'required|string|max:100',
            'form.client_number' => 'required|string|max:15',
            'form.device_type' => 'required|in:mobile,laptop,tablet,pc,mac,other',
            'form.device_model' => 'required|string|max:100',
            'form.serial_number' => 'nullable|string|max:100',
            'form.imei' => 'nullable|string|max:100',
            'form.issue_description' => 'nullable|string|max:500',
            'form.repair_status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);
        do {
            $orderNumber = 'TPB-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (Device::where('order_number', $orderNumber)->exists());
        $validated['form']['order_number'] = $orderNumber;
        $device = Device::create($validated['form']);
        session()->flash('message', 'Device created successfully.');
        return redirect()->route('device.show', $device);
    }

    public function update()
    {
        $validated = $this->validate([
            'form.client_name' => 'required|string|max:100',
            'form.client_number' => 'required|string|max:15',
            'form.device_type' => 'required|in:mobile,laptop,tablet,pc,mac,other',
            'form.device_model' => 'required|string|max:100',
            'form.serial_number' => 'nullable|string|max:100',
            'form.imei' => 'nullable|string|max:100',
            'form.issue_description' => 'nullable|string|max:500',
            'form.repair_status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);
        $this->device->update($validated['form']);
        session()->flash('message', 'Device updated successfully.');
        return redirect()->route('device.show', $this->device);
    }

    public function showDetails($id)
    {
        return redirect()->route('device.show', ['device' => $id]);
    }

    public function deleteDevice($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();
        session()->flash('message', 'Device deleted successfully.');
        return redirect()->route('devices.index');
    }

    public function render()
    {
        // Use route-based view selection for Livewire
        if (request()->routeIs('device.show')) {
            return view('livewire.devices.show', ['device' => $this->device]);
        } elseif (request()->routeIs('device.edit')) {
            return view('livewire.devices.edit', ['device' => $this->device, 'form' => $this->form]);
        } elseif (request()->routeIs('device.create')) {
            return view('livewire.devices.device-create', ['form' => $this->form]);
        } else {
            return view('livewire.devices.index', ['devices' => $this->devices]);
        }
    }
}
