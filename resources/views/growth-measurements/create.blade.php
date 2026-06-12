@extends('layouts.app')

@section('header')
Record Growth Measurement
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    @if($errors->any())
    <div class="mb-4 bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-md">
        <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('growth-measurements.store') }}" id="measurementForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Child *</label>
                    <select name="child_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Child</option>
                        @foreach($children as $c)
                            <option value="{{ $c->id }}" {{ ($child && $child->id == $c->id) || old('child_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Measurement Date *</label>
                    <input type="date" name="measurement_date" value="{{ old('measurement_date', date('Y-m-d')) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                    <div class="flex space-x-2">
                        <input type="number" step="0.01" name="weight" id="weightInput" value="{{ old('weight') }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. 12.5">
                        <button type="button" id="connectScaleBtn" class="px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 whitespace-nowrap flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Scale
                        </button>
                    </div>
                    <div id="scaleStatus" class="mt-1 text-xs text-gray-500 hidden"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Height / Length (cm)</label>
                    <input type="number" step="0.1" name="height" value="{{ old('height') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. 90.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Head Circumference (cm)</label>
                    <input type="number" step="0.1" name="head_circumference" value="{{ old('head_circumference') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">MUAC (cm)</label>
                    <input type="number" step="0.1" name="mid_upper_arm_circumference" value="{{ old('mid_upper_arm_circumference') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Temperature (°C)</label>
                    <input type="number" step="0.1" name="temperature" value="{{ old('temperature') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. 36.5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Clinical Notes</label>
                    <textarea name="clinical_notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('clinical_notes') }}</textarea>
                </div>
            </div>
            <div class="flex justify-end border-t border-gray-200 pt-6">
                <a href="{{ route('growth-measurements.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 mr-3">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">Save Measurement</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const connectBtn = document.getElementById('connectScaleBtn');
    const weightInput = document.getElementById('weightInput');
    const scaleStatus = document.getElementById('scaleStatus');
    let port = null;
    let reader = null;
    let keepReading = false;

    // Check if Web Serial API is supported
    if (!navigator.serial) {
        connectBtn.textContent = 'Web Serial N/A';
        connectBtn.disabled = true;
        connectBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
        connectBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        scaleStatus.textContent = 'Web Serial API not supported in this browser. Use Chrome or Edge.';
        scaleStatus.classList.remove('hidden');
        scaleStatus.classList.add('text-orange-500');
    }

    connectBtn.addEventListener('click', async function() {
        if (port) {
            // Disconnect
            await disconnectScale();
            return;
        }

        try {
            // Request a serial port
            port = await navigator.serial.requestPort();
            
            // Open the port
            await port.open({
                baudRate: 9600,
                dataBits: 8,
                stopBits: 1,
                parity: 'none',
                flowControl: 'none'
            });

            connectBtn.textContent = 'Disconnect';
            connectBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            connectBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            scaleStatus.textContent = '✅ Connected to scale. Place child on scale...';
            scaleStatus.classList.remove('hidden', 'text-orange-500');
            scaleStatus.classList.add('text-green-600');

            keepReading = true;
            readScaleData();

        } catch (err) {
            if (err.name !== 'NotFoundError') {
                scaleStatus.textContent = '❌ Error: ' + err.message;
                scaleStatus.classList.remove('hidden');
                scaleStatus.classList.add('text-red-500');
            }
        }
    });

    async function readScaleData() {
        const textDecoder = new TextDecoderStream();
        const readableStreamClosed = port.readable.pipeTo(textDecoder.writable);
        reader = textDecoder.readable.getReader();
        
        let buffer = '';

        try {
            while (keepReading) {
                const { value, done } = await reader.read();
                if (done) break;

                if (value) {
                    buffer += value;
                    
                    // Process complete lines
                    let lines = buffer.split('\n');
                    buffer = lines.pop(); // Keep incomplete line in buffer

                    for (const line of lines) {
                        const trimmed = line.trim();
                        if (!trimmed) continue;
                        
                        // Extract weight value - supports multiple formats:
                        // "12.345 kg", "12.345kg", "ST,12.345", "12.345", "+12.345"
                        const patterns = [
                            /(\d+\.?\d*)\s*kg/i,
                            /ST,?(\d+\.?\d*)/i,
                            /(\d+\.?\d*)/,
                        ];

                        for (const pattern of patterns) {
                            const match = trimmed.match(pattern);
                            if (match) {
                                const value = parseFloat(match[1]);
                                if (!isNaN(value) && value > 0 && value < 50) {
                                    weightInput.value = value.toFixed(2);
                                    weightInput.classList.add('ring-2', 'ring-green-400');
                                    scaleStatus.textContent = `⚖️ Captured weight: ${value.toFixed(2)} kg`;
                                    setTimeout(() => {
                                        weightInput.classList.remove('ring-2', 'ring-green-400');
                                    }, 2000);
                                }
                                break;
                            }
                        }
                    }
                }
            }
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('Serial read error:', err);
            }
        } finally {
            reader.releaseLock();
        }
    }

    async function disconnectScale() {
        keepReading = false;
        
        if (reader) {
            try {
                reader.cancel();
            } catch (e) { /* ignore */ }
            reader = null;
        }
        
        if (port) {
            try {
                await port.close();
            } catch (e) { /* ignore */ }
            port = null;
        }

        connectBtn.textContent = 'Scale';
        connectBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        connectBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        scaleStatus.textContent = 'Disconnected from scale.';
        scaleStatus.classList.remove('text-green-600', 'text-red-500');
        scaleStatus.classList.add('text-gray-500');

        setTimeout(() => {
            scaleStatus.classList.add('hidden');
        }, 3000);
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        keepReading = false;
        if (reader) reader.cancel();
        if (port) port.close();
    });
});
</script>
@endsection