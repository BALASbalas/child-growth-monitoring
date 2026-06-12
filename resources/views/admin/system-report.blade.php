@extends('layouts.app')

@section('header')
<div class="flex justify-between items-center flex-wrap gap-3">
    System-Wide Report
    <div class="flex gap-2">
        <button onclick="window.print()" class="btn-primary btn-sm no-print">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Report
        </button>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-blue-600 transition-colors">← Back to Dashboard</a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6 fade-in">
    <!-- Date -->
    <div class="text-sm" style="color: #64748B;">Report Date: {{ now()->format('d/m/Y H:i') }}</div>

    <!-- System Overview -->
    <div class="content-card p-6">
        <h3 class="font-semibold text-navy-900 mb-4 text-lg" style="color: #0F172A;">📊 System Overview</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-navy-900" style="color: #0F172A;">{{ $stats['total_users'] }}</p>
                <p class="text-sm mt-1" style="color: #64748B;">Total Users</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-navy-900" style="color: #0F172A;">{{ $stats['total_children'] }}</p>
                <p class="text-sm mt-1" style="color: #64748B;">Total Children</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-navy-900" style="color: #0F172A;">{{ $stats['total_measurements'] }}</p>
                <p class="text-sm mt-1" style="color: #64748B;">Total Measurements</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-navy-900" style="color: #0F172A;">{{ $stats['total_immunizations'] }}</p>
                <p class="text-sm mt-1" style="color: #64748B;">Total Immunizations</p>
            </div>
        </div>
    </div>

    <!-- Users by Role + Children Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="content-card p-6">
            <h3 class="font-semibold text-navy-900 mb-4" style="color: #0F172A;">👥 Users by Role</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Administrators</span>
                    <span class="font-bold text-lg text-red-600">{{ $stats['total_admins'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Nurses</span>
                    <span class="font-bold text-lg text-emerald-600">{{ $stats['total_nurses'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Doctors</span>
                    <span class="font-bold text-lg text-indigo-600">{{ $stats['total_doctors'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Parents</span>
                    <span class="font-bold text-lg text-blue-600">{{ $stats['total_parents'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Guardians</span>
                    <span class="font-bold text-lg text-purple-600">{{ $stats['total_guardians'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="content-card p-6">
            <h3 class="font-semibold text-navy-900 mb-4" style="color: #0F172A;">👶 Children Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Male</span>
                    <span class="font-bold text-lg text-blue-600">{{ $stats['children_by_gender']['male'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Female</span>
                    <span class="font-bold text-lg text-pink-600">{{ $stats['children_by_gender']['female'] ?? 0 }}</span>
                </div>
                <hr class="border-gray-200">
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Registered This Month</span>
                    <span class="font-bold text-lg text-indigo-600">{{ $stats['recent_registrations'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50">
                    <span class="text-sm font-medium text-gray-700">Measurements This Month</span>
                    <span class="font-bold text-lg text-purple-600">{{ $stats['recent_measurements'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Vaccination Status -->
    <div class="content-card p-6">
        <h3 class="font-semibold text-navy-900 mb-4" style="color: #0F172A;">💉 Vaccination Status</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="text-center p-5 rounded-xl" style="background: linear-gradient(135deg, #FEF2F2, #FEE2E2);">
                <p class="text-sm font-medium text-red-600">Overdue</p>
                <p class="text-3xl font-bold text-red-900 mt-1">{{ $stats['overdue_vaccines'] ?? 0 }}</p>
            </div>
            <div class="text-center p-5 rounded-xl" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE);">
                <p class="text-sm font-medium text-blue-600">Upcoming</p>
                <p class="text-3xl font-bold text-blue-900 mt-1">{{ $stats['upcoming_vaccines'] ?? 0 }}</p>
            </div>
            <div class="text-center p-5 rounded-xl" style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5);">
                <p class="text-sm font-medium text-emerald-600">Total Immunizations</p>
                <p class="text-3xl font-bold text-emerald-900 mt-1">{{ $stats['total_immunizations'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-xs" style="color: #94A3B8;">
        <p>Report Generated by Child Growth Monitoring System</p>
        <p>{{ now()->format('d/m/Y H:i:s') }} | Generated by: {{ Auth::user()->name }} (Admin)</p>
    </div>
</div>
@endsection