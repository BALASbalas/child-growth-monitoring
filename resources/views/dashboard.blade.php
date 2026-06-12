@extends('layouts.app')

@section('header')
Dashboard
@endsection

@section('content')
<div class="space-y-6 fade-in">
    @php
        $cu = Auth::user();
        // Determine role-based stats
        if ($cu->isAdmin() || $cu->isNurse() || $cu->isDoctor() || $cu->isHealthcareWorker()) {
            $totalChildren = \App\Models\Child::active()->count();
            $totalMeasurements = \App\Models\GrowthMeasurement::count();
            $dueVaccines = \App\Models\Immunization::scheduled()->upcoming()->count();
            $overdueVaccines = \App\Models\Immunization::overdue()->count();
            $totalVaccinations = \App\Models\Immunization::count();
            $recentMeas = \App\Models\GrowthMeasurement::with('child')->latest()->limit(5)->get();
            $upcomingVax = \App\Models\Immunization::with('child')->scheduled()->upcoming()->limit(5)->get();
        } else {
            $totalChildren = \App\Models\Child::where('user_id', $cu->id)->active()->count();
            $totalMeasurements = \App\Models\GrowthMeasurement::whereHas('child', fn($q) => $q->where('user_id', $cu->id))->count();
            $dueVaccines = \App\Models\Immunization::whereHas('child', fn($q) => $q->where('user_id', $cu->id))->scheduled()->upcoming()->count();
            $overdueVaccines = \App\Models\Immunization::whereHas('child', fn($q) => $q->where('user_id', $cu->id))->overdue()->count();
            $totalVaccinations = \App\Models\Immunization::whereHas('child', fn($q) => $q->where('user_id', $cu->id))->count();
            $recentMeas = \App\Models\GrowthMeasurement::with('child')->whereHas('child', fn($q) => $q->where('user_id', $cu->id))->latest()->limit(5)->get();
            $upcomingVax = \App\Models\Immunization::with('child')->whereHas('child', fn($q) => $q->where('user_id', $cu->id))->scheduled()->upcoming()->limit(5)->get();
        }
        $normalCount = \App\Models\GrowthMeasurement::whereHas('child', fn($q) => $q->active())->whereNotNull('nutritional_status')->where('nutritional_status', 'normal')->count();
        $atRiskCount = \App\Models\GrowthMeasurement::whereHas('child', fn($q) => $q->active())->whereNotNull('nutritional_status')->whereIn('nutritional_status', ['moderate_underweight', 'overweight'])->count();
        $criticalCount = \App\Models\GrowthMeasurement::whereHas('child', fn($q) => $q->active())->whereNotNull('nutritional_status')->whereIn('nutritional_status', ['severe_underweight', 'obese'])->count();
    @endphp

    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl" style="background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 50%, #1E40AF 100%);">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="40" fill="white"/><circle cx="150" cy="80" r="25" fill="white"/>
                <circle cx="280" cy="40" r="35" fill="white"/><circle cx="350" cy="120" r="20" fill="white"/>
                <circle cx="80" cy="150" r="30" fill="white"/><circle cx="220" cy="160" r="28" fill="white"/>
                <circle cx="380" cy="180" r="22" fill="white"/>
            </svg>
        </div>
        <div class="relative p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/20 text-white tracking-wider uppercase">Healthcare Dashboard</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-white">Welcome back, {{ $cu->name }}! 👋</h2>
                    <p class="text-sm text-blue-200 mt-1">Child Growth Monitoring & Immunization Tracking System</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 bg-white/15 rounded-xl px-4 py-2">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs text-white/80">{{ now()->format('l, F d, Y') }}</span>
                    </div>
                    @if($cu->facility_name)
                    <div class="flex items-center gap-2 bg-white/15 rounded-xl px-4 py-2">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="text-xs text-white/80">{{ $cu->facility_name }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE);">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Total</span>
            </div>
            <p class="text-2xl font-bold text-navy-900" style="color: #0F172A;">{{ $totalChildren }}</p>
            <p class="text-sm mt-0.5" style="color: #64748B;">Registered Children</p>
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-1 text-xs text-emerald-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>Active records</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5);">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Recorded</span>
            </div>
            <p class="text-2xl font-bold text-navy-900" style="color: #0F172A;">{{ $totalMeasurements }}</p>
            <p class="text-sm mt-0.5" style="color: #64748B;">Growth Measurements</p>
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-1 text-xs text-blue-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                <span>WHO-standard tracking</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #FFFBEB, #FEF3C7);">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Upcoming</span>
            </div>
            <p class="text-2xl font-bold text-navy-900" style="color: #0F172A;">{{ $dueVaccines }}</p>
            <p class="text-sm mt-0.5" style="color: #64748B;">Due Vaccinations</p>
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-1 text-xs text-amber-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Needs attention</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #FEF2F2, #FEE2E2);">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Alert</span>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $overdueVaccines }}</p>
            <p class="text-sm mt-0.5" style="color: #64748B;">Overdue Vaccines</p>
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-1 text-xs text-red-600">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Requires immediate action</span>
            </div>
        </div>
    </div>

    <!-- Nutrition Status Summary + Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Nutrition Status -->
        <div class="content-card lg:col-span-2">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-navy-900" style="color: #0F172A;">Nutrition Status Overview</h3>
                <span class="text-xs text-gray-400">Last 30 days</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-xl font-bold text-emerald-700">{{ number_format($totalChildren > 0 ? ($normalCount / max($normalCount + $atRiskCount + $criticalCount, 1)) * 100 : 0) }}%</p>
                        <p class="text-xs text-emerald-600 font-medium mt-0.5">Normal</p>
                        <p class="text-[10px] text-emerald-500">{{ $normalCount }} children</p>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-amber-50 border border-amber-100">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-xl font-bold text-amber-700">{{ number_format($totalChildren > 0 ? ($atRiskCount / max($normalCount + $atRiskCount + $criticalCount, 1)) * 100 : 0) }}%</p>
                        <p class="text-xs text-amber-600 font-medium mt-0.5">At Risk</p>
                        <p class="text-[10px] text-amber-500">{{ $atRiskCount }} children</p>
                    </div>
                    <div class="text-center p-4 rounded-xl bg-red-50 border border-red-100">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-xl font-bold text-red-700">{{ number_format($totalChildren > 0 ? ($criticalCount / max($normalCount + $atRiskCount + $criticalCount, 1)) * 100 : 0) }}%</p>
                        <p class="text-xs text-red-600 font-medium mt-0.5">Critical</p>
                        <p class="text-[10px] text-red-500">{{ $criticalCount }} children</p>
                    </div>
                </div>
                <!-- Progress bar -->
                <div class="mt-4 h-2.5 rounded-full bg-gray-100 overflow-hidden flex">
                    <div class="bg-emerald-500 h-full transition-all" style="width: {{ $totalChildren > 0 ? ($normalCount / max($normalCount + $atRiskCount + $criticalCount, 1)) * 100 : 0 }}%"></div>
                    <div class="bg-amber-500 h-full transition-all" style="width: {{ $totalChildren > 0 ? ($atRiskCount / max($normalCount + $atRiskCount + $criticalCount, 1)) * 100 : 0 }}%"></div>
                    <div class="bg-red-500 h-full transition-all" style="width: {{ $totalChildren > 0 ? ($criticalCount / max($normalCount + $atRiskCount + $criticalCount, 1)) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="content-card">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-navy-900" style="color: #0F172A;">Quick Actions</h3>
            </div>
            <div class="p-4 space-y-2">
                @if($cu->isHealthcareWorker() || $cu->isDoctor())
                <a href="{{ route('children.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE);">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Manage Children</p>
                        <p class="text-xs text-gray-500">View & manage all children records</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('children.create') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5);">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Register Child</p>
                        <p class="text-xs text-gray-500">Add a new child to the system</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('growth-measurements.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #F3E8FF, #E9D5FF);">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Record Growth</p>
                        <p class="text-xs text-gray-500">Log new growth measurements</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('immunizations.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #FFFBEB, #FEF3C7);">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Vaccinations</p>
                        <p class="text-xs text-gray-500">Manage immunization records</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('reports.growth') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #FCE7F3, #FBCFE8);">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">View Reports</p>
                        <p class="text-xs text-gray-500">Analytics & growth charts</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @else
                <a href="{{ route('children.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE);">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">My Children</p>
                        <p class="text-xs text-gray-500">View your children's health records</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Measurements + Upcoming Vaccinations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Growth Measurements -->
        <div class="content-card">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-navy-900" style="color: #0F172A;">Recent Growth Measurements</h3>
                @if($cu->isHealthcareWorker())
                <a href="{{ route('growth-measurements.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">View all →</a>
                @endif
            </div>
            <div class="p-4">
                @if($recentMeas->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentMeas as $m)
                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5);">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $m->child->full_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">Weight: {{ $m->weight_kg ? number_format($m->weight_kg, 2) . ' kg' : 'N/A' }} · Height: {{ $m->height_cm ? number_format($m->height_cm, 1) . ' cm' : 'N/A' }}</p>
                            </div>
                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ $m->measurement_date ? \Carbon\Carbon::parse($m->measurement_date)->diffForHumans() : 'N/A' }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        <p class="mt-3 text-sm text-gray-500">No measurements recorded yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Vaccinations -->
        <div class="content-card">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-navy-900" style="color: #0F172A;">Upcoming Vaccinations</h3>
                @if($cu->isHealthcareWorker())
                <a href="{{ route('immunizations.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">View all →</a>
                @endif
            </div>
            <div class="p-4">
                @if($upcomingVax->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingVax as $v)
                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #FFFBEB, #FEF3C7);">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $v->child->full_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $v->vaccine_name ?? 'Vaccine' }} · {{ $v->scheduled_date ? \Carbon\Carbon::parse($v->scheduled_date)->format('M d, Y') : 'TBD' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-semibold
                                @if($v->status === 'scheduled') bg-blue-50 text-blue-700
                                @elseif($v->status === 'overdue') bg-red-50 text-red-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ $v->status ?? 'scheduled' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <p class="mt-3 text-sm text-gray-500">No upcoming vaccinations.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- System Summary -->
    <div class="content-card">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-navy-900" style="color: #0F172A;">System Overview</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center p-4 rounded-xl bg-gray-50">
                    <p class="text-2xl font-bold text-navy-900" style="color: #0F172A;">{{ $totalChildren }}</p>
                    <p class="text-xs mt-0.5" style="color: #64748B;">Total Children</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-gray-50">
                    <p class="text-2xl font-bold text-navy-900" style="color: #0F172A;">{{ $totalMeasurements }}</p>
                    <p class="text-xs mt-0.5" style="color: #64748B;">Measurements</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-gray-50">
                    <p class="text-2xl font-bold text-navy-900" style="color: #0F172A;">{{ $totalVaccinations }}</p>
                    <p class="text-xs mt-0.5" style="color: #64748B;">Vaccinations</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-gray-50">
                    <p class="text-2xl font-bold text-navy-900" style="color: #0F172A;">{{ $dueVaccines + $overdueVaccines }}</p>
                    <p class="text-xs mt-0.5" style="color: #64748B;">Pending Actions</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection