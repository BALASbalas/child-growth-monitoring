@extends('layouts.app')

@section('header', 'System Settings')

@section('title', config('app.name', 'Child Growth Monitor'))

@section('content')
<div x-data="settingsManager()" class="space-y-6">
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

    <!-- General Settings -->
    <div class="content-card">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-blue-50">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">General Settings</h3>
                    <p class="text-sm text-gray-500">System name, contact info, timezone and localization</p>
                </div>
            </div>
            <button @click="saveAll()" class="btn-primary btn-sm" :disabled="saving">
                <span x-show="saving" class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:14px;height:14px;border-width:2px;"></span>
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span x-text="saving ? 'Saving...' : 'Save All'"></span>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="form-group">
                    <label class="form-label">System Name</label>
                    <input type="text" x-model="settings.system_name" class="form-input" placeholder="Child Growth Monitoring System">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Email</label>
                    <input type="email" x-model="settings.system_email" class="form-input" placeholder="admin@example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" x-model="settings.phone_number" class="form-input" placeholder="+255 123 456 789">
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" x-model="settings.address" class="form-input" placeholder="Dar es Salaam, Tanzania">
                </div>
                <div class="form-group">
                    <label class="form-label">Footer Text</label>
                    <input type="text" x-model="settings.footer_text" class="form-input" placeholder="Copyright text">
                </div>
                <div class="form-group">
                    <label class="form-label">Timezone</label>
                    <select x-model="settings.timezone" class="form-input">
                        <option value="UTC">UTC</option>
                        <option value="Africa/Nairobi">Africa/Nairobi (EAT)</option>
                        <option value="Africa/Lagos">Africa/Lagos (WAT)</option>
                        <option value="Africa/Cairo">Africa/Cairo (EET)</option>
                        <option value="Africa/Johannesburg">Africa/Johannesburg (SAST)</option>
                        <option value="America/New_York">America/New_York (EST)</option>
                        <option value="America/Los_Angeles">America/Los_Angeles (PST)</option>
                        <option value="Europe/London">Europe/London (GMT)</option>
                        <option value="Asia/Dubai">Asia/Dubai (GST)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Language</label>
                    <select x-model="settings.language" class="form-input">
                        <option value="en">English</option>
                        <option value="fr">French</option>
                        <option value="sw">Swahili</option>
                        <option value="es">Spanish</option>
                        <option value="pt">Portuguese</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Records Per Page</label>
                    <select x-model="settings.per_page" class="form-input">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date Format</label>
                    <select x-model="settings.date_format" class="form-input">
                        <option value="Y-m-d">Y-m-d (2026-06-09)</option>
                        <option value="m/d/Y">m/d/Y (06/09/2026)</option>
                        <option value="d/m/Y">d/m/Y (09/06/2026)</option>
                        <option value="d M, Y">d M, Y (09 Jun, 2026)</option>
                        <option value="M d, Y">M d, Y (Jun 09, 2026)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- System Logo -->
    <div class="content-card">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-purple-50">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">System Logo</h3>
                    <p class="text-sm text-gray-500">Upload your organization logo (shown in header)</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-6">
                <div class="w-32 h-32 rounded-2xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden bg-gray-50"
                     :class="logoPreview ? 'border-solid border-blue-200 bg-blue-50' : ''">
                    <template x-if="logoPreview">
                        <img :src="logoPreview" class="w-full h-full object-contain p-2">
                    </template>
                    <template x-if="!logoPreview">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </template>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-3">Recommended: 200x200px, PNG or JPG, max 2MB</p>
                    <div class="flex items-center gap-3">
                        <label class="btn-secondary btn-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            Choose File
                            <input type="file" accept="image/*" @change="handleLogoUpload($event)" class="hidden">
                        </label>
                        <button @click="removeLogo()" class="text-sm text-red-500 hover:text-red-700 font-medium">Remove</button>
                    </div>
                    <div x-show="logoUploading" class="mt-2">
                        <div class="flex items-center gap-2 text-sm text-blue-600">
                            <span class="spinner" style="border-color:#E2E8F0;border-top-color:#2563EB;width:16px;height:16px;border-width:2px;"></span>
                            Uploading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Maintenance -->
    <div class="content-card">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-50">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Security & Maintenance</h3>
                    <p class="text-sm text-gray-500">Retention policies and system toggles</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="form-group">
                    <label class="form-label">Audit Log Retention (days)</label>
                    <input type="number" x-model="settings.audit_log_retention_days" class="form-input" min="1" max="3650">
                    <p class="text-xs text-gray-400 mt-1">Logs older than this will be auto-purged</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Backup Retention (days)</label>
                    <input type="number" x-model="settings.backup_retention_days" class="form-input" min="1" max="365">
                    <p class="text-xs text-gray-400 mt-1">Old backups beyond this period will be removed</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Auto Backup</label>
                    <div class="flex items-center gap-3 mt-2">
                        <button @click="settings.auto_backup_enabled = !settings.auto_backup_enabled"
                                class="relative w-12 h-6 rounded-full transition-colors duration-200 ease-in-out"
                                :class="settings.auto_backup_enabled ? 'bg-blue-600' : 'bg-gray-300'">
                            <span class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-transform duration-200 ease-in-out" :class="settings.auto_backup_enabled ? 'translate-x-6' : 'translate-x-0'"></span>
                        </button>
                        <span class="text-sm text-gray-600" x-text="settings.auto_backup_enabled ? 'Enabled' : 'Disabled'"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Maintenance Mode</label>
                    <div class="flex items-center gap-3 mt-2">
                        <button @click="settings.maintenance_mode = !settings.maintenance_mode"
                                class="relative w-12 h-6 rounded-full transition-colors duration-200 ease-in-out"
                                :class="settings.maintenance_mode ? 'bg-amber-600' : 'bg-gray-300'">
                            <span class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-transform duration-200 ease-in-out" :class="settings.maintenance_mode ? 'translate-x-6' : 'translate-x-0'"></span>
                        </button>
                        <span class="text-sm text-gray-600" x-text="settings.maintenance_mode ? 'Enabled' : 'Disabled'"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function settingsManager() {
    return {
        saving: false,
        logoUploading: false,
        logoPreview: '',
        message: { show: false, text: '', type: 'success' },
        settings: {
            system_name: 'Child Growth Monitoring System',
            system_email: '',
            footer_text: '',
            phone_number: '',
            address: '',
            timezone: 'UTC',
            language: 'en',
            per_page: 15,
            date_format: 'Y-m-d',
            time_format: 'H:i:s',
            audit_log_retention_days: 365,
            backup_retention_days: 30,
            auto_backup_enabled: false,
            maintenance_mode: false,
        },

        async init() {
            await this.loadSettings();
        },

        showMessage(text, type = 'success') {
            this.message = { show: true, text, type };
            setTimeout(() => { this.message.show = false; }, 4000);
        },

        async loadSettings() {
            try {
                const r = await fetch('{{ route('admin.api.settings') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await r.json();
                // data is flat key-value: { system_name: "...", system_logo: "...", ... }
                Object.keys(data).forEach(key => {
                    if (key in this.settings) {
                        let val = data[key];
                        // Convert boolean strings
                        if (val === 'true') val = true;
                        if (val === 'false') val = false;
                        this.settings[key] = val;
                    }
                });
                if (data.system_logo) {
                    this.logoPreview = '{{ asset('storage/') }}/' + data.system_logo;
                }
            } catch (e) {
                // Use defaults
            }
        },

        async saveAll() {
            this.saving = true;
            try {
                const r = await fetch('{{ route('admin.api.settings.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.settings)
                });
                const data = await r.json();
                if (data.success) {
                    this.showMessage(data.message, 'success');
                } else {
                    this.showMessage(data.message || 'Failed to save settings', 'error');
                }
            } catch (e) {
                this.showMessage('Network error: ' + e.message, 'error');
            }
            this.saving = false;
        },

        async handleLogoUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => { this.logoPreview = e.target.result; };
            reader.readAsDataURL(file);

            this.logoUploading = true;
            const formData = new FormData();
            formData.append('logo', file);

            try {
                const r = await fetch('{{ route('admin.api.settings.logo') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const data = await r.json();
                if (data.success) {
                    this.settings.system_logo = 'settings/' + (file.name.split('.').pop());
                    this.showMessage(data.message, 'success');
                } else {
                    this.showMessage(data.message || 'Upload failed', 'error');
                }
            } catch (e) {
                this.showMessage('Upload error: ' + e.message, 'error');
            }
            this.logoUploading = false;
        },

        async removeLogo() {
            if (!confirm('Remove the system logo?')) return;
            this.logoPreview = '';
            this.settings.system_logo = null;
            try {
                const r = await fetch('{{ route('admin.api.settings.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ system_logo: null })
                });
                const data = await r.json();
                if (data.success) {
                    this.showMessage('Logo removed', 'success');
                }
            } catch (e) {
                this.showMessage('Error removing logo', 'error');
            }
        }
    };
}
</script>
@endpush
@endsection