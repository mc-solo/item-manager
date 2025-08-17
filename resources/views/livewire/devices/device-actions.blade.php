<!-- these are my action buttons for my devices -->
<!-- will link them latter with the appropriate controller methods and forms -->

<div class="flex gap-2">
    <a href="{{ route('device.show', $device) }}"
        class="rounded-md px-2 py-1 text-xs font-medium bg-blue-200 text-black hover:bg-green-300 cursor-pointer transition">Details</a>
    <form method="POST" action="{{ route('device.destroy', $device) }}"
        onsubmit="return confirm('Are you sure you want to delete this device?');">
        @csrf
        @method('DELETE')
        <button type="submit"
            class="rounded-md px-2 py-1 text-xs font-medium bg-red-700 text-white hover:bg-red-600 cursor-pointer transition">Delete</button>
    </form>
</div>