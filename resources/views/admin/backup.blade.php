@extends('layouts.app')

@section('header', 'Backup & Restore')

@section('title', config('app.name', 'Child Growth Monitor'))

@section('content')
<div x-data="backupManager()" class="space-y-6">
    <!-- Success/Error Messages -->
    <template x-if="message.show">
        <div class="flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-sm fade-in"
             :class="message.type === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-red-50 border border-red-200 text-red-800'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      :d="message.type === 'success' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'"/>
            </svg>
            <span class="text-sm font-medium flex-1" x-text="message.text"></span>
            <button @click="message.show = false" class="opacity-60 hover:opacity-100">&times;</button>
        </div>
    </template>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-blue-600" x-text="stats.total"></p>
            <p class="text-xs text-gray-500 font-medium">Total Backups</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-emerald-600" x-text="stats.sql"></p>
            <p class="text-xs text-gray-500 font-medium">SQL Backups</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-purple-600" x-text="stats.json"></p>
            <p class="text-xs text-gray-500 font-medium">JSON Backups</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-amber-600" x-text="stats.totalSize"></p>
            <p class="text-xs text-gray-500 font-medium">Total Size</p>
        </div>
    </div>

    <!-- Create Backup Card -->
    <div class="content-card">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-50">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Create New Backup</h3>
                    <p class="text-sm text-gray-500">Generate a complete backup of all system data</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <select x-model="backupType" class="form-input" style="width:140px;">
                    <option value="sql">SQL Format</option>
                    <option value="json">JSON Format</option>
                </select>
                <button @click="createBackup()" class="btn-primary btn-sm" :disabled="creating">
                    <span x-show="creating" class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:14px;height:14px;border-width:2px;"></span>
                    <svg x-show="!creating" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    <span x-text="creating ? 'Creating...' : 'Create Backup'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Restore Backup Card -->
    <div class="content-card">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-orange-50">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Restore from Backup</h3>
                    <p class="text-sm text-gray-500">Upload a backup file to restore system data (JSON or SQL)</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="file" x-ref="restoreFile" accept=".json,.sql,.zip,.gz" 
                               class="form-input text-sm py-2 pr-20" placeholder="Choose backup file...">
                        <button @click="triggerRestoreFile()" class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-blue-600 font-semibold hover:text-blue-800 bg-white px-2 py-1 rounded-lg">Browse</button>
                    </div>
                </div>
                <button @click="restoreBackup()" class="btn-danger btn-sm" :disabled="restoring">
                    <span x-show="restoring" class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:14px;height:14px;border-width:2px;"></span>
                    <svg x-show="!restoring" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span x-text="restoring ? 'Restoring...' : 'Restore'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Existing Backups Table -->
    <div class="content-card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900">Available Backups</h3>
                <p class="text-sm text-gray-500" x-text="backups.length + ' backup(s) stored'"></p>
            </div>
            <button @click="refreshBackups()" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="table-main">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Filename</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="backups.length === 0">
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                <p class="text-sm">No backups available yet.</p>
                                <p class="text-xs text-gray-400 mt-1">Create your first backup above.</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(backup, index) in backups" :key="backup.id">
                        <tr>
                            <td class="font-mono text-xs font-medium text-gray-500" x-text="index + 1"></td>
                            <td class="font-semibold text-gray-900">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" :class="backup.type === 'json' ? 'text-purple-500' : 'text-blue-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span x-text="backup.filename"></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge" :class="backup.type === 'json' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'" x-text="backup.type?.toUpperCase()"></span>
                            </td>
                            <td class="text-xs font-medium" x-text="backup.file_size_formatted || formatSize(backup.file_size)"></td>
                            <td class="text-xs text-gray-500" x-text="backup.creator?.name || 'System'"></td>
                            <td class="text-xs text-gray-500" x-text="formatDate(backup.created_at)"></td>
                            <td>
                                <div class="flex gap-1.5 justify-center">
                                    <!-- Download -->
                                    <a :href="'/admin/api/backups/' + backup.id + '/download'" class="action-btn" style="background:#EFF6FF;color:#2563EB;display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:none;cursor:pointer;text-decoration:none;" title="Download backup">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    <!-- Delete -->
                                    <button @click="confirmDelete(backup)" class="action-btn" style="background:#FEF2F2;color:#DC2626;display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;border:none;cursor:pointer;" title="Delete backup">
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

    <!-- Delete Confirmation Modal -->
    <div class="confirm-overlay" id="deleteBackupConfirm">
        <div class="confirm-container">
            <div class="confirm-icon" style="background:#FEF2F2;margin:0 auto 16px;">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            </div>
            <h3 style="font-size:1.1rem;font-weight:700;color:#0F172A;margin-bottom:8px;">Delete Backup</h3>
            <p style="color:#64748B;font-size:0.85rem;margin-bottom:24px;" x-text="'Are you sure you want to delete "' + (deleteTarget?.filename || 'this backup') + '"? This action cannot be undone.'"></p>
            <div class="flex gap-3 justify-center">
                <button @click="closeConfirm('deleteBackupConfirm')" class="btn-secondary">Cancel</button>
                <button @click="deleteBackup()" class="btn-danger" :disabled="deleting">
                    <span x-show="deleting" class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:16px;height:16px;border-width:2px;"></span>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .action-btn { transition: all 0.15s ease; }
    .action-btn:hover { transform: translateY(-1px); }
