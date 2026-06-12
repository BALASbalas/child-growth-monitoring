@auth
@php
    $cu = Auth::user();
    $currentRoute = request()->route()->getName();
@endphp

<aside class="app-sidebar flex flex-col custom-scroll overflow-hidden">
    <!-- Logo Area -->
    <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>
        <div>
            <span class="text-sm font-bold" style="color: #0F172A;">Child<span style="color: #2563EB;">Growth</span></span>
            <p class="text-[10px] text-gray-400">Monitoring System</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden custom-scroll py-3 space-y-0.5 px-3">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-item {{ str_starts_with($currentRoute, 'dashboard') || str_starts_with($currentRoute, 'admin.dashboard') || str_starts_with($currentRoute, 'nurse.') || str_starts_with($currentRoute, 'doctor.') || str_starts_with($currentRoute, 'parent.') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="nav-label ml-3">Dashboard</span>
        </a>

        <!-- Manage Users (Admin only) -->
        @if($cu->isAdmin())
        <a href="{{ route('admin.users') }}" class="nav-item {{ $currentRoute === 'admin.users' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="nav-label ml-3">Manage Users</span>
        </a>
        @endif

        <!-- Manage Children -->
        @if($cu->isHealthcareWorker() || $cu->isDoctor())
        <a href="{{ route('children.index') }}" class="nav-item {{ str_starts_with($currentRoute, 'children') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="nav-label ml-3">Manage Children</span>
        </a>
        @endif

        @if($cu->isParent() || $cu->isGuardian())
        <a href="{{ route('children.index') }}" class="nav-item {{ str_starts_with($currentRoute, 'children') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="nav-label ml-3">Manage Children</span>
        </a>
        @endif

        <!-- Growth Measurements -->
        @if($cu->isHealthcareWorker() || $cu->isDoctor())
        <a href="{{ route('growth-measurements.index') }}" class="nav-item {{ str_starts_with($currentRoute, 'growth-measurements') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
            <span class="nav-label ml-3">Growth Measurements</span>
        </a>

        <!-- Vaccinations Accordion -->
        <div x-data="{ open: {{ request('vaccinate') == 1 ? 'true' : 'false' }} }">
            <button @click="open = !open" class="nav-item w-full text-left" style="display:flex;align-items:center;justify-content:space-between;cursor:pointer;">
                <div class="flex items-center gap-3">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="nav-label">Vaccinations</span>
                </div>
                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse.duration.200ms>
                <a href="{{ route('children.index') }}?vaccinate=1" class="nav-item pl-8 {{ request('vaccinate') == 1 ? 'active' : '' }}">
                    <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="nav-label ml-3">Select Child to Vaccinate</span>
                </a>
                <a href="{{ route('immunizations.index') }}" class="nav-item pl-8 {{ str_starts_with($currentRoute, 'immunizations') && request('vaccinate') != 1 ? 'active' : '' }}">
                    <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="nav-label ml-3">Vaccination Records</span>
                </a>
            </div>
        </div>

        <!-- Devices Section -->
        <div class="nav-item" style="cursor:default;opacity:0.5;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;padding:6px 16px;margin-top:4px;">
            <span class="nav-label">Devices</span>
        </div>
        <a href="{{ route('devices.index') }}" class="nav-item pl-8 {{ $currentRoute === 'devices.index' || $currentRoute === 'devices.show' ? 'active' : '' }}">
            <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
            </svg>
            <span class="nav-label ml-3">My Devices</span>
        </a>
        <a href="{{ route('devices.create') }}" class="nav-item pl-8 {{ $currentRoute === 'devices.create' || $currentRoute === 'devices.edit' ? 'active' : '' }}">
            <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="nav-label ml-3">Add Device</span>
        </a>
        <a href="{{ route('devices.index') }}?connect=1" class="nav-item pl-8" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE);color:#2563EB;font-weight:600;">
            <svg class="nav-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#2563EB;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <span class="nav-label ml-3">Connect Device</span>
        </a>
        @endif

        <!-- Reports -->
        <a href="{{ route('admin.system-report') }}" class="nav-item {{ $currentRoute === 'admin.system-report' || str_starts_with($currentRoute, 'reports') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            <span class="nav-label ml-3">Reports</span>
        </a>

        @if($cu->isAdmin())
        <!-- Backup & Restore -->
        <a href="{{ route('admin.backup') }}" class="nav-item {{ $currentRoute === 'admin.backup' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
            <span class="nav-label ml-3">Backup & Restore</span>
        </a>

        <!-- Audit Logs -->
        <a href="{{ route('admin.audit-logs') }}" class="nav-item {{ $currentRoute === 'admin.audit-logs' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="nav-label ml-3">Audit Logs</span>
        </a>

        <!-- System Settings -->
        <a href="{{ route('admin.settings') }}" class="nav-item {{ $currentRoute === 'admin.settings' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="nav-label ml-3">System Settings</span>
        </a>
        @endif
    </nav>

    <!-- Logout -->
    <div class="border-t border-gray-100 px-3 py-3 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-red-500 hover:text-red-700 hover:bg-red-50">
                <svg class="nav-icon text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="nav-label ml-3">Logout</span>
            </button>
        </form>
    </div>
</aside>
@endauth