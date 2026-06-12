@extends('layouts.app')

@section('header', 'Vaccinations')

@section('title', config('app.name', 'Child Growth Monitor'))

@push('styles')
<style>
    .pagination-info { font-size: 0.8rem; color: #64748B; }
    .pagination button {
        padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600;
        border: 1px solid #E2E8F0; background: white; color: #475569;
        cursor: pointer; transition: all 0.15s;
    }
    .pagination button:hover:not(:disabled) { border-color: #2563EB; color: #2563EB; }
    .pagination button.active { background: #2563EB; color: white; border-color: #2563EB; }
    .pagination button:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="immunizationManager()">
    <!-- Toolbar -->
    <div class="content-card p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="search" @input.debounce.300ms="loadImmunizations()" placeholder="Search vaccine or child..." class="form-input pl-10" style="width: 220px;">
                </div>
                <select x-model="statusFilter" @change="loadImmunizations()" class="form-input" style="width: 160px;">
                    <option value="">All Status</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="administered">Administered</option>
                    <option value="missed">Missed</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <button @click="openAddModal()" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Vaccination
            </button>
        </div>
    </div>

    <!-- Vaccinations Table -->
    <div class="content-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-main">
                <thead>
                    <tr>
                        <th>Child Name</th>
                        <th>ID</th>
                        <th>Vaccine</th>
                        <th>Dose</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Administered</th>
                        <th>Given By</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="immunizations.length === 0">
                        <tr>
                            <td colspan="9" class="text-center py-12 text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm">No vaccination records found</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(imm, index) in immunizations" :key="imm.id">
                        <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'">
                            <td class="font-semibold text-gray-900" x-text="imm.child_name"></td>
                            <td class="font-mono text-xs text-gray-500" x-text="imm.child_unique_id"></td>
                            <td x-text="imm.vaccine_name"></td>
                            <td x-text="imm.dose_number ? 'Dose ' + imm.dose_number : '-'"></td>
                            <td>
                                <span class="badge" :class="statusClass(imm.status)" x-text="imm.status_label"></span>
                            </td>
                            <td class="text-xs" x-text="imm.next_due_date || '-'"></td>
                            <td class="text-xs" x-text="imm.date_administered || '-'"></td>
                            <td class="text-xs" x-text="imm.administered_by || '-'"></td>
                            <td>
                                <div class="flex gap-1.5 justify-center">
                                    <button @click="viewImmunization(imm)" class="btn-xs" style="background:#EFF6FF;color:#2563EB;border:none;border-radius:8px;font-weight:600;">View</button>
                                    <button @click="editImmunization(imm)" class="btn-xs" style="background:#F1F5F9;color:#475569;border:none;border-radius:8px;font-weight:600;">Edit</button>
                                    <button @click="confirmDelete(imm)" class="btn-xs" style="background:#FEF2F2;color:#DC2626;border:none;border-radius:8px;font-weight:600;">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between" x-show="total > 0">
        <p class="pagination-info" x-text="'Showing ' + ((currentPage - 1) * perPage + 1) + ' to ' + Math.min(currentPage * perPage, total) + ' of ' + total + ' records'"></p>
        <div class="pagination flex gap-1.5">
            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
            <template x-for="page in pages" :key="page">
                <button @click="changePage(page)" :class="{'active': page === currentPage}" x-text="page"></button>
            </template>
            <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage">Next</button>
        </div>
    </div>

    <!-- Add/Edit Vaccination Modal -->
    <div class="modal-overlay" id="immunizationModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 x-text="isEditing ? 'Edit Vaccination' : 'Add Vaccination'"></h3>
                <button @click="closeModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group sm:col-span-2">
                        <label class="form-label">Child *</label>
                        <select x-model="form.child_id" class="form-input">
                            <option value="">Select child</option>
                            <template x-for="child in childList" :key="child.id">
                                <option :value="child.id" x-text="child.full_name + ' (' + child.unique_id + ')'"></option>
                            </template>
                        </select>
                        <p class="form-error" x-show="errors.child_id" x-text="errors.child_id"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vaccine Name *</label>
                        <input type="text" x-model="form.vaccine_name" class="form-input" placeholder="e.g. BCG, OPV, DPT">
                        <p class="form-error" x-show="errors.vaccine_name" x-text="errors.vaccine_name"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vaccine Type</label>
                        <input type="text" x-model="form.vaccine_type" class="form-input" placeholder="e.g. Oral, Injectable">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dose Number</label>
                        <input type="number" x-model="form.dose_number" class="form-input" min="1" placeholder="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Next Due Date</label>
                        <input type="date" x-model="form.next_due_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date Administered</label>
                        <input type="date" x-model="form.date_administered" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Batch Number</label>
                        <input type="text" x-model="form.batch_number" class="form-input" placeholder="Batch #">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Site</label>
                        <input type="text" x-model="form.site" class="form-input" placeholder="e.g. Left arm">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Route</label>
                        <input type="text" x-model="form.route" class="form-input" placeholder="e.g. Oral, IM">
                    </div>
                    <div class="form-group sm:col-span-2">
                        <label class="form-label">Adverse Reactions</label>
                        <textarea x-model="form.adverse_reactions" class="form-input" rows="2" placeholder="Any adverse reactions"></textarea>
                    </div>
                    <div class="form-group sm:col-span-2">
                        <label class="form-label">Notes</label>
                        <textarea x-model="form.notes" class="form-input" rows="2" placeholder="Additional notes"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="closeModal()" class="btn-secondary">Cancel</button>
                <button @click="saveImmunization()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner"></span>
                    <span x-text="isEditing ? 'Update' : 'Save'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- View Vaccination Modal -->
    <div class="modal-overlay" id="viewImmunizationModal">
        <div class="modal-container modal-lg">
            <div class="modal-header">
                <h3>Vaccination Details</h3>
                <button @click="closeModal('viewImmunizationModal')" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <template x-if="viewData">
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 p-5 rounded-2xl bg-gray-50">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900" x-text="viewData.child_name"></h4>
                                <p class="text-sm text-gray-500" x-text="viewData.child_unique_id"></p>
                            </div>
                            <div class="ml-auto">
                                <span class="badge text-sm px-4 py-1.5" :class="statusClass(viewData.status)" x-text="viewData.status_label"></span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div class="bg-white rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Vaccine</p>
                                <p class="text-sm font-bold text-gray-900 mt-1" x-text="viewData.vaccine_name"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Type</p>
                                <p class="text-sm font-bold text-gray-900 mt-1" x-text="viewData.vaccine_type || '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Dose</p>
                                <p class="text-sm font-bold text-gray-900 mt-1" x-text="viewData.dose_number ? 'Dose ' + viewData.dose_number : '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Due Date</p>
                                <p class="text-sm font-bold text-gray-900 mt-1" x-text="viewData.next_due_date || '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Administered</p>
                                <p class="text-sm font-bold text-gray-900 mt-1" x-text="viewData.date_administered || '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Given By</p>
                                <p class="text-sm font-bold text-gray-900 mt-1" x-text="viewData.administered_by || '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Batch #</p>
                                <p class="text-sm font-bold text-gray-900 mt-1" x-text="viewData.batch_number || '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Site</p>
                                <p class="text-sm font-bold text-gray-900 mt-1" x-text="viewData.site || '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Route</p>
                                <p class="text-sm font-bold text-gray-900 mt-1" x-text="viewData.route || '-'"></p>
                            </div>
                        </div>
                        <div x-show="viewData.adverse_reactions" class="bg-white rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-400 font-semibold uppercase mb-1">Adverse Reactions</p>
                            <p class="text-sm text-gray-700" x-text="viewData.adverse_reactions"></p>
                        </div>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button @click="closeModal('viewImmunizationModal')" class="btn-secondary">Close</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation -->
    <div class="confirm-overlay" id="deleteImmunizationConfirm">
        <div class="confirm-container">
            <div class="confirm-icon bg-red-100">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Vaccination</h3>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this vaccination record?</p>
            <div class="flex gap-3 justify-center">
                <button @click="closeConfirm('deleteImmunizationConfirm')" class="btn-secondary">Cancel</button>
                <button @click="deleteImmunization()" class="btn-danger" :disabled="saving">
                    <span x-show="saving" class="spinner"></span>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function immunizationManager() {
        return {
            immunizations: [], childList: [],
            total: 0, currentPage: 1, perPage: 15, lastPage: 1, pages: [],
            search: '', statusFilter: '',
            isEditing: false, editingId: null, saving: false, deleteTarget: null, viewData: null,
            form: { child_id: '', vaccine_name: '', vaccine_type: '', dose_number: '', next_due_date: '', date_administered: '', batch_number: '', site: '', route: '', adverse_reactions: '', notes: '' },
            errors: {},

            init() { this.loadImmunizations(); this.loadChildren(); },

            async loadChildren() {
                try { const r = await fetch('/api/children?per_page=1000', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }); const d = await r.json(); this.childList = d.data || []; } catch(e) {}
            },

            async loadImmunizations() {
                try {
                    const params = new URLSearchParams({ page: this.currentPage });
                    if (this.search) params.append('search', this.search);
                    if (this.statusFilter) params.append('status', this.statusFilter);
                    const r = await fetch(`/api/immunizations?${params}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    const d = await r.json();
                    this.immunizations = d.data; this.total = d.total; this.currentPage = d.current_page; this.lastPage = d.last_page; this.perPage = d.per_page;
                    this.pages = []; for (let i = 1; i <= d.last_page; i++) this.pages.push(i);
                } catch(e) { showToast('Failed to load records', 'error'); }
            },

            changePage(p) { if (p < 1 || p > this.lastPage) return; this.currentPage = p; this.loadImmunizations(); },

            statusClass(s) {
                const c = { scheduled: 'bg-blue-100 text-blue-800', administered: 'bg-green-100 text-green-800', missed: 'bg-red-100 text-red-800', overdue: 'bg-orange-100 text-orange-800' };
                return c[s] || 'bg-gray-100 text-gray-600';
            },

            resetForm() {
                this.form = { child_id: '', vaccine_name: '', vaccine_type: '', dose_number: '', next_due_date: '', date_administered: '', batch_number: '', site: '', route: '', adverse_reactions: '', notes: '' };
                this.errors = {}; this.isEditing = false; this.editingId = null;
            },

            openAddModal() { this.resetForm(); openModal('immunizationModal'); },

            editImmunization(imm) {
                this.resetForm(); this.isEditing = true; this.editingId = imm.id;
                this.form.child_id = imm.child_id;
                this.form.vaccine_name = imm.vaccine_name;
                this.form.vaccine_type = imm.vaccine_type || '';
                this.form.dose_number = imm.dose_number || '';
                this.form.next_due_date = imm.next_due_date || '';
                this.form.date_administered = imm.date_administered || '';
                this.form.batch_number = imm.batch_number || '';
                this.form.site = imm.site || '';
                this.form.route = imm.route || '';
                this.form.adverse_reactions = imm.adverse_reactions || '';
                openModal('immunizationModal');
            },

            viewImmunization(imm) { this.viewData = imm; openModal('viewImmunizationModal'); },
            closeModal(id) { if (id) { closeModal(id); return; } closeModal('immunizationModal'); this.resetForm(); },

            async saveImmunization() {
                this.saving = true; this.errors = {};
                if (!this.form.child_id) { this.errors.child_id = 'Required'; this.saving = false; return; }
                if (!this.form.vaccine_name) { this.errors.vaccine_name = 'Required'; this.saving = false; return; }
                try {
                    const url = this.isEditing ? `/api/immunizations/${this.editingId}` : '/api/immunizations';
                    const method = this.isEditing ? 'PUT' : 'POST';
                    const r = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(this.form) });
                    const d = await r.json();
                    if (!r.ok) { if (d.errors) Object.keys(d.errors).forEach(k => this.errors[k] = d.errors[k][0]); else if (d.message) showToast(d.message, 'error'); this.saving = false; return; }
                    showToast(d.message, 'success'); this.closeModal(); this.loadImmunizations();
                } catch(e) { showToast('An error occurred', 'error'); }
                this.saving = false;
            },

            confirmDelete(imm) { this.deleteTarget = imm; openConfirm('deleteImmunizationConfirm'); },

            async deleteImmunization() {
                if (!this.deleteTarget) return; this.saving = true;
                try {
                    const r = await fetch(`/api/immunizations/${this.deleteTarget.id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                    const d = await r.json();
                    if (d.success) { showToast(d.message, 'success'); closeConfirm('deleteImmunizationConfirm'); this.deleteTarget = null; this.loadImmunizations(); }
                    else showToast(d.message, 'error');
                } catch(e) { showToast('Failed to delete', 'error'); }
                this.saving = false;
            },

            closeConfirm(id) { closeConfirm(id); this.deleteTarget = null; }
        };
    }
</script>
@endpush
@endsection