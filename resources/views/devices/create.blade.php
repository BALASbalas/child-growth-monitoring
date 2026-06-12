<x-app-layout>
    <x-slot name="header">Add New Device</x-slot>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('devices.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700">Device Name *</label>
                        <input type="text" name="device_name" value="{{ old('device_name') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Serial Number *</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Device Type</label>
                        <select name="device_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="digital_scale">Digital Scale</option>
                            <option value="height_rod">Height Rod</option>
                            <option value="infantometer">Infantometer</option>
                            <option value="muac_tape">MUAC Tape</option>
                            <option value="multi_function">Multi-Function Device</option>
                        </select></div>
                    <div class="flex justify-end border-t border-gray-200 pt-4">
                        <a href="{{ route('devices.index') }}" class="px-4 py-2 text-gray-600 mr-3">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add Device</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
