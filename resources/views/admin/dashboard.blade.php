@extends('layouts.app')

@section('header', 'Admin Dashboard')

@section('title', config('app.name', 'Child Growth Monitor'))

@section('content')
<div class="space-y-6">
    <!-- ===== TOP STATS ROW (8 Cards with distinct colors) ===== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- 1. Total Users - Blue Gradient -->
        <div class="relative overflow-hidden rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #2563EB, #1D4ED8);">
            <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full backdrop-blur-sm">All Roles</span>
                </div>
                <p class="text-3xl font-extrabold tracking-tight">{{ \App\Models\User::count() }}</p>
                <p class="text-sm text-white/80 mt-1 font-medium">Total Registered Users</p>
            </div>
        </div>

        <!-- 2. Active Users - Emerald Gradient -->
        <div class="relative overflow-hidden rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #059669, #047857);">
            <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full backdrop-blur-sm">{{ number_format((\App\Models\User::where('is_active', true)->count() / max(\App\Models\User::count(), 1)) * 100) }}%</span>
                </div>
                <p class="text-3xl font-extrabold tracking-tight">{{ \App\Models\User::where('is_active', true)->count() }}</p>
                <p class="text-sm text-white/80 mt-1 font-medium">Active Users</p>
            </div>
        </div>

        <!-- 3. Healthcare Workers - Amber/Orange Gradient -->
        <div class="relative overflow-hidden rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #D97706, #B45309);">
            <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full backdrop-blur-sm">{{ \App\Models\User::whereIn('role', ['doctor', 'nurse'])->count() }} staff</span>
                </div>
                <p class="text-3xl font-extrabold tracking-tight">{{ \App\Models\User::whereIn('role', ['doctor', 'nurse'])->count() }}</p>
                <p class="text-sm text-white/80 mt-1 font-medium">Healthcare Workers</p>
            </div>
        </div>

        <!-- 4. Administrators - Rose/Red Gradient -->
        <div class="relative overflow-hidden rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #E11D48, #BE123C);">
            <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full backdrop-blur-sm">{{ \App\Models\User::where('role', 'admin')->count() }} total</span>
                </div>
                <p class="text-3xl font-extrabold tracking-tight">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
                <p class="text-sm text-white/80 mt-1 font-medium">Administrators</p>
            </div>
        </div>

        <!-- 5. Total Children - Violet/Purple Gradient -->
        <div class="relative overflow-hidden rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #7C3AED, #6D28D9);">
            <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full backdrop-blur-sm">{{ \App\Models\Child::active()->count() }} active</span>
                </div>
                <p class="text-3xl font-extrabold tracking-tight">{{ \App\Models\Child::count() }}</p>
                <p class="text-sm text-white/80 mt-1 font-medium">Total Children Enrolled</p>
            </div>
        </div>

        <!-- 6. Children with Growth Data - Teal/Cyan Gradient -->
        <div class="relative overflow-hidden rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #0D9488, #0F766E);">
            <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full backdrop-blur-sm">{{ \App\Models\GrowthMeasurement::distinct('child_id')->count('child_id') }} children</span>
                </div>
                <p class="text-3xl font-extrabold tracking-tight">{{ \App\Models\GrowthMeasurement::distinct('child_id')->count('child_id') }}</p>
                <p class="text-sm text-white/80 mt-1 font-medium">Children With Growth Data</p>
            </div>
        </div>

        <!-- 7. Total Immunizations Administered - Indigo Gradient -->
        <div class="relative overflow-hidden rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #4F46E5, #4338CA);">
            <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full backdrop-blur-sm">{{ \App\Models\Immunization::administered()->count() }} given</span>
                </div>
                <p class="text-3xl font-extrabold tracking-tight">{{ \App\Models\Immunization::administered()->count() }}</p>
                <p class="text-sm text-white/80 mt-1 font-medium">Immunizations Given</p>
            </div>
        </div>

        <!-- 8. Total Measurements - Pink/Fuchsia Gradient -->
        <div class="relative overflow-hidden rounded-2xl p-5 text-white" style="background: linear-gradient(135deg, #D946EF, #C026D3);">
            <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    </div>
                    <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full backdrop-blur-sm">{{ \App\Models\GrowthMeasurement::count() }} records</span>
                </div>
                <p class="text-3xl font-extrabold tracking-tight">{{ \App\Models\GrowthMeasurement::count() }}</p>
                <p class="text-sm text-white/80 mt-1 font-medium">Total Growth Measurements</p>
            </div>
        </div>

    </div>

    <!-- ===== CHARTS & QUICK ACTIONS SECTION ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ===== CHART 1: User Role Distribution (Doughnut) ===== -->
        <div class="lg:col-span-1 content-card p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                User Role Distribution
            </h3>
            <div class="relative" style="height: 240px;">
                <canvas id="roleChart"></canvas>
            </div>
        </div>

        <!-- ===== CHART 2: Monthly Growth Registrations (Bar) ===== -->
        <div class="lg:col-span-1 content-card p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Monthly Child Registrations
            </h3>
            <div class="relative" style="height: 240px;">
                <canvas id="registrationsChart"></canvas>
            </div>
        </div>

        <!-- ===== CHART 3: Nutritional Status Overview (Bar) ===== -->
        <div class="lg:col-span-1 content-card p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                Nutritional Status Overview
            </h3>
            <div class="relative" style="height: 240px;">
                <canvas id="nutritionChart"></canvas>
            </div>
        </div>

    </div>

    <!-- ===== SECOND ROW: System Overview + Quick Actions ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- System Overview -->
        <div class="lg:col-span-2 content-card p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                System Overview
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center p-4 rounded-xl" style="background: linear-gradient(135deg, #F0F9FF, #E0F2FE);">
                    <p class="text-lg font-bold text-sky-700">{{ \App\Models\User::where('role', 'nurse')->count() }}</p>
                    <p class="text-xs font-semibold text-sky-600">Nurses</p>
                </div>
                <div class="text-center p-4 rounded-xl" style="background: linear-gradient(135deg, #FEF3C7, #FDE68A);">
                    <p class="text-lg font-bold text-amber-700">{{ \App\Models\User::where('role', 'doctor')->count() }}</p>
                    <p class="text-xs font-semibold text-amber-600">Doctors</p>
                </div>
                <div class="text-center p-4 rounded-xl" style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5);">
                    <p class="text-lg font-bold text-emerald-700">{{ \App\Models\Child::active()->male()->count() }}</p>
                    <p class="text-xs font-semibold text-emerald-600">Male Children</p>
                </div>
                <div class="text-center p-4 rounded-xl" style="background: linear-gradient(135deg, #FDF2F8, #FCE7F3);">
                    <p class="text-lg font-bold text-pink-700">{{ \App\Models\Child::active()->female()->count() }}</p>
                    <p class="text-xs font-semibold text-pink-600">Female Children</p>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center p-4 rounded-xl" style="background: linear-gradient(135deg, #F5F3FF, #EDE9FE);">
                    <p class="text-lg font-bold text-violet-700">{{ \App\Models\Immunization::overdue()->count() }}</p>
                    <p class="text-xs font-semibold text-violet-600">Overdue Immunizations</p>
                </div>
                <div class="text-center p-4 rounded-xl" style="background: linear-gradient(135deg, #FFF7ED, #FFEDD5);">
                    <p class="text-lg font-bold text-orange-700">{{ \App\Models\Immunization::due()->count() }}</p>
                    <p class="text-xs font-semibold text-orange-600">Due This Week</p>
                </div>
                <div class="text-center p-4 rounded-xl" style="background: linear-gradient(135deg, #FEF2F2, #FEE2E2);">
                    <p class="text-lg font-bold text-red-700">{{ \App\Models\GrowthMeasurement::withAbnormalZScores()->count() }}</p>
                    <p class="text-xs font-semibold text-red-600">Abnormal Z-Scores</p>
                </div>
                <div class="text-center p-4 rounded-xl" style="background: linear-gradient(135deg, #F0FDF4, #DCFCE7);">
                    <p class="text-lg font-bold text-green-700">{{ \App\Models\Child::whereDate('created_at', '>=', now()->subDays(30))->count() }}</p>
                    <p class="text-xs font-semibold text-green-600">New Children (30d)</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="content-card p-5">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Quick Actions
            </h3>
            <div class="space-y-2">
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-3 rounded-xl transition-all hover:scale-[1.02] hover:shadow-md" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE);">
                    <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-blue-800">Manage Users</span>
                        <p class="text-xs text-blue-600">View, edit, or add system users</p>
                    </div>
                </a>
                <a href="{{ route('children.index') }}" class="flex items-center gap-3 p-3 rounded-xl transition-all hover:scale-[1.02] hover:shadow-md" style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5);">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-emerald-800">View Children</span>
                        <p class="text-xs text-emerald-600">Browse and manage children records</p>
                    </div>
                </a>
                <a href="{{ route('children.create') }}" class="flex items-center gap-3 p-3 rounded-xl transition-all hover:scale-[1.02] hover:shadow-md" style="background: linear-gradient(135deg, #FEF3C7, #FDE68A);">
                    <div class="w-10 h-10 rounded-lg bg-amber-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-amber-800">Register Child</span>
                        <p class="text-xs text-amber-600">Add a new child to the system</p>
                    </div>
                </a>
                <a href="{{ route('admin.system-report') }}" class="flex items-center gap-3 p-3 rounded-xl transition-all hover:scale-[1.02] hover:shadow-md" style="background: linear-gradient(135deg, #F5F3FF, #EDE9FE);">
                    <div class="w-10 h-10 rounded-lg bg-violet-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-violet-800">System Report</span>
                        <p class="text-xs text-violet-600">Generate and view system reports</p>
                    </div>
                </a>
                <a href="{{ route('reports.growth') }}" class="flex items-center gap-3 p-3 rounded-xl transition-all hover:scale-[1.02] hover:shadow-md" style="background: linear-gradient(135deg, #FCE7F3, #FBCFE8);">
                    <div class="w-10 h-10 rounded-lg bg-pink-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-pink-800">Growth Reports</span>
                        <p class="text-xs text-pink-600">View child growth analytics</p>
                    </div>
                </a>
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 p-3 rounded-xl transition-all hover:scale-[1.02] hover:shadow-md" style="background: linear-gradient(135deg, #F0F9FF, #E0F2FE);">
                    <div class="w-10 h-10 rounded-lg bg-sky-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-sky-800">System Settings</span>
                        <p class="text-xs text-sky-600">Configure application preferences</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
</div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Common chart defaults
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#64748B';

    // ===== 1. User Role Distribution (Doughnut) =====
    const roleCtx = document.getElementById('roleChart').getContext('2d');
    new Chart(roleCtx, {
        type: 'doughnut',
        data: {
            labels: ['Admins', 'Doctors', 'Nurses', 'Parents'],
            datasets: [{
                data: [
                    {{ \App\Models\User::where('role', 'admin')->count() }},
                    {{ \App\Models\User::where('role', 'doctor')->count() }},
                    {{ \App\Models\User::where('role', 'nurse')->count() }},
                    {{ \App\Models\User::where('role', 'parent')->count() }}
                ],
                backgroundColor: [
                    '#E11D48',  // rose/red
                    '#D97706',  // amber
                    '#059669',  // emerald
                    '#2563EB'   // blue
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { weight: '600', size: 11 }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 1200
            }
        }
    });

    // ===== 2. Monthly Child Registrations (Bar) =====
    const regCtx = document.getElementById('registrationsChart').getContext('2d');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const currentMonth = new Date().getMonth();
    const last6Months = [];
    for (let i = 5; i >= 0; i--) {
        let monthIndex = (currentMonth - i + 12) % 12;
        last6Months.push(months[monthIndex]);
    }

    // Get registration counts for last 6 months
    const regCounts = @json(
        (function() {
            $counts = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $counts[] = \App\Models\Child::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
            }
            return $counts;
        })()
    );

    new Chart(regCtx, {
        type: 'bar',
        data: {
            labels: last6Months,
            datasets: [{
                label: 'Registrations',
                data: regCounts,
                backgroundColor: [
                    'rgba(5, 150, 105, 0.7)',
                    'rgba(5, 150, 105, 0.7)',
                    'rgba(5, 150, 105, 0.7)',
                    'rgba(5, 150, 105, 0.7)',
                    'rgba(5, 150, 105, 0.7)',
                    'rgba(5, 150, 105, 0.7)'
                ],
                borderColor: '#059669',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: '600' } }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });

    // ===== 3. Nutritional Status Overview (Horizontal Bar) =====
    const nutCtx = document.getElementById('nutritionChart').getContext('2d');
    new Chart(nutCtx, {
        type: 'bar',
        data: {
            labels: ['Normal', 'Underweight', 'Overweight', 'Obese', 'Stunted', 'Wasted'],
            datasets: [{
                label: 'Children',
                data: [
                    {{ \App\Models\GrowthMeasurement::whereNull('nutritional_status')->orWhere('nutritional_status', 'normal')->count() }},
                    {{ \App\Models\GrowthMeasurement::where('nutritional_status', 'underweight')->count() }},
                    {{ \App\Models\GrowthMeasurement::where('nutritional_status', 'overweight')->count() }},
                    {{ \App\Models\GrowthMeasurement::where('nutritional_status', 'obese')->count() }},
                    {{ \App\Models\GrowthMeasurement::where('stunting_status', 'stunted')->count() }},
                    {{ \App\Models\GrowthMeasurement::where('wasting_status', 'wasted')->count() }}
                ],
                backgroundColor: [
                    'rgba(5, 150, 105, 0.7)',   // normal - green
                    'rgba(234, 179, 8, 0.7)',   // underweight - yellow
                    'rgba(249, 115, 22, 0.7)',  // overweight - orange
                    'rgba(239, 68, 68, 0.7)',   // obese - red
                    'rgba(168, 85, 247, 0.7)',  // stunted - purple
                    'rgba(59, 130, 246, 0.7)'   // wasted - blue
                ],
                borderColor: [
                    '#059669', '#CA8A04', '#EA580C', '#DC2626', '#9333EA', '#2563EB'
                ],
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 10 }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: '600' } }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });
});
</script>
@endpush
@endsection