</style>
@endpush

@push('scripts')
<script>
    function backupManager() {
        return {
            backups: [],
            backupType: 'sql',
            deleteTarget: null,
            creating: false,
            restoring: false,
            deleting: false,
            message: { show: false, text: '', type: 'success' },
            stats: { total: 0, sql: 0, json: 0, totalSize: '0 B' },

            async init() {
                await this.refreshBackups();
            },

            showMessage(text, type = 'success') {
                this.message = { show: true, text, type };
                setTimeout(() => { this.message.show = false; }, 4000);
            },

            async refreshBackups() {
                try {
                    const r = await fetch('{{ route('admin.api.backups') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.backups = await r.json();
                    this.updateStats();
                } catch (e) {
                    // Ignore
                }
            },

            updateStats() {
                let totalSize = 0;
                let sql = 0, json = 0;
                this.backups.forEach(b => {
                    totalSize += parseInt(b.file_size) || 0;
                    if (b.type === 'sql') sql++;
                    else if (b.type === 'json') json++;
                });
                this.stats = {
                    total: this.backups.length,
                    sql,
                    json,
                    totalSize: this.formatBytes(totalSize)
                };
            },

            formatBytes(bytes) {
                if (!bytes || bytes === 0) return '0 B';
                const units = ['B', 'KB', 'MB', 'GB'];
                let i = 0;
                let size = bytes;
                while (size >= 1024 && i < units.length - 1) { size /= 1024; i++; }
                return size.toFixed(2) + ' ' + units[i];
            },

            formatSize(size) {
                return this.formatBytes(parseInt(size) || 0);
            },

            formatDate(date) {
                if (!date) return '-';
                return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
            },

            async createBackup() {
                this.creating = true;
                try {
                    const r = await fetch('{{ route('admin.api.backups.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ type: this.backupType })
                    });
                    const data = await r.json();
                    if (data.success) {
                        this.showMessage(data.message, 'success');
                        await this.refreshBackups();
                    } else {
                        this.showMessage(data.message || 'Failed to create backup', 'error');
                    }
                } catch (e) {
                    this.showMessage('Network error: ' + e.message, 'error');
                }
                this.creating = false;
            },

            triggerRestoreFile() {
                const input = this.$refs.restoreFile;
                if (input) input.click();
            },

            async restoreBackup() {
                const input = this.$refs.restoreFile;
                if (!input || !input.files || !input.files[0]) {
                    this.showMessage('Please select a backup file to restore', 'error');
                    return;
                }

                if (!confirm('Restoring will overwrite current system settings. Continue?')) return;

                this.restoring = true;
                const formData = new FormData();
                formData.append('backup_file', input.files[0]);

                try {
                    const r = await fetch('{{ route('admin.api.backups.restore') }}', {
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
                        this.showMessage(data.message, 'success');
                        input.value = '';
                        await this.refreshBackups();
                    } else {
                        this.showMessage(data.message || 'Restore failed', 'error');
                    }
                } catch (e) {
                    this.showMessage('Network error: ' + e.message, 'error');
                }
                this.restoring = false;
            },

            confirmDelete(backup) {
                this.deleteTarget = backup;
                openConfirm('deleteBackupConfirm');
            },

            async deleteBackup() {
                if (!this.deleteTarget) return;
                this.deleting = true;
                try {
                    const r = await fetch('/admin/api/backups/' + this.deleteTarget.id, {
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
                        closeConfirm('deleteBackupConfirm');
                        this.deleteTarget = null;
                        await this.refreshBackups();
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
</script>
@endpush
@endsection