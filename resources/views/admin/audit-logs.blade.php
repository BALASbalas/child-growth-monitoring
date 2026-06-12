@extends('layouts.app')

@section('header', 'Audit Logs')

@section('title', config('app.name', 'Child Growth Monitor'))

@section('content')
<div x-data="auditLogManager()" class="space-y-6">
    <!-- Filter Bar -->
    <div class="content-card p-4">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="filters.search" @input.debounce.500ms="loadLogs()" placeholder="Search logs..." class="form-input pl-10" style="width:260px;">
            </div>
            <!-- Filter by Action -->
            <select x-model="filters.action" @change="loadLogs()" class="form-input" style="width:160px;">
                <option value="all">All Actions</option>
                <template x-for="act in actions" :key="act.value">
                    <option :value="act.value" x-text="act.label"></option>
                </template>
            </select>
            <!-- Filter by User Role -->
            <select x-model="filters.role" @change="loadLogs()" class="form-input" style="width:160px;">
                <option value="all">All Users</option>
                <option value="admin">Administrator</option>
                <option value="nurse">Nurse</option>
                <option value="doctor">Doctor</option>
                <option value="parent">Parent</option>
                <option value="guardian">Guardian</option>
            </select>
            <!-- Date From -->
            <input type="date" x-model="filters.date_from" @change="loadLogs()" class="form-input" style="width:150px;" placeholder="From date">
            <!-- Date To -->
            <input type="date" x-model="filters.date_to" @change="loadLogs()" class="form-input" style="width:150px;" placeholder="To date">
            <!-- Clear Filters -->
            <button @click="clearFilters()" class="btn-secondary btn-sm" x-show="hasActiveFilters">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear
            </button>
            <!-- Refresh -->
            <button @click="loadLogs()" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-blue-600" x-text="stats.total"></p>
            <p class="text-xs text-gray-500 font-medium">Total Logs</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-emerald-600" x-text="stats.creates"></p>
            <p class="text-xs text-gray-500 font-medium">Creations</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-amber-600" x-text="stats.updates"></p>
            <p class="text-xs text-gray-500 font-medium">Updates</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-red-600" x-text="stats.deletes"></p>
            <p class="text-xs text-gray-500 font-medium">Deletions</p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="content-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-main">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th class="text-center">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <span class="spinner" style="border-color:#E2E8F0;border-top-color:#2563EB;width:32px;height:32px;border-width:3px;display:inline-block;"></span>
                                <p class="text-sm mt-2">Loading logs...</p>
                            </td>
                        </tr>
                    </template>
                    <template x-if="!loading && logs.length === 0">
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm">No audit logs found</p>
                                <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or check back later.</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="log in logs" :key="log.id">
                        <tr>
                            <td class="text-xs font-mono" x-text="formatTimestamp(log.created_at)"></td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0"
                                         :style="'background:' + avatarColor(log.user?.name || '?')">
                                        <span x-text="(log.user?.name || '?').charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900" x-text="log.user?.name || 'System'"></p>
                                        <p class="text-[10px] text-gray-400" x-text="log.user?.email || ''"></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge" :class="log.action_color || 'bg-gray-100 text-gray-800'" x-text="log.action_label || log.action"></span>
                            </td>
                            <td class="text-sm text-gray-600 max-w-xs truncate" x-text="log.description || '-'"></td>
                            <td class="text-xs font-mono text-gray-400" x-text="log.ip_address || '-'"></td>
                            <td class="text-center">
                                <button @click="viewDetails(log)" class="btn-xs" style="background:#F1F5F9;color:#475569;border:none;border-radius:8px;font-weight:600;">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between" x-show="total > 0">
        <p class="text-sm text-gray-500" x-text="'Showing ' + ((currentPage - 1) * perPage + 1) + ' to ' + Math.min(currentPage * perPage, total) + ' of ' + total + ' logs'"></p>
        <div class="flex gap-1.5">
            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                    class="px-3 py-1.5 rounded-lg text-sm font-semibold border border-gray-200 bg-white text-gray-600 hover:border-blue-500 hover:text-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                Previous
            </button>
            <template x-for="page in pages" :key="page">
                <button @click="changePage(page)" 
                        class="px-3 py-1.5 rounded-lg text-sm font-semibold border transition-all"
                        :class="page === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-200 bg-white text-gray-600 hover:border-blue-500 hover:text-blue-600'"
                        x-text="page">
                </button>
            </template>
            <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage"
                    class="px-3 py-1.5 rounded-lg text-sm font-semibold border border-gray-200 bg-white text-gray-600 hover:border-blue-500 hover:text-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                Next
            </button>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal-overlay" id="logDetailsModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Audit Log Details</h3>
                <button @click="closeModal('logDetailsModal')" class="modal-close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <template x-if="selectedLog">
                    <div class="space-y-4">
                        <div class="detail-section">
                            <div class="detail-section-title">Event Information</div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="detail-label">Action</div>
                                    <div class="detail-value">
                                        <span class="badge" :class="selectedLog.action_color" x-text="selectedLog.action_label"></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="detail-label">Timestamp</div>
                                    <div class="detail-value" x-text="formatTimestamp(selectedLog.created_at)"></div>
                                </div>
                                <div>
                                    <div class="detail-label">IP Address</div>
                                    <div class="detail-value text-xs font-mono" x-text="selectedLog.ip_address || '-'"></div>
                                </div>
                                <div>
                                    <div class="detail-label">User Agent</div>
                                    <div class="detail-value text-xs truncate" x-text="selectedLog.user_agent || '-'"></div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <div class="detail-section-title">User Information</div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="detail-label">Name</div>
                                    <div class="detail-value" x-text="selectedLog.user?.name || 'System'"></div>
                                </div>
                                <div>
                                    <div class="detail-label">Email</div>
                                    <div class="detail-value" x-text="selectedLog.user?.email || '-'"></div>
                                </div>
                                <div>
                                    <div class="detail-label">Role</div>
                                    <div class="detail-value" x-text="selectedLog.user?.role || '-'"></div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <div class="detail-section-title">Description</div>
                            <p class="text-sm text-gray-700" x-text="selectedLog.description || 'No description'"></p>
                        </div>

                        <template x-if="selectedLog.model_type">
                            <div class="detail-section">
                                <div class="detail-section-title">Affected Record</div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="detail-label">Model</div>
                                        <div class="detail-value text-xs" x-text="selectedLog.model_type.split('\\').pop()"></div>
                                    </div>
                                    <div>
                                        <div class="detail-label">Record ID</div>
                                        <div class="detail-value" x-text="'#' + selectedLog.model_id"></div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedLog.old_values">
                            <div class="detail-section">
                                <div class="detail-section-title">Previous Values</div>
                                <pre class="bg-gray-50 rounded-xl p-4 text-xs overflow-x-auto max-h-40" x-text="JSON.stringify(selectedLog.old_values, null, 2)"></pre>
                            </div>
                        </template>

                        <template x-if="selectedLog.new_values">
                            <div class="detail-section">
                                <div class="detail-section-title">New Values</div>
                                <pre class="bg-gray-50 rounded-xl p-4 text-xs overflow-x-auto max-h-40" x-text="JSON.stringify(selectedLog.new_values, null, 2)"></pre>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button @click="closeModal('logDetailsModal')" class="btn-secondary">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #94A3B8; margin-bottom: 4px; }
    .detail-value { font-size: 0.9rem; font-weight: 500; color: #0F172A; }
    .detail-section { margin-bottom: 20px; }
    .detail-section-title { font-size: 0.85rem; font-weight: 700; color: #0F172A; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #E2E8F0; }
</style>
@endpush

@push('scripts')
<script>
    function auditLogManager() {
        return {
            logs: [],
            total: 0,
            currentPage: 1,
            lastPage: 1,
            perPage: 25,
            pages: [],
            loading: true,
            selectedLog: null,
            actions: [],
            filters: {
                search: '',
                action: 'all',
                role: 'all',
                date_from: '',
                date_to: '',
            },
            stats: { total: 0, creates: 0, updates: 0, deletes: 0 },

            get hasActiveFilters() {
                return this.filters.search || this.filters.action !== 'all' || this.filters.role !== 'all' || this.filters.date_from || this.filters.date_to;
            },

            async init() {
                await this.loadActions();
                await this.loadLogs();
            },

            async loadActions() {
                try {
                    const r = await fetch('{{ route('admin.api.audit-log-actions') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.actions = await r.json();
                } catch(e) {
                    this.actions = [];
                }
            },

            async loadLogs() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({ page: this.currentPage, per_page: this.perPage });
                    if (this.filters.search) params.append('search', this.filters.search);
                    if (this.filters.action && this.filters.action !== 'all') params.append('action', this.filters.action);
                    if (this.filters.role && this.filters.role !== 'all') params.append('role', this.filters.role);
                    if (this.filters.date_from) params.append('date_from', this.filters.date_from);
                    if (this.filters.date_to) params.append('date_to', this.filters.date_to);

                    const r = await fetch('{{ route('admin.api.audit-logs') }}?' + params.toString(), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await r.json();
                    this.logs = data.data || [];
                    this.total = data.total || 0;
                    this.currentPage = data.current_page || 1;
                    this.lastPage = data.last_page || 1;
                    this.perPage = data.per_page || 25;
                    this.pages = [];
                    for (let i = 1; i <= data.last_page; i++) this.pages.push(i);
                    this.updateStats();
                } catch(e) {
                    this.logs = [];
                }
                this.loading = false;
            },

            updateStats() {
                const creates = this.logs.filter(l => l.action === 'create').length;
                const updates = this.logs.filter(l => l.action === 'update').length;
                const deletes = this.logs.filter(l => l.action === 'delete').length;
                this.stats = { total: this.total, creates, updates, deletes };
            },

            changePage(page) {
                if (page < 1 || page > this.lastPage) return;
                this.currentPage = page;
                this.loadLogs();
            },

            clearFilters() {
                this.filters = { search: '', action: 'all', role: 'all', date_from: '', date_to: '' };
                this.currentPage = 1;
                this.loadLogs();
            },

            viewDetails(log) {
                this.selectedLog = log;
                openModal('logDetailsModal');
            },

            closeModal(id) { closeModal(id); this.selectedLog = null; },

            formatTimestamp(date) {
                if (!date) return '-';
                return new Date(date).toLocaleDateString('en-US', { 
                    year: 'numeric', month: 'short', day: 'numeric',
                    hour: '2-digit', minute: '2-digit', second: '2-digit' 
                });
            },

            avatarColor(name) {
                const colors = ['#2563EB', '#8b5cf6', '#d946ef', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#10b981', '#14b8a6', '#06b6d4'];
                let hash = 0;
                for (let i = 0; i < (name || '').length; i++) hash = hash * 31 + name.charCodeAt(i);
                return colors[Math.abs(hash) % colors.length];
            }
        };
    }
</script>
@endpush
@endsection