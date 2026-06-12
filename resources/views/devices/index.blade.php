@extends('layouts.app')

@section('header', 'Connected Devices')

@section('title', config('app.name', 'Child Growth Monitor'))

@section('content')
<div x-data="deviceManager()" class="space-y-6">
    <!-- Success/Error Messages -->
    <template x-if="message.show">
        <div class="flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-sm fade-in"
             :class="message.type === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-red-50 border border-red-200 text-red-800'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="message.type === 'success' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'"/>
            </svg>
            <span class="text-sm font-medium flex-1" x-text="message.text"></span>
            <button @click="message.show = false" class="opacity-60 hover:opacity-100">&times;</button>
        </div>
    </template>

    <!-- Connect Device Section (shown when ?connect=1) -->
    @if(request('connect') == 1)
    <div class="content-card" x-data="connectManager()">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-50">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Connect a Device</h3>
                    <p class="text-sm text-gray-500">Connect to a registered device for automatic data capture</p>
                </div>
            </div>
            <a href="{{ route('devices.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">&larr; Back to Devices</a>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label class="form-label">Select Device</label>
                    <select x-model="selectedDevice" class="form-input">
                        <option value="">-- Choose a device --</option>
                        @foreach(\App\Models\DeviceConnection::where('user_id', Auth::id())->get() as $device)
                        <option value="{{ $device->id }}" data-type="{{ $device->device_type }}">{{ $device->device_name }} ({{ $device->serial_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Connection Port</label>
                    <input type="text" x-model="comPort" class="form-input" placeholder="e.g. COM3 or /dev/ttyUSB0">
                </div>
                <div class="form-group">
                    <label class="form-label">Baud Rate</label>
                    <select x-model="baudRate" class="form-input">
                        <option value="9600">9600</option>
                        <option value="19200">19200</option>
                        <option value="38400">38400</option>
                        <option value="57600">57600</option>
                        <option value="115200" selected>115200</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Data Bits</label>
                    <select x-model="dataBits" class="form-input">
                        <option value="7">7</option>
                        <option value="8" selected>8</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Parity</label>
                    <select x-model="parity" class="form-input">
                        <option value="none" selected>None</option>
                        <option value="even">Even</option>
                        <option value="odd">Odd</option>
                        <option value="mark">Mark</option>
                        <option value="space">Space</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Stop Bits</label>
                    <select x-model="stopBits" class="form-input">
                        <option value="1" selected>1</option>
                        <option value="2">2</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                <button @click="connect()" class="btn-primary" :disabled="!selectedDevice || connecting">
                    <span x-show="connecting" class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:14px;height:14px;border-width:2px;"></span>
                    <svg x-show="!connecting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    <span x-text="connecting ? 'Connecting...' : 'Connect Device'"></span>
                </button>
                <button @click="testConnection()" class="btn-secondary" :disabled="!selectedDevice">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Test Connection
                </button>
                <div x-show="connectionStatus" x-cloak class="text-sm font-medium" :class="connectionStatus === 'success' ? 'text-emerald-600' : 'text-red-600'" x-text="connectionMessage"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-blue-600" x-text="stats.total"></p>
            <p class="text-xs text-gray-500 font-medium">Total Devices</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-emerald-600" x-text="stats.connected"></p>
            <p class="text-xs text-gray-500 font-medium">Connected</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-purple-600" x-text="stats.active"></p>
            <p class="text-xs text-gray-500 font-medium">Active</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-amber-600" x-text="stats.inactive"></p>
            <p class="text-xs text-gray-500 font-medium">Inactive</p>
        </div>
    </div>

    <!-- Device List -->
    <div class="content-card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900">Registered Devices</h3>
                <p class="text-sm text-gray-500" x-text="devices.length + ' device(s)'"></p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('devices.create') }}" class="btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Device
                </a>
                <button @click="refreshDevices()" class="btn-secondary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table-main">
                <thead>
                    <tr>
                        <th>Device</th>
                        <th>Serial #</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Last Connected</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="devices.length === 0">
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                                <p class="text-sm">No devices registered yet.</p>
                                <p class="text-xs text-gray-400 mt-1">Add your first device to start capturing growth data automatically.</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(device, index) in devices" :key="device.id">
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                                         :class="device.is_active ? 'bg-emerald-50' : 'bg-gray-100'">
                                        <svg class="w-4 h-4" :class="device.is_active ? 'text-emerald-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900" x-text="device.device_name"></span>
                                        <p class="text-xs text-gray-400" x-text="device.manufacturer || 'Unknown manufacturer'"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs font-medium text-gray-500" x-text="device.serial_number"></td>
                            <td>
                                <span class="badge text-xs" x-text="getDeviceTypeLabel(device.device_type)"></span>
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold"
                                      :class="isDeviceConnected(device) ? 'text-emerald-700' : 'text-gray-500'">
                                    <span class="w-2 h-2 rounded-full" :class="isDeviceConnected(device) ? 'bg-emerald-500' : 'bg-gray-300'"></span>
                                    <span x-text="isDeviceConnected(device) ? 'Connected' : 'Offline'"></span>
                                </span>
                            </td>
                            <td class="text-xs text-gray-500" x-text="formatDate(device.last_connected_at)"></td>
                            <td>
                                <div class="flex gap-1.5 justify-center">
                                    <!-- Connect -->
                                    <button @click="connectDevice(device)" :disabled="isDeviceConnected(device)" class="action-btn"
                                            style="background:#EFF6FF;color:#2563EB;display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:none;cursor:pointer;"
                                            :title="isDeviceConnected(device) ? 'Already connected' : 'Connect'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    </button>
                                    <!-- Calibrate -->
                                    <button @click="calibrateDevice(device)" class="action-btn"
                                            style="background:#FFFBEB;color:#D97706;display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:none;cursor:pointer;"
                                            title="Calibrate device">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </button>
                                    <!-- Delete -->
                                    <button @click="confirmDelete(device)" class="action-btn"
                                            style="background:#FEF2F2;color:#DC2626;display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:none;cursor:pointer;"
                                            title="Remove device">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Calibration Modal -->
    <div class="modal-overlay" id="calibrateModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Calibrate Device</h3>
                <button class="modal-close" onclick="closeModal('calibrateModal')">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-sm text-gray-500 mb-4" x-text="'Configure calibration for ' + (calibrateTarget?.device_name || 'device')"></p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Offset</label>
                        <input type="number" step="0.001" x-model="calibrationOffset" class="form-input" placeholder="0.000">
                        <p class="text-xs text-gray-400 mt-1">Adjustment offset value</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Factor</label>
                        <input type="number" step="0.01" x-model="calibrationFactor" class="form-input" placeholder="1.00" min="0.1" max="10">
                        <p class="text-xs text-gray-400 mt-1">Multiplication factor</p>
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mt-2">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Calibration Formula</p>
                            <p class="text-xs text-blue-600 mt-1">Calibrated Value = (Raw Value + Offset) × Factor</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="closeModal('calibrateModal')" class="btn-secondary">Cancel</button>
                <button @click="saveCalibration()" class="btn-primary" :disabled="calibrating">
                    <span x-show="calibrating" class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:14px;height:14px;border-width:2px;"></span>
                    Save Calibration
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="confirm-overlay" id="deleteDeviceConfirm">
        <div class="confirm-container">
            <div class="confirm-icon" style="background:#FEF2F2;margin:0 auto 16px;">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <h3 style="font-size:1.1rem;font-weight:700;color:#0F172A;margin-bottom:8px;">Remove Device</h3>
            <p style="color:#64748B;font-size:0.85rem;margin-bottom:24px;" x-text="'Are you sure you want to remove "' + (deleteTarget?.device_name || 'this device') + '"? This action cannot be undone.'"></p>
            <div class="flex gap-3 justify-center">
                <button @click="closeConfirm('deleteDeviceConfirm')" class="btn-secondary">Cancel</button>
                <button @click="deleteDevice()" class="btn-danger" :disabled="deleting">
                    <span x-show="deleting" class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:16px;height:16px;border-width:2px;"></span>
                    Remove
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .action-btn { transition: all 0.15s ease; }
    .action-btn:hover { transform: translateY(-1px); }
    .action-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
</style>
@endpush

@push('scripts')
<script>
function deviceManager() {
    return {
        devices: @json(\App\Models\DeviceConnection::where('user_id', Auth::id())->with('user')->latest()->get()),
        deleteTarget: null,
        calibrateTarget: null,
        calibrationOffset: 0,
        calibrationFactor: 1.0,
        connecting: false,
        deleting: false,
        calibrating: false,
        message: { show: false, text: '', type: 'success' },
        stats: { total: 0, connected: 0, active: 0, inactive: 0 },

        init() {
            this.updateStats();
        },

        showMessage(text, type = 'success') {
            this.message = { show: true, text, type };
            setTimeout(() => { this.message.show = false; }, 4000);
        },

        refreshDevices() {
            fetch('{{ route('api.devices.index') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.devices) this.devices = data.devices;
                this.updateStats();
            })
            .catch(() => {});
        },

        updateStats() {
            const total = this.devices.length;
            const now = new Date();
            let connected = 0, active = 0, inactive = 0;
            this.devices.forEach(d => {
                if (d.is_active) active++; else inactive++;
                if (d.last_connected_at) {
                    const last = new Date(d.last_connected_at);
                    if ((now - last) < 300000) connected++; // 5 min threshold
                }
            });
            this.stats = { total, connected, active, inactive };
        },

        getDeviceTypeLabel(type) {
            const labels = {
                'weight_scale': 'Weight Scale',
                'height_rod': 'Height Rod',
                'muac_tape': 'MUAC Tape',
                'infantometer': 'Infantometer',
                'multi_function': 'Multi-Function',
                'digital_scale': 'Digital Scale'
            };
            return labels[type] || type?.replace(/_/g, ' ') || 'Unknown';
        },

        isDeviceConnected(device) {
            if (!device.last_connected_at) return false;
            const now = new Date();
            const last = new Date(device.last_connected_at);
            return (now - last) < 300000; // 5 min threshold
        },

        formatDate(date) {
            if (!date) return 'Never';
            return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        async connectDevice(device) {
            this.connecting = true;
            try {
                const r = await fetch('/devices/' + device.id + '/connect', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await r.json();
                if (data.success) {
                    this.showMessage('Device connected successfully', 'success');
                    this.refreshDevices();
                } else {
                    this.showMessage(data.message || 'Connection failed', 'error');
                }
            } catch (e) {
                this.showMessage('Connection error: ' + e.message, 'error');
            }
            this.connecting = false;
        },

        calibrateDevice(device) {
            this.calibrateTarget = device;
            this.calibrationOffset = device.calibration_data?.offset || 0;
            this.calibrationFactor = device.calibration_data?.factor || 1.0;
            openModal('calibrateModal');
        },

        async saveCalibration() {
            if (!this.calibrateTarget) return;
            this.calibrating = true;
            try {
                const r = await fetch('/devices/' + this.calibrateTarget.id + '/calibrate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ offset: this.calibrationOffset, factor: this.calibrationFactor })
                });
                const data = await r.json();
                if (data.success) {
                    this.showMessage('Device calibrated successfully', 'success');
                    closeModal('calibrateModal');
                    this.refreshDevices();
                } else {
                    this.showMessage(data.message || 'Calibration failed', 'error');
                }
            } catch (e) {
                this.showMessage('Calibration error: ' + e.message, 'error');
            }
            this.calibrating = false;
        },

        confirmDelete(device) {
            this.deleteTarget = device;
            openConfirm('deleteDeviceConfirm');
        },

        async deleteDevice() {
            if (!this.deleteTarget) return;
            this.deleting = true;
            try {
                const r = await fetch('/devices/' + this.deleteTarget.id, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await r.json();
                if (data.success) {
                    this.showMessage(data.message, 'success');
                    closeConfirm('deleteDeviceConfirm');
                    this.deleteTarget = null;
                    this.refreshDevices();
                } else {
                    this.showMessage(data.message || 'Delete failed', 'error');
                }
            } catch (e) {
                this.showMessage('Network error: ' + e.message, 'error');
            }
            this.deleting = false;
        },

        closeConfirm(id) {
            closeConfirm(id);
            this.deleteTarget = null;
        }
    };
}

// Connect Manager for the ?connect=1 section (separate alpine component)
function connectManager() {
    return {
        selectedDevice: '',
        comPort: '',
        baudRate: 115200,
        dataBits: 8,
        parity: 'none',
        stopBits: 1,
        connecting: false,
        connectionStatus: '',
        connectionMessage: '',
        connected: false,

        async connect() {
            if (!this.selectedDevice) {
                this.connectionStatus = 'error';
                this.connectionMessage = 'Please select a device';
                return;
            }
            this.connecting = true;
            this.connectionStatus = '';
            this.connectionMessage = '';
            try {
                const r = await fetch('/devices/' + this.selectedDevice + '/connect', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        com_port: this.comPort,
                        baud_rate: this.baudRate,
                        data_bits: this.dataBits,
                        parity: this.parity,
                        stop_bits: this.stopBits
                    })
                });
                const data = await r.json();
                if (data.success) {
                    this.connectionStatus = 'success';
                    this.connectionMessage = '✓ Device connected successfully!';
                    this.connected = true;
                } else {
                    this.connectionStatus = 'error';
                    this.connectionMessage = '✗ ' + (data.message || 'Connection failed');
                }
            } catch (e) {
                this.connectionStatus = 'error';
                this.connectionMessage = '✗ Connection error: ' + e.message;
            }
            this.connecting = false;
        },

        async testConnection() {
            if (!this.selectedDevice) {
                this.connectionStatus = 'error';
                this.connectionMessage = 'Please select a device first';
                return;
            }
            this.connectionStatus = '';
            this.connectionMessage = 'Testing connection...';
            // Simulate test - just verify the device exists
            try {
                const r = await fetch('/devices/' + this.selectedDevice, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (r.ok) {
                    this.connectionStatus = 'success';
                    this.connectionMessage = '✓ Device found and ready to connect';
                } else {
                    this.connectionStatus = 'error';
                    this.connectionMessage = '✗ Could not reach device';
                }
            } catch (e) {
                this.connectionStatus = 'error';
                this.connectionMessage = '✗ Error: ' + e.message;
            }
        }
    };
}
</script>
@endpush
@endsection