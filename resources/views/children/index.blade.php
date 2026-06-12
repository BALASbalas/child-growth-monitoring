@extends('layouts.app')

@section('header', 'Manage Children')

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
@php $vaccinateMode = request('vaccinate') == 1; @endphp

<div class="space-y-6" x-data="childManager()">
    <!-- Vaccine Mode Banner -->
    @if($vaccinateMode)
    <div class="content-card" style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5);border-color:#A7F3D0;">
        <div class="p-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-white/80">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-emerald-900 text-lg">Select a Child to Vaccinate</h3>
                    <p class="text-sm text-emerald-700">Choose a child from the list below to record their vaccination</p>
                </div>
            </div>
            <a href="{{ route('immunizations.index') }}" class="btn-sm" style="background:white;color:#475569;border:1px solid #A7F3D0;border-radius:10px;font-weight:600;text-decoration:none;padding:8px 18px;">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                View Vaccination Records
            </a>
        </div>
    </div>
    @endif

    <!-- Toolbar -->
    <div class="content-card p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Search -->
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="search" @input.debounce.300ms="loadChildren()" placeholder="{{ $vaccinateMode ? 'Search child to vaccinate...' : 'Search children...' }}" class="form-input pl-10" style="width: 220px;">
                </div>
                <!-- Filter by Gender -->
                <select x-model="genderFilter" @change="loadChildren()" class="form-input" style="width: 140px;">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <!-- Filter by Age Group -->
                <select x-model="ageFilter" @change="loadChildren()" class="form-input" style="width: 150px;">
                    <option value="">All Ages</option>
                    <option value="0-6">0-6 months</option>
                    <option value="6-12">6-12 months</option>
                    <option value="12-24">1-2 years</option>
                    <option value="24-60">2-5 years</option>
                    <option value="60+">5+ years</option>
                </select>
                <!-- Filter by Date -->
                <input type="date" x-model="dateFrom" @change="loadChildren()" class="form-input" style="width: 150px;" placeholder="From date">
                <input type="date" x-model="dateTo" @change="loadChildren()" class="form-input" style="width: 150px;" placeholder="To date">
            </div>
            @if(!$vaccinateMode && (Auth::user()->isHealthcareWorker() || Auth::user()->isDoctor()))
            <button @click="openAddModal()" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Register Child
            </button>
            @endif
        </div>
    </div>

    <!-- Children Table -->
    <div class="content-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-main">
                <thead>
                    <tr>
                        @if(!$vaccinateMode)
                        <th>#</th>
                        @endif
                        <th>Full Name</th>
                        <th>ID</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>DOB</th>
                        @if(!$vaccinateMode)
                        <th>Nutrition</th>
                        <th>Vaccines</th>
                        @endif
                        <th class="text-center">{{ $vaccinateMode ? 'Action' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="children.length === 0">
                        <tr>
                            <td colspan="{{ $vaccinateMode ? '6' : '9' }}" class="text-center py-12 text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm">{{ $vaccinateMode ? 'No children found to vaccinate' : 'No children found' }}</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(child, index) in children" :key="child.id">
                        <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'">
                            @if(!$vaccinateMode)
                            <td class="text-gray-500 text-xs font-mono" x-text="((currentPage - 1) * perPage) + (index + 1)"></td>
                            @endif
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs"
                                         :style="'background:' + (child.sex === 'male' ? '#2563EB' : '#EC4899')">
                                        <span x-text="child.full_name?.charAt(0)"></span>
                                    </div>
                                    <span class="font-semibold text-gray-900" x-text="child.full_name"></span>
                                </div>
                            </td>
                            <td class="font-mono text-xs text-gray-500" x-text="child.unique_id"></td>
                            <td>
                                <span class="badge" :class="child.sex === 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'" x-text="child.sex === 'male' ? 'Male' : 'Female'"></span>
                            </td>
                            <td x-text="child.age_string"></td>
                            <td class="text-xs" x-text="child.date_of_birth"></td>
                            @if(!$vaccinateMode)
                            <td>
                                <span class="badge" :class="child.nutrition_color" x-text="child.nutrition_label"></span>
                            </td>
                            <td class="text-center">
                                <span class="font-semibold" :class="child.vaccine_progress.split('/')[0] === child.vaccine_progress.split('/')[1] && parseInt(child.vaccine_progress.split('/')[1]) > 0 ? 'text-green-600' : 'text-amber-600'" x-text="child.vaccine_progress"></span>
                            </td>
                            @endif
                            <td>
                                <div class="flex gap-1.5 justify-center">
                                    @if($vaccinateMode)
                                    <!-- Vaccine Mode: Show Vaccinate button -->
                                    <a :href="'/immunizations/create?child_id=' + child.id" class="btn-sm" style="background:linear-gradient(135deg,#059669,#047857);color:white;border:none;border-radius:10px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:8px 18px;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        Vaccinate
                                    </a>
                                    <a :href="'/children/' + child.id + '/immunizations'" class="btn-xs" style="background:#F1F5F9;color:#475569;border:none;border-radius:8px;font-weight:600;text-decoration:none;">History</a>
                                    @else
                                    <a :href="'/children/' + child.id" class="btn-xs" style="background:#EFF6FF;color:#2563EB;border:none;border-radius:8px;font-weight:600;text-decoration:none;">View</a>
                                    @if(Auth::user()->isHealthcareWorker() || Auth::user()->isDoctor())
                                    <button @click="editChild(child)" class="btn-xs" style="background:#F1F5F9;color:#475569;border:none;border-radius:8px;font-weight:600;">Edit</button>
                                    <button @click="toggleStatus(child)" class="btn-xs" :style="child.is_active ? 'background:#FEF3C7;color:#92400E;border:none;border-radius:8px;font-weight:600;' : 'background:#ECFDF5;color:#065F46;border:none;border-radius:8px;font-weight:600;'" x-text="child.is_active ? 'Deactivate' : 'Activate'"></button>
                                    <button @click="confirmDelete(child)" class="btn-xs" style="background:#FEF2F2;color:#DC2626;border:none;border-radius:8px;font-weight:600;">Delete</button>
                                    @endif
                                    @endif
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
        <p class="pagination-info" x-text="'Showing ' + ((currentPage - 1) * perPage + 1) + ' to ' + Math.min(currentPage * perPage, total) + ' of ' + total + ' children'"></p>
        <div class="pagination flex gap-1.5">
            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
            <template x-for="page in pages" :key="page">
                <button @click="changePage(page)" :class="{'active': page === currentPage}" x-text="page"></button>
            </template>
            <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage">Next</button>
        </div>
    </div>

    <!-- Add/Edit Child Modal -->
    <div class="modal-overlay" id="childModal">
        <div class="modal-container modal-lg">
            <div class="modal-header">
                <h3 x-text="isEditing ? 'Edit Child' : 'Register New Child'"></h3>
                <button @click="closeModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label class="form-label">First Name *</label>
                        <input type="text" x-model="form.first_name" class="form-input" placeholder="First name">
                        <p class="form-error" x-show="errors.first_name" x-text="errors.first_name"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" x-model="form.middle_name" class="form-input" placeholder="Middle name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name *</label>
                        <input type="text" x-model="form.last_name" class="form-input" placeholder="Last name">
                        <p class="form-error" x-show="errors.last_name" x-text="errors.last_name"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth *</label>
                        <input type="date" x-model="form.date_of_birth" class="form-input">
                        <p class="form-error" x-show="errors.date_of_birth" x-text="errors.date_of_birth"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sex *</label>
                        <select x-model="form.sex" class="form-input">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <p class="form-error" x-show="errors.sex" x-text="errors.sex"></p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gestational Age (weeks)</label>
                        <input type="number" x-model="form.gestational_age_weeks" class="form-input" placeholder="e.g. 40" min="20" max="44">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Birth Weight (kg)</label>
                        <input type="number" step="0.01" x-model="form.birth_weight" class="form-input" placeholder="e.g. 3.2">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Birth Length (cm)</label>
                        <input type="number" step="0.1" x-model="form.birth_length" class="form-input" placeholder="e.g. 50">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Birth Head Circ. (cm)</label>
                        <input type="number" step="0.1" x-model="form.birth_head_circumference" class="form-input" placeholder="e.g. 34">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mother's Name</label>
                        <input type="text" x-model="form.mother_name" class="form-input" placeholder="Mother's name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mother's Phone</label>
                        <input type="text" x-model="form.mother_phone" class="form-input" placeholder="Mother's phone">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Father's Name</label>
                        <input type="text" x-model="form.father_name" class="form-input" placeholder="Father's name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Father's Phone</label>
                        <input type="text" x-model="form.father_phone" class="form-input" placeholder="Father's phone">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Guardian Name</label>
                        <input type="text" x-model="form.guardian_name" class="form-input" placeholder="Guardian name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Guardian Phone</label>
                        <input type="text" x-model="form.guardian_phone" class="form-input" placeholder="Guardian phone">
                    </div>
                    <div class="form-group sm:col-span-3">
                        <label class="form-label">Address</label>
                        <input type="text" x-model="form.address" class="form-input" placeholder="Address">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location / Village</label>
                        <input type="text" x-model="form.location" class="form-input" placeholder="Village/Street">
                    </div>
                    <div class="form-group">
                        <label class="form-label">District</label>
                        <input type="text" x-model="form.district" class="form-input" placeholder="District">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Region</label>
                        <input type="text" x-model="form.region" class="form-input" placeholder="Region">
                    </div>
                    <div class="form-group sm:col-span-3">
                        <label class="form-label">Medical History</label>
                        <textarea x-model="form.medical_history" class="form-input" rows="2" placeholder="Medical history"></textarea>
                    </div>
                    <div class="form-group sm:col-span-3">
                        <label class="form-label">Notes</label>
                        <textarea x-model="form.notes" class="form-input" rows="2" placeholder="Additional notes"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="closeModal()" class="btn-secondary">Cancel</button>
                <button @click="saveChild()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner"></span>
                    <span x-text="isEditing ? 'Update Child' : 'Register Child'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation -->
    <div class="confirm-overlay" id="deleteChildConfirm">
        <div class="confirm-container">
            <div class="confirm-icon bg-red-100">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Child Record</h3>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete <span class="font-semibold text-gray-700" x-text="deleteTarget ? deleteTarget.full_name : ''"></span>? This will also remove all measurements and vaccination records.</p>
            <div class="flex gap-3 justify-center">
                <button @click="closeConfirm('deleteChildConfirm')" class="btn-secondary">Cancel</button>
                <button @click="deleteChild()" class="btn-danger" :disabled="saving">
                    <span x-show="saving" class="spinner"></span>
                    Delete Record
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function childManager() {
        return {
            children: [],
            total: 0,
            currentPage: 1,
            perPage: 15,
            lastPage: 1,
            pages: [],
            search: '',
            genderFilter: '',
            ageFilter: '',
            dateFrom: '',
            dateTo: '',
            isEditing: false,
            editingId: null,
            saving: false,
            deleteTarget: null,
            form: {
                first_name: '', middle_name: '', last_name: '', date_of_birth: '', sex: '',
                gestational_age_weeks: '', birth_weight: '', birth_length: '', birth_head_circumference: '',
                mother_name: '', mother_phone: '', father_name: '', father_phone: '',
                guardian_name: '', guardian_phone: '', address: '', location: '', district: '', region: '',
                medical_history: '', notes: ''
            },
            errors: {},

            init() {
                this.loadChildren();
            },

            async loadChildren() {
                try {
                    const params = new URLSearchParams({ page: this.currentPage });
                    if (this.search) params.append('search', this.search);
                    if (this.genderFilter) params.append('sex', this.genderFilter);
                    if (this.ageFilter) params.append('age_group', this.ageFilter);
                    if (this.dateFrom) params.append('date_from', this.dateFrom);
                    if (this.dateTo) params.append('date_to', this.dateTo);

                    const response = await fetch(`/api/children?${params}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();
                    this.children = data.data;
                    this.total = data.total;
                    this.currentPage = data.current_page;
                    this.lastPage = data.last_page;
                    this.perPage = data.per_page;
                    this.pages = [];
                    for (let i = 1; i <= data.last_page; i++) this.pages.push(i);
                } catch (e) {
                    showToast('Failed to load children', 'error');
                }
            },

            changePage(page) {
                if (page < 1 || page > this.lastPage) return;
                this.currentPage = page;
                this.loadChildren();
            },

            resetForm() {
                this.form = {
                    first_name: '', middle_name: '', last_name: '', date_of_birth: '', sex: '',
                    gestational_age_weeks: '', birth_weight: '', birth_length: '', birth_head_circumference: '',
                    mother_name: '', mother_phone: '', father_name: '', father_phone: '',
                    guardian_name: '', guardian_phone: '', address: '', location: '', district: '', region: '',
                    medical_history: '', notes: ''
                };
                this.errors = {};
                this.isEditing = false;
                this.editingId = null;
            },

            openAddModal() {
                this.resetForm();
                openModal('childModal');
            },

            editChild(child) {
                this.resetForm();
                this.isEditing = true;
                this.editingId = child.id;
                this.form.first_name = child.full_name?.split(' ')[0] || '';
                this.form.last_name = child.full_name?.split(' ').slice(-1)[0] || '';
                this.form.date_of_birth = child.date_of_birth || '';
                this.form.sex = child.sex || '';

                // Fetch full details for edit
                fetch(`/api/children/${child.id}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    this.form.first_name = data.first_name || '';
                    this.form.middle_name = data.middle_name || '';
                    this.form.last_name = data.last_name || '';
                    this.form.date_of_birth = data.date_of_birth ? data.date_of_birth.split(' ')[0] : '';
                    this.form.sex = data.sex || '';
                    this.form.gestational_age_weeks = data.gestational_age_weeks || '';
                    this.form.birth_weight = data.birth_weight || '';
                    this.form.birth_length = data.birth_length || '';
                    this.form.birth_head_circumference = data.birth_head_circumference || '';
                    this.form.mother_name = data.mother_name || '';
                    this.form.mother_phone = data.mother_phone || '';
                    this.form.father_name = data.father_name || '';
                    this.form.father_phone = data.father_phone || '';
                    this.form.guardian_name = data.guardian_name || '';
                    this.form.guardian_phone = data.guardian_phone || '';
                    this.form.address = data.address || '';
                    this.form.location = data.location || '';
                    this.form.district = data.district || '';
                    this.form.region = data.region || '';
                    this.form.medical_history = data.medical_history || '';
                    this.form.notes = data.notes || '';
                    openModal('childModal');
                })
                .catch(() => showToast('Failed to load child details', 'error'));
            },

            closeModal() {
                closeModal('childModal');
                this.resetForm();
            },

            async saveChild() {
                this.saving = true;
                this.errors = {};
                try {
                    if (!this.form.first_name) { this.errors.first_name = 'Required'; this.saving = false; return; }
                    if (!this.form.last_name) { this.errors.last_name = 'Required'; this.saving = false; return; }
                    if (!this.form.date_of_birth) { this.errors.date_of_birth = 'Required'; this.saving = false; return; }
                    if (!this.form.sex) { this.errors.sex = 'Required'; this.saving = false; return; }

                    const url = this.isEditing ? `/api/children/${this.editingId}` : '/api/children';
                    const method = this.isEditing ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => { this.errors[key] = data.errors[key][0]; });
                        } else if (data.message) {
                            showToast(data.message, 'error');
                        }
                        this.saving = false;
                        return;
                    }

                    showToast(data.message, 'success');
                    this.closeModal();
                    this.loadChildren();
                } catch (e) {
                    showToast('An error occurred', 'error');
                }
                this.saving = false;
            },

            toggleStatus(child) {
                const action = child.is_active ? 'deactivate' : 'activate';
                if (!confirm(`Are you sure you want to ${action} this child?`)) return;

                fetch(`/api/children/${child.id}/toggle-status`, {
                    method: 'PATCH',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        this.loadChildren();
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(() => showToast('Failed to update status', 'error'));
            },

            confirmDelete(child) {
                this.deleteTarget = child;
                openConfirm('deleteChildConfirm');
            },

            async deleteChild() {
                if (!this.deleteTarget) return;
                this.saving = true;
                try {
                    const response = await fetch(`/api/children/${this.deleteTarget.id}`, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    const data = await response.json();
                    if (data.success) {
                        showToast(data.message, 'success');
                        this.closeConfirm('deleteChildConfirm');
                        this.deleteTarget = null;
                        this.loadChildren();
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (e) {
                    showToast('Failed to delete child', 'error');
                }
                this.saving = false;
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