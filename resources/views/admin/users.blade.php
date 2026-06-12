@extends('layouts.app')

@section('header', 'Manage Users')

@section('title', config('app.name', 'Child Growth Monitor'))

@push('styles')
<style>
    .user-avatar-sm {
        width: 32px; height: 32px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.75rem; color: white;
        flex-shrink: 0;
    }
    .action-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 8px; border: none;
        cursor: pointer; transition: all 0.15s ease; text-decoration: none;
    }
    .action-btn:hover { transform: translateY(-1px); }
    .action-btn-view { background: #EFF6FF; color: #2563EB; }
    .action-btn-view:hover { background: #DBEAFE; }
    .action-btn-edit { background: #F1F5F9; color: #475569; }
    .action-btn-edit:hover { background: #E2E8F0; }
    .action-btn-toggle { background: #FEF3C7; color: #92400E; }
    .action-btn-toggle:hover { background: #FDE68A; }
    .action-btn-toggle.active { background: #ECFDF5; color: #065F46; }
    .action-btn-toggle.active:hover { background: #D1FAE5; }
    .action-btn-delete { background: #FEF2F2; color: #DC2626; }
    .action-btn-delete:hover { background: #FEE2E2; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $users->total() }}</p>
            <p class="text-xs text-gray-500 font-medium">Total Users</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ \App\Models\User::where('is_active', true)->count() }}</p>
            <p class="text-xs text-gray-500 font-medium">Active</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $totalByRole['admin'] }}</p>
            <p class="text-xs text-gray-500 font-medium">Admins</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ $totalByRole['nurse'] + $totalByRole['doctor'] }}</p>
            <p class="text-xs text-gray-500 font-medium">Healthcare</p>
        </div>
        <div class="content-card p-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $totalByRole['parent'] + $totalByRole['guardian'] }}</p>
            <p class="text-xs text-gray-500 font-medium">Parents/Guardians</p>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="content-card p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap items-center gap-3 flex-1">
                    <div class="relative">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="form-input pl-10" style="width: 240px;">
                    </div>
                    <select name="role" class="form-input" style="width: 160px;" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="nurse" {{ request('role') == 'nurse' ? 'selected' : '' }}>Nurse</option>
                        <option value="doctor" {{ request('role') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                        <option value="parent" {{ request('role') == 'parent' ? 'selected' : '' }}>Parent</option>
                        <option value="guardian" {{ request('role') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                    </select>
                    <select name="status" class="form-input" style="width: 160px;" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @if(request()->anyFilled(['search', 'role', 'status']))
                        <a href="{{ route('admin.users') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Clear filters</a>
                    @endif
                </form>
            </div>
            <button onclick="openAddUserModal()" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add User
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="content-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-main">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Children</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $avatarColors = ['#2563EB', '#8b5cf6', '#d946ef', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#10b981', '#14b8a6', '#06b6d4'];
                        $colorIdx = abs(crc32($user->email)) % count($avatarColors);
                        $roleLabels = ['admin'=>'Administrator','nurse'=>'Nurse','doctor'=>'Doctor','parent'=>'Parent','guardian'=>'Guardian'];
                        $roleClasses = ['admin'=>'bg-red-100 text-red-800','nurse'=>'bg-emerald-100 text-emerald-800','doctor'=>'bg-indigo-100 text-indigo-800','parent'=>'bg-blue-100 text-blue-800','guardian'=>'bg-purple-100 text-purple-800'];
                    @endphp
                    <tr>
                        <td class="font-mono text-xs font-medium text-gray-500">{{ $user->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="user-avatar-sm" style="background: {{ $avatarColors[$colorIdx] }};">
                                    <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-gray-500">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $roleClasses[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-500' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-gray-500 text-xs">{{ $user->children_count ?? '-' }}</td>
                        <td class="text-gray-500 text-xs">{{ $user->created_at ? date('M d, Y', strtotime($user->created_at)) : '-' }}</td>
                        <td>
                            <div class="flex gap-1.5 justify-center">
                                <!-- View button -->
                                <button onclick="openViewModal({{ $user->id }})" class="action-btn action-btn-view" title="View user">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <!-- Edit button -->
                                <button onclick="openEditModal({{ $user->id }})" class="action-btn action-btn-edit" title="Edit user">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <!-- Activate/Deactivate button -->
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" style="display:inline;" class="toggle-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn {{ $user->is_active ? 'action-btn-toggle' : 'action-btn-toggle active' }}" title="{{ $user->is_active ? 'Deactivate user' : 'Activate user' }}">
                                        @if($user->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                                <!-- Delete button -->
                                <button onclick="openDeleteConfirm({{ $user->id }}, '{{ addslashes($user->name) }}')" class="action-btn action-btn-delete" title="Delete user">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <p class="text-sm">No users found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
        </p>
        <div class="flex gap-1.5">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- ===== ADD/EDIT USER MODAL ===== -->
<div class="modal-overlay" id="userModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 id="userModalTitle">Add User</h3>
            <button type="button" class="modal-close" onclick="closeModal('userModal')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="userForm" method="POST" onsubmit="return submitUserForm(event)">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="userId" name="userId" value="">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" required placeholder="Enter full name">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" required placeholder="Enter email address">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Leave blank to keep current password">
                    <p class="text-xs text-gray-400 mt-1">Required for new users. Leave blank when editing.</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="role">Role</label>
                    <select id="role" name="role" class="form-input" required>
                        <option value="">Select a role</option>
                        <option value="admin">Administrator</option>
                        <option value="nurse">Nurse</option>
                        <option value="doctor">Doctor</option>
                        <option value="parent">Parent</option>
                        <option value="guardian">Guardian</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-input" placeholder="Enter phone number">
                </div>
                <div class="form-group">
                    <label class="form-label" for="facility_name">Facility Name</label>
                    <input type="text" id="facility_name" name="facility_name" class="form-input" placeholder="Enter facility name">
                </div>
                <div class="form-group">
                    <label class="form-label" for="license_number">License Number</label>
                    <input type="text" id="license_number" name="license_number" class="form-input" placeholder="Enter license number">
                </div>
                <div class="form-group">
                    <label class="form-label" for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-input" placeholder="Enter location">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('userModal')">Cancel</button>
                <button type="submit" class="btn-primary" id="userFormSubmit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span id="userFormSubmitText">Save</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ===== VIEW USER MODAL ===== -->
<div class="modal-overlay" id="viewUserModal">
    <div class="modal-container modal-lg">
        <div class="modal-header">
            <h3>User Details</h3>
            <button type="button" class="modal-close" onclick="closeModal('viewUserModal')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body" id="viewUserContent">
            <div class="flex items-center justify-center py-12">
                <div class="spinner" style="border-color:#E2E8F0;border-top-color:#2563EB;width:32px;height:32px;"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('viewUserModal')">Close</button>
        </div>
    </div>
</div>

<!-- ===== DELETE CONFIRMATION MODAL ===== -->
<div class="confirm-overlay" id="deleteConfirm">
    <div class="confirm-container">
        <div class="confirm-icon" style="background:#FEF2F2;margin:0 auto 16px;">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h3 style="font-size:1.1rem;font-weight:700;color:#0F172A;margin-bottom:8px;">Delete User</h3>
        <p style="color:#64748B;font-size:0.85rem;margin-bottom:24px;" id="deleteConfirmText">Are you sure you want to delete this user? This action cannot be undone.</p>
        <form id="deleteForm" method="POST" style="display:flex;gap:10px;justify-content:center;">
            @csrf
            @method('DELETE')
            <button type="button" class="btn-secondary" onclick="closeConfirm('deleteConfirm')">Cancel</button>
            <button type="submit" class="btn-danger">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </form>
    </div>
</div>

@push('styles')
<style>
    /* Style Laravel pagination to match the existing button design */
    .pagination { display: flex; gap: 6px; margin: 0; padding: 0; list-style: none; }
    .pagination li { display: inline-flex; }
    .pagination li a, .pagination li span {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 6px 14px; border-radius: 8px; font-size: 0.82rem; font-weight: 600;
        border: 1px solid #E2E8F0; background: white; color: #475569;
        cursor: pointer; transition: all 0.15s; text-decoration: none;
    }
    .pagination li a:hover { border-color: #2563EB; color: #2563EB; }
    .pagination li.active span { background: #2563EB; color: white; border-color: #2563EB; }
    .pagination li.disabled span { opacity: 0.5; cursor: not-allowed; }

    /* View modal user details layout */
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .detail-item {}
    .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #94A3B8; margin-bottom: 4px; }
    .detail-value { font-size: 0.9rem; font-weight: 500; color: #0F172A; }
    .detail-section { margin-bottom: 20px; }
    .detail-section-title { font-size: 0.85rem; font-weight: 700; color: #0F172A; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #E2E8F0; }
</style>
@endpush

@push('scripts')
<script>
    /**
     * ===== MODAL MANAGEMENT =====
     * Uses the global openModal/closeModal/openConfirm/closeConfirm from app layout.
     */

    // ===== ADD USER =====
    function openAddUserModal() {
        document.getElementById('userModalTitle').textContent = 'Add User';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('password').required = true;
        document.getElementById('password').placeholder = 'Enter password';
        document.querySelector('#userForm .form-group:has(#password) p').textContent = 'Required for new users.';
        document.getElementById('userFormSubmitText').textContent = 'Create User';
        openModal('userModal');
    }

    // ===== EDIT USER =====
    function openEditModal(userId) {
        document.getElementById('userModalTitle').textContent = 'Edit User';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = userId;
        document.getElementById('password').required = false;
        document.getElementById('password').placeholder = 'Leave blank to keep current';
        document.querySelector('#userForm .form-group:has(#password) p').textContent = 'Leave blank to keep current password.';
        document.getElementById('userFormSubmitText').textContent = 'Update User';

        // Show loading state on the button
        const submitBtn = document.getElementById('userFormSubmit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:16px;height:16px;border-width:2px;"></span> Loading...';

        // Fetch user data via API
        fetch('{{ url("api/users") }}/' + userId, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to load user data');
            return response.json();
        })
        .then(user => {
            document.getElementById('name').value = user.name || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('password').value = '';
            document.getElementById('role').value = user.role || '';
            document.getElementById('phone').value = user.phone || '';
            document.getElementById('facility_name').value = user.facility_name || '';
            document.getElementById('license_number').value = user.license_number || '';
            document.getElementById('location').value = user.location || '';

            // Restore submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> <span id="userFormSubmitText">Update User</span>';

            openModal('userModal');
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> <span id="userFormSubmitText">Update User</span>';
            showToast('Error loading user data: ' + error.message, 'error');
        });
    }

    // ===== SUBMIT USER FORM (Add/Edit) =====
    function submitUserForm(event) {
        event.preventDefault();

        const form = document.getElementById('userForm');
        const userId = document.getElementById('userId').value;
        const isEdit = userId !== '';
        const submitBtn = document.getElementById('userFormSubmit');

        // Disable button to prevent double submit
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:16px;height:16px;border-width:2px;"></span> Saving...';

        const formData = new FormData(form);
        const data = {
            name: formData.get('name'),
            email: formData.get('email'),
            role: formData.get('role'),
            phone: formData.get('phone') || '',
            facility_name: formData.get('facility_name') || '',
            license_number: formData.get('license_number') || '',
            location: formData.get('location') || '',
        };

        if (formData.get('password')) {
            data.password = formData.get('password');
        }

        let url = '{{ route("api.users.store") }}';
        let method = 'POST';

        if (isEdit) {
            url = '{{ url("api/users") }}/' + userId;
            method = 'PUT';
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> <span id="userFormSubmitText">' + (isEdit ? 'Update User' : 'Create User') + '</span>';

            if (result.success) {
                closeModal('userModal');
                showToast(result.message, 'success');
                // Reload page to reflect changes
                setTimeout(() => { window.location.reload(); }, 800);
            } else {
                showToast(result.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> <span id="userFormSubmitText">' + (isEdit ? 'Update User' : 'Create User') + '</span>';
            showToast('Network error: ' + error.message, 'error');
        });

        return false;
    }

    // ===== VIEW USER =====
    function openViewModal(userId) {
        const content = document.getElementById('viewUserContent');
        content.innerHTML = '<div class="flex items-center justify-center py-12"><div class="spinner" style="border-color:#E2E8F0;border-top-color:#2563EB;width:32px;height:32px;"></div></div>';
        openModal('viewUserModal');

        fetch('{{ url("api/users") }}/' + userId, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to load user details');
            return response.json();
        })
        .then(user => {
            const roleLabels = {
                'admin': 'Administrator',
                'nurse': 'Nurse',
                'doctor': 'Doctor',
                'parent': 'Parent',
                'guardian': 'Guardian'
            };
            const roleColors = {
                'admin': 'bg-red-100 text-red-800',
                'nurse': 'bg-emerald-100 text-emerald-800',
                'doctor': 'bg-indigo-100 text-indigo-800',
                'parent': 'bg-blue-100 text-blue-800',
                'guardian': 'bg-purple-100 text-purple-800'
            };

            const statusClass = user.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-500';
            const statusLabel = user.is_active ? 'Active' : 'Inactive';
            const createdDate = user.created_at ? new Date(user.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '-';

            content.innerHTML = `
                <div class="detail-section">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="user-avatar-sm" style="width:48px;height:48px;font-size:1.1rem;background:#2563EB;">
                            <span>${user.name ? user.name.charAt(0).toUpperCase() : '?'}</span>
                        </div>
                        <div>
                            <h4 style="font-size:1.05rem;font-weight:700;color:#0F172A;">${user.name || 'N/A'}</h4>
                            <p style="font-size:0.8rem;color:#64748B;">${user.email || ''}</p>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">Account Information</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">User ID</div>
                            <div class="detail-value">#${user.id || '-'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Role</div>
                            <div class="detail-value"><span class="badge ${roleColors[user.role] || 'bg-gray-100 text-gray-800'}">${roleLabels[user.role] || user.role || '-'}</span></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status</div>
                            <div class="detail-value"><span class="badge ${statusClass}">${statusLabel}</span></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Created</div>
                            <div class="detail-value">${createdDate}</div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">Contact Information</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value">${user.phone || '-'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Location</div>
                            <div class="detail-value">${user.location || '-'}</div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <div class="detail-section-title">Professional Information</div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Facility</div>
                            <div class="detail-value">${user.facility_name || '-'}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">License Number</div>
                            <div class="detail-value">${user.license_number || '-'}</div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-red-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-red-500">Error loading user details: ${error.message}</p>
                </div>
            `;
        });
    }

    // ===== DELETE CONFIRM =====
    function openDeleteConfirm(userId, userName) {
        const form = document.getElementById('deleteForm');
        form.action = '{{ url("admin/users") }}/' + userId;
        document.getElementById('deleteConfirmText').textContent = 'Are you sure you want to delete "' + userName + '"? This action cannot be undone.';
        openConfirm('deleteConfirm');
    }

    // ===== HANDLE TOGGLE STATUS FORMS VIA AJAX =====
    document.addEventListener('DOMContentLoaded', function() {
        // Intercept toggle forms to submit via AJAX
        document.querySelectorAll('.toggle-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: new FormData(form)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showToast(result.message, 'success');
                        setTimeout(() => { window.location.reload(); }, 500);
                    } else {
                        showToast(result.message || 'An error occurred', 'error');
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    showToast('Network error: ' + error.message, 'error');
                    submitBtn.disabled = false;
                });
            });
        });
    });

    // ===== HANDLE DELETE FORM SUBMISSION VIA AJAX =====
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner" style="border-color:rgba(255,255,255,0.3);border-top-color:#fff;width:16px;height:16px;border-width:2px;"></span> Deleting...';

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    closeConfirm('deleteConfirm');
                    showToast(result.message, 'success');
                    setTimeout(() => { window.location.reload(); }, 800);
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Delete';
                    showToast(result.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Delete';
                showToast('Network error: ' + error.message, 'error');
            });
        });
    });
</script>
@endpush
@endsection