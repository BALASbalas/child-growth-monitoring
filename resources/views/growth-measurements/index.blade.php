@extends('layouts.app')

@section('header', 'Growth Measurements')

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
<div class="space-y-6" x-data="measurementManager()">
    <!-- Toolbar -->
    <div class="content-card p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="search" @input.debounce.300ms="loadMeasurements()" placeholder="Search by child name..." class="form-input pl-10" style="width: 220px;">
                </div>
                <input type="date" x-model="dateFrom" @change="loadMeasurements()" class="form-input" style="width: 150px;" placeholder="From date">
                <input type="date" x-model="dateTo" @change="loadMeasurements()" class="form-input" style="width: 150px;" placeholder="To date">
            </div>
            <button @click="openAddModal()" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Measurement
            </button>
        </div>
    </div>

    <!-- Measurements Table -->
    <div class="content-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-main">
                <thead>
                    <tr>
                        <th>Child Name</th>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Weight</th>
                        <th>Height</th>
                        <th>BMI</th>
                        <th>Nutrition Status</th>
                        <th>Stunting</th>
                        <th>Wasting</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="measurements.length === 0">
                        <tr>
                            <td colspan="10" class="text-center py-12 text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                <p class="text-sm">No measurements found</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(m, index) in measurements" :key="m.id">
                        <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'">
                            <td class="font-semibold text-gray-900" x-text="m.child_name"></td>
                            <td class="font-mono text-xs text-gray-500" x-text="m.child_unique_id"></td>
                            <td class="text-xs" x-text="m.measurement_date"></td>
                            <td x-text="m.weight ? m.weight + ' kg' : '-'"></td>
                            <td x-text="m.height ? m.height + ' cm' : '-'"></td>
                            <td x-text="m.bmi ? m.bmi : '-'"></td>
                            <td>
                                <span class="badge" :class="nutritionClass(m.nutritional_status)" x-text="m.nutritional_status || 'N/A'"></span>
                            </td>
                            <td>
                                <span class="badge" :class="stuntingClass(m.stunting_status)" x-text="m.stunting_status || 'Normal'"></span>
                            </td>
                            <td>
                                <span class="badge" :class="wastingClass(m.wasting_status)" x-text="m.wasting_status || 'Normal'"></span>
                            </td>
                            <td>
                                <div class="flex gap-1.5 justify-center">
                                    <button @click="viewMeasurement(m)" class="btn-xs" style="background:#EFF6FF;color:#2563EB;border:none;border-radius:8px;font-weight:600;">View</button>
                                    <button @click="editMeasurement(m)" class="btn-xs" style="background:#F1F5F9;color:#475569;border:none;border-radius:8px;font-weight:600;">Edit</button>
                                    <button @click="confirmDelete(m)" class="btn-xs" style="background:#FEF2F2;color:#DC2626;border:none;border-radius:8px;font-weight:600;">Delete</button>
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
        <p class="pagination-info" x-text="'Showing ' + ((currentPage - 1) * perPage + 1) + ' to ' + Math.min(currentPage * perPage, total) + ' of ' + total + ' measurements'"></p>
        <div class="pagination flex gap-1.5">
            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
            <template x-for="page in pages" :key="page">
                <button @click="changePage(page)" :class="{'active': page === currentPage}" x-text="page"></button>
            </template>
            <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage">Next</button>
        </div>
    </div>

    <!-- Add/Edit Measurement Modal -->
    <div class="modal-overlay" id="measurementModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 x-text="isEditing ? 'Edit Measurement' : 'Add Measurement'"></h3>
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
                        <label class="form-label">Measurement Date *</label>
                        <input type="date" x-model="form.measurement_date" class="form-input">
                        <p class="form-error" x-show="errors.measurement_date" x-text="errors.measurement_date"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temperature (°C)</label>
                        <input type="number" step="0.1" x-model="form.temperature" class="form-input" placeholder="e.g. 36.5">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" step="0.01" x-model="form.weight" class="form-input" placeholder="e.g. 8.5">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Height (cm)</label>
                        <input type="number" step="0.1" x-model="form.height" class="form-input" placeholder="e.g. 68">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Head Circumference (cm)</label>
                        <input type="number" step="0.1" x-model="form.head_circumference" class="form-input" placeholder="e.g. 44">
                    </div>
                    <div class="form-group">
                        <label class="form-label">MUAC (cm)</label>
                        <input type="number" step="0.1" x-model="form.mid_upper_arm_circumference" class="form-input" placeholder="e.g. 14.5">
                    </div>
                    <div class="form-group sm:col-span-2">
                        <label class="form-label">Clinical Notes</label>
                        <textarea x-model="form.clinical_notes" class="form-input" rows="2" placeholder="Clinical notes"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="closeModal()" class="btn-secondary">Cancel</button>
                <button @click="saveMeasurement()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner"></span>
                    <span x-text="isEditing ? 'Update' : 'Save'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- View Measurement Modal -->
    <div class="modal-overlay" id="viewMeasurementModal">
        <div class="modal-container modal-lg">
            <div class="modal-header">
                <h3>Measurement Details</h3>
                <button @click="closeModal('viewMeasurementModal')" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <template x-if="viewData">
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 p-5 rounded-2xl bg-gray-50">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900" x-text="viewData.child_name"></h4>
                                <p class="text-sm text-gray-500" x-text="viewData.child_unique_id"></p>
                            </div>
                            <div class="ml-auto text-right">
                                <p class="text-sm font-semibold text-gray-900" x-text="viewData.measurement_date"></p>
                                <p class="text-xs text-gray-500">Measured by: <span x-text="viewData.measured_by"></span></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Weight</p>
                                <p class="text-lg font-bold text-gray-900 mt-1" x-text="viewData.weight ? viewData.weight + ' kg' : '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Height</p>
                                <p class="text-lg font-bold text-gray-900 mt-1" x-text="viewData.height ? viewData.height + ' cm' : '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                                <p class="text-xs text-gray-400 font-semibold uppercase">BMI</p>
                                <p class="text-lg font-bold text-gray-900 mt-1" x-text="viewData.bmi || '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Head Circ.</p>
                                <p class="text-lg font-bold text-gray-900 mt-1" x-text="viewData.head_circumference ? viewData.head_circumference + ' cm' : '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                                <p class="text-xs text-gray-400 font-semibold uppercase">MUAC</p>
                                <p class="text-lg font-bold text-gray-900 mt-1" x-text="viewData.mid_upper_arm_circumference ? viewData.mid_upper_arm_circumference + ' cm' : '-'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Nutrition</p>
                                <p class="text-lg font-bold text-gray-900 mt-1" x-text="viewData.nutritional_status || 'N/A'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Stunting</p>
                                <p class="text-lg font-bold text-gray-900 mt-1" x-text="viewData.stunting_status || 'Normal'"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                                <p class="text-xs text-gray-400 font-semibold uppercase">Wasting</p>
                                <p class="text-lg font-bold text-gray-900 mt-1" x-text="viewData.wasting_status || 'Normal'"></p>
                            </div>
                        </div>
                        <div x-show="viewData.clinical_notes" class="bg-white rounded-xl p-4 border border-gray-100">
                            <p class="text-xs text-gray-400 font-semibold uppercase mb-1">Clinical Notes</p>
                            <p class="text-sm text-gray-700" x-text="viewData.clinical_notes"></p>
                        </div>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button @click="closeModal('viewMeasurementModal')" class="btn-secondary">Close</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation -->
    <div class="confirm-overlay" id="deleteMeasurementConfirm">
        <div class="confirm-container">
            <div class="confirm-icon bg-red-100">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Measurement</h3>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this measurement? This cannot be undone.</p>
            <div class="flex gap-3 justify-center">
                <button @click="closeConfirm('deleteMeasurementConfirm')" class="btn-secondary">Cancel</button>
                <button @click="deleteMeasurement()" class="btn-danger" :disabled="saving">
                    <span x-show="saving" class="spinner"></span>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function measurementManager() {
        return {
            measurements: [],
            childList: [],
            total: 0, currentPage: 1, perPage: 15, lastPage: 1, pages: [],
            search: '', dateFrom: '', dateTo: '',
            isEditing: false, editingId: null, saving: false, deleteTarget: null, viewData: null,
            form: { child_id: '', measurement_date: '', weight: '', height: '', head_circumference: '', mid_upper_arm_circumference: '', temperature: '', clinical_notes: '' },
            errors: {},

            init() {
                this.loadMeasurements();
                this.loadChildren();
            },

            async loadChildren() {
                try {
                    const r = await fetch('/api/children?per_page=1000', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const d = await r.json();
                    this.childList = d.data || [];
                } catch(e) {}
            },

            async loadMeasurements() {
                try {
                    const params = new URLSearchParams({ page: this.currentPage });
                    if (this.search) params.append('search', this.search);
                    if (this.dateFrom) params.append('date_from', this.dateFrom);
                    if (this.dateTo) params.append('date_to', this.dateTo);
                    const r = await fetch(`/api/growth-measurements?${params}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const d = await r.json();
                    this.measurements = d.data;
                    this.total = d.total; this.currentPage = d.current_page; this.lastPage = d.last_page; this.perPage = d.per_page;
                    this.pages = []; for (let i = 1; i <= d.last_page; i++) this.pages.push(i);
                } catch(e) { showToast('Failed to load measurements', 'error'); }
            },

            changePage(p) { if (p < 1 || p > this.lastPage) return; this.currentPage = p; this.loadMeasurements(); },

            nutritionClass(s) {
                const c = { severe_underweight: 'bg-red-100 text-red-800', moderate_underweight: 'bg-yellow-100 text-yellow-800', normal: 'bg-green-100 text-green-800', overweight: 'bg-orange-100 text-orange-800', obese: 'bg-red-200 text-red-900' };
                return c[s] || 'bg-gray-100 text-gray-600';
            },
            stuntingClass(s) { return s === 'stunted' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; },
            wastingClass(s) { return s === 'wasted' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; },

            resetForm() {
                this.form = { child_id: '', measurement_date: '', weight: '', height: '', head_circumference: '', mid_upper_arm_circumference: '', temperature: '', clinical_notes: '' };
                this.errors = {}; this.isEditing = false; this.editingId = null;
            },

            openAddModal() { this.resetForm(); openModal('measurementModal'); },

            editMeasurement(m) {
                this.resetForm();
                this.isEditing = true; this.editingId = m.id;
                this.form.child_id = m.child_id;
                this.form.measurement_date = m.measurement_date;
                this.form.weight = m.weight || '';
                this.form.height = m.height || '';
                this.form.head_circumference = m.head_circumference || '';
                this.form.mid_upper_arm_circumference = m.mid_upper_arm_circumference || '';
                this.form.temperature = '';
                this.form.clinical_notes = m.clinical_notes || '';
                openModal('measurementModal');
            },

            viewMeasurement(m) {
                this.viewData = m;
                openModal('viewMeasurementModal');
            },

            closeModal(id) { if (id) { closeModal(id); return; } closeModal('measurementModal'); this.resetForm(); },

            async saveMeasurement() {
                this.saving = true; this.errors = {};
                if (!this.form.child_id) { this.errors.child_id = 'Required'; this.saving = false; return; }
                if (!this.form.measurement_date) { this.errors.measurement_date = 'Required'; this.saving = false; return; }
                try {
                    const url = this.isEditing ? `/api/growth-measurements/${this.editingId}` : '/api/growth-measurements';
                    const method = this.isEditing ? 'PUT' : 'POST';
                    const r = await fetch(url, {
                        method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.form)
                    });
                    const d = await r.json();
                    if (!r.ok) { if (d.errors) Object.keys(d.errors).forEach(k => this.errors[k] = d.errors[k][0]); else if (d.message) showToast(d.message, 'error'); this.saving = false; return; }
                    showToast(d.message, 'success'); this.closeModal(); this.loadMeasurements();
                } catch(e) { showToast('An error occurred', 'error'); }
                this.saving = false;
            },

            confirmDelete(m) { this.deleteTarget = m; openConfirm('deleteMeasurementConfirm'); },

            async deleteMeasurement() {
                if (!this.deleteTarget) return; this.saving = true;
                try {
                    const r = await fetch(`/api/growth-measurements/${this.deleteTarget.id}`, { method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    const d = await r.json();
                    if (d.success) { showToast(d.message, 'success'); closeConfirm('deleteMeasurementConfirm'); this.deleteTarget = null; this.loadMeasurements(); }
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