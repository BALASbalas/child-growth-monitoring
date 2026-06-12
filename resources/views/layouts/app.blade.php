<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Child Growth Monitor')) - @yield('header', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }

        /* ===== CORE LAYOUT ===== */
        body { background: #F8FAFC; }

        /* ===== HEADER - Clean White ===== */
        .app-header {
            position: fixed; top: 0; left: 0; right: 0; height: 70px; z-index: 50;
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        /* ===== SIDEBAR - Clean White, Flat (no dropdowns) ===== */
        .app-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 40;
            width: 260px; background: #FFFFFF;
            transition: width 0.25s ease, transform 0.25s ease;
            border-right: 1px solid #E2E8F0;
            box-shadow: 1px 0 4px rgba(0,0,0,0.03);
            padding-top: 0;
        }

        /* Content area */
        .app-content {
            margin-left: 260px;
            min-height: 100vh;
            padding-bottom: 0;
        }

        @media (max-width: 1024px) {
            .app-sidebar { transform: translateX(-100%); width: 260px; box-shadow: none; }
            .sidebar-open .app-sidebar { transform: translateX(0); box-shadow: 20px 0 60px rgba(0,0,0,0.08); }
            .app-content { margin-left: 0; }
        }

        /* ===== SIDEBAR NAVIGATION ===== */
        .nav-item {
            display: flex; align-items: center; padding: 10px 16px; margin: 2px 4px;
            border-radius: 10px; font-size: 0.85rem; font-weight: 500;
            color: #475569; transition: all 0.2s; cursor: pointer;
            text-decoration: none; position: relative;
        }
        .nav-item:hover { background: #F1F5F9; color: #0F172A; }
        .nav-item.active {
            background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
            color: #2563EB; font-weight: 600;
        }
        .nav-item.active .nav-icon { color: #2563EB; }
        .nav-icon { width: 20px; height: 20px; flex-shrink: 0; color: #94A3B8; transition: color 0.2s; }
        .nav-item:hover .nav-icon { color: #64748B; }
        .nav-item.active .nav-icon { color: #2563EB; }

        /* ===== STAT CARDS ===== */
        .stat-card {
            background: white; border-radius: 20px; border: 1px solid #E2E8F0;
            padding: 24px; transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px -12px rgba(0,0,0,0.1), 0 4px 12px rgba(0,0,0,0.04);
        }

        /* ===== CONTENT CARDS ===== */
        .content-card {
            background: white; border-radius: 20px; border: 1px solid #E2E8F0;
            overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.2s;
        }
        .content-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.06); }

        /* ===== TABLES ===== */
        .table-main { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-main thead th {
            padding: 12px 16px; text-align: left; font-size: 0.65rem;
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
            color: #64748B; background: #F8FAFC;
            border-bottom: 1px solid #E2E8F0; white-space: nowrap;
            position: sticky; top: 0; z-index: 1;
        }
        .table-main tbody td {
            padding: 12px 16px; font-size: 0.82rem; color: #334155;
            border-bottom: 1px solid #F1F5F9;
        }
        .table-main tbody tr { transition: background 0.15s; }
        .table-main tbody tr:hover { background: #F8FAFC; }
        .table-main tbody tr:last-child td { border-bottom: none; }

        /* ===== FORMS ===== */
        .form-input {
            width: 100%; padding: 12px 16px; border-radius: 12px;
            border: 1.5px solid #E2E8F0; background: #FFFFFF;
            color: #0F172A; font-size: 0.85rem; font-family: 'Inter', sans-serif;
            transition: all 0.2s ease; outline: none;
        }
        .form-input::placeholder { color: #94A3B8; }
        .form-input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }
        select.form-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%2394A3B8' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 40px; }
        .form-label {
            display: block; font-size: 0.78rem; font-weight: 600;
            color: #0F172A; margin-bottom: 6px;
        }
        .form-group { margin-bottom: 18px; }
        .form-error { font-size: 0.72rem; color: #EF4444; margin-top: 4px; }

        /* ===== BUTTONS ===== */
        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 8px; padding: 10px 22px; border-radius: 12px; border: none;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #fff; font-weight: 600; font-size: 0.85rem;
            font-family: 'Inter', sans-serif; cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
            text-decoration: none;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.35);
        }
        .btn-primary:disabled {
            opacity: 0.6; cursor: not-allowed; transform: none;
        }
        .btn-secondary {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 8px; padding: 10px 22px; border-radius: 12px;
            border: 1.5px solid #E2E8F0; background: #fff;
            color: #475569; font-weight: 600; font-size: 0.85rem;
            font-family: 'Inter', sans-serif; cursor: pointer;
            transition: all 0.2s ease; text-decoration: none;
        }
        .btn-secondary:hover { border-color: #2563EB; color: #2563EB; background: #F8FAFC; }
        .btn-danger {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 8px; padding: 10px 22px; border-radius: 12px; border: none;
            background: #EF4444; color: #fff; font-weight: 600; font-size: 0.85rem;
            font-family: 'Inter', sans-serif; cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-danger:hover { background: #DC2626; }
        .btn-sm { padding: 8px 18px; font-size: 0.8rem; border-radius: 10px; }
        .btn-xs { padding: 5px 12px; font-size: 0.72rem; border-radius: 8px; }

        /* ===== BADGES ===== */
        .badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }

        /* ===== PROFILE DROPDOWN ===== */
        .profile-dropdown {
            position: absolute; right: 0; top: 100%; margin-top: 8px;
            min-width: 240px; background: white;
            border: 1px solid #E2E8F0; border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
            z-index: 60; display: none; overflow: hidden;
        }
        .profile-dropdown.show { display: block; }
        .profile-dropdown-item {
            display: flex; align-items: center; padding: 10px 16px;
            font-size: 0.82rem; color: #334155;
            transition: background 0.15s; text-decoration: none;
        }
        .profile-dropdown-item:hover { background: #F1F5F9; }

        /* ===== MODAL OVERLAY ===== */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 100; background: rgba(15, 23, 42, 0.5);
            display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
            padding: 20px;
        }
        .modal-overlay.active { display: flex; }
        .modal-container {
            background: white; border-radius: 24px; width: 100%;
            max-width: 560px; max-height: 85vh; overflow-y: auto;
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
            animation: modalSlideIn 0.25s ease-out;
        }
        .modal-container.modal-lg { max-width: 720px; }
        .modal-container.modal-xl { max-width: 900px; }
        .modal-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid #E2E8F0;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; background: white; z-index: 1;
            border-radius: 24px 24px 0 0;
        }
        .modal-header h3 { font-size: 1.1rem; font-weight: 700; color: #0F172A; margin: 0; }
        .modal-close {
            width: 36px; height: 36px; border-radius: 10px; border: none;
            background: #F1F5F9; color: #64748B; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
        }
        .modal-close:hover { background: #E2E8F0; color: #0F172A; }
        .modal-body { padding: 20px 24px; }
        .modal-footer {
            padding: 16px 24px 20px;
            border-top: 1px solid #E2E8F0;
            display: flex; align-items: center; justify-content: flex-end;
            gap: 10px;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ===== CONFIRMATION DIALOG ===== */
        .confirm-overlay {
            position: fixed; inset: 0; z-index: 200; background: rgba(15, 23, 42, 0.5);
            display: none; align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
            padding: 20px;
        }
        .confirm-overlay.active { display: flex; }
        .confirm-container {
            background: white; border-radius: 24px; width: 100%;
            max-width: 420px; padding: 32px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
            text-align: center;
            animation: modalSlideIn 0.25s ease-out;
        }
        .confirm-icon {
            width: 64px; height: 64px; border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }

        /* ===== TOAST NOTIFICATIONS ===== */
        .toast-container {
            position: fixed; top: 20px; right: 20px; z-index: 300;
            display: flex; flex-direction: column; gap: 8px;
        }
        .toast {
            padding: 14px 20px; border-radius: 14px; min-width: 300px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            display: flex; align-items: center; gap: 12px;
            animation: toastSlideIn 0.3s ease-out;
            font-size: 0.85rem; font-weight: 500;
        }
        .toast-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .toast-error { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        .toast-info { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }

        @keyframes toastSlideIn {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ===== GENERAL ===== */
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.3s ease-out; }

        @keyframes slideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .slide-up { animation: slideUp 0.4s ease-out; }

        /* ===== GRID RESPONSIVE ===== */
        .stat-grid { display: grid; gap: 20px; }
        .stat-grid-2 { grid-template-columns: repeat(2, 1fr); }
        .stat-grid-3 { grid-template-columns: repeat(3, 1fr); }
        .stat-grid-4 { grid-template-columns: repeat(4, 1fr); }
        @media (max-width: 1024px) { .stat-grid-4 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .stat-grid-2, .stat-grid-3, .stat-grid-4 { grid-template-columns: 1fr; } }

        @media print { .app-sidebar, .app-header, footer, .no-print { display: none !important; } .app-content { margin-left: 0 !important; } }

        /* Loading Spinner */
        .spinner { width: 20px; height: 20px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Filter chips */
        .filter-chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;
            border: 1.5px solid #E2E8F0; background: white; color: #475569;
            cursor: pointer; transition: all 0.15s;
        }
        .filter-chip:hover { border-color: #2563EB; color: #2563EB; }
        .filter-chip.active { background: #EFF6FF; border-color: #2563EB; color: #2563EB; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased" x-data="{
    sidebarOpen: window.innerWidth >= 1024,
    mobileMenuOpen: false
}" x-init="window.addEventListener('resize', () => { if(window.innerWidth >= 1024) { sidebarOpen = true; mobileMenuOpen = false; } else { sidebarOpen = false; } })">

    <div :class="{'sidebar-open': mobileMenuOpen}">
        <!-- Header -->
        @auth
            @include('layouts.header')
        @endauth

        <!-- Sidebar -->
        @auth
            @include('layouts.sidebar')
        @endauth

        <!-- Mobile Overlay -->
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/40 z-30 lg:hidden" x-cloak></div>

        <!-- Main Content -->
        <div class="app-content min-h-screen flex flex-col">
            <!-- Top Bar (below header) -->
            @auth
            <div style="height: 70px;"></div>
            @endauth

            <main class="flex-1 p-4 sm:p-6 lg:p-8" style="background: #F8FAFC;">
                <!-- Page Header -->
                @hasSection('header')
                    <div class="mb-6 fade-in">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <h1 class="text-xl font-bold" style="color: #0F172A;">
                                    @yield('header')
                                </h1>
                                @auth
                                    @php $cu = Auth::user(); @endphp
                                    <p class="text-sm mt-0.5" style="color: #64748B;">
                                        {{ now()->format('l, F d, Y') }}
                                    </p>
                                @endauth
                            </div>
                            @auth
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                                    @if($cu->isAdmin()) bg-red-50 text-red-700 border border-red-200
                                    @elseif($cu->isNurse() || $cu->isHealthcareWorker()) bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($cu->isDoctor()) bg-indigo-50 text-indigo-700 border border-indigo-200
                                    @elseif($cu->isParent() || $cu->isGuardian()) bg-blue-50 text-blue-700 border border-blue-200
                                    @else bg-purple-50 text-purple-700 border border-purple-200
                                    @endif">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $cu->role_label }}
                                </span>
                            </div>
                            @endauth
                        </div>
                    </div>
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl shadow-sm fade-in" x-cloak>
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium flex-1">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">&times;</button>
                </div>
                @endif
                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 rounded-2xl shadow-sm fade-in" x-cloak>
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium flex-1">{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-400 hover:text-red-600">&times;</button>
                </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <script>
        // Global modal helpers
        function openModal(id) { document.getElementById(id)?.classList.add('active'); }
        function closeModal(id) { document.getElementById(id)?.classList.remove('active'); }
        function openConfirm(id) { document.getElementById(id)?.classList.add('active'); }
        function closeConfirm(id) { document.getElementById(id)?.classList.remove('active'); }

        // Toast notification system
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            let icon = '';
            if (type === 'success') icon = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            else if (type === 'error') icon = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            else icon = '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            
            toast.innerHTML = `${icon}<span class="flex-1">${message}</span><button onclick="this.parentElement.remove()" class="flex-shrink-0 opacity-60 hover:opacity-100">&times;</button>`;
            container.appendChild(toast);
            setTimeout(() => { if (toast.parentElement) toast.remove(); }, 4000);
        }

        // Close modals on overlay click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
            }
            if (e.target.classList.contains('confirm-overlay')) {
                e.target.classList.remove('active');
            }
        });

        // Close modals on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active, .confirm-overlay.active').forEach(el => {
                    el.classList.remove('active');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>