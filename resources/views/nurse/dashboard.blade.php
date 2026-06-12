@extends('layouts.app')

@section('header')
Nurse Dashboard
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</h3>
                <p class="text-green-100 mt-1">Nurse - Digital Child Growth Monitoring System</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm">
                    {{ Auth::user()->role_label }}
                </span>
                @if(Auth::user()->facility_name)
                    <span class="text-sm text-green-200">| {{ Auth::user()->facility_name }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    @php
        $totalUsers = \App\Models\User::count();
        $totalChildren = \App\Models\Child::active()->count();
        $totalMeasurements = \App\Models\GrowthMeasurement::count();
        $totalVaccinations = \App\Models\Immunization::count();
        $dueVaccines = \App\Models\Immunization::scheduled()->upcoming()->count();
        $overdueVaccines = \App\Models\Immunization::overdue()->count();
        $adminCount = \App\Models\User::where('role', 'admin')->count();
        $nurseCount = \App\Models\User::where('role', 'nurse')->count();
        $doctorCount = \App\Models\User::where('role', 'doctor')->count();
        $parentCount = \App\Models\User::where('role', 'parent')->count();
        $guardianCount = \App\Models\User::where('role', 'guardian')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
            <div><p class="text-sm text-gray-500">Total Children</p><p class="text-2xl font-bold text-gray-900">{{ $totalChildren }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg></div>
            <div><p class="text-sm text-gray-500">Measurements</p><p class="text-2xl font-bold text-gray-900">{{ $totalMeasurements }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
            <div><p class="text-sm text-gray-500">Total Users</p><p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg></div>
            <div><p class="text-sm text-gray-500">Vaccinations</p><p class="text-2xl font-bold text-gray-900">{{ $totalVaccinations }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg></div>
            <div><p class="text-sm text-gray-500">Due Vaccines</p><p class="text-2xl font-bold text-gray-900">{{ $dueVaccines }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77 1.333-2.694 1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
            <div><p class="text-sm text-gray-500">Overdue Vaccinations</p><p class="text-2xl font-bold text-red-600">{{ $overdueVaccines }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div><p class="text-sm text-gray-500">Registered Doctors</p><p class="text-2xl font-bold text-gray-900">{{ $doctorCount }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857"/></svg></div>
            <div><p class="text-sm text-gray-500">Registered Parents</p><p class="text-2xl font-bold text-gray-900">{{ $parentCount }}</p></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('children.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex items-center space-x-4">
            <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center"><svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
            <div><h4 class="font-semibold text-gray-900">Children</h4><p class="text-sm text-gray-500">View & manage children</p></div>
        </a>
        <a href="{{ route('children.create') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex items-center space-x-4">
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center"><svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
            <div><h4 class="font-semibold text-gray-900">Register Child</h4><p class="text-sm text-gray-500">Add a new child record</p></div>
        </a>
        <a href="{{ route('growth-measurements.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex items-center space-x-4">
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center"><svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg></div>
            <div><h4 class="font-semibold text-gray-900">Growth Tracking</h4><p class="text-sm text-gray-500">Record measurements</p></div>
        </a>
        <a href="{{ route('immunizations.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex items-center space-x-4">
            <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center"><svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg></div>
            <div><h4 class="font-semibold text-gray-900">Vaccinations</h4><p class="text-sm text-gray-500">Manage immunizations</p></div>
        </a>
        <a href="{{ route('devices.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex items-center space-x-4">
            <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center"><svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg></div>
            <div><h4 class="font-semibold text-gray-900">Devices</h4><p class="text-sm text-gray-500">Digital measuring devices</p></div>
        </a>
        <a href="{{ route('reports.growth') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex items-center space-x-4">
            <div class="w-14 h-14 bg-cyan-100 rounded-xl flex items-center justify-center"><svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg></div>
            <div><h4 class="font-semibold text-gray-900">Reports</h4><p class="text-sm text-gray-500">View growth reports</p></div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">User Distribution by Role</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <div class="flex justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-600">Administrators</span>
                        <span class="text-sm font-bold text-red-600">{{ $adminCount }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-red-400 h-2 rounded-full" style="width: {{ $totalUsers > 0 ? ($adminCount / $totalUsers) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-600">Nurses</span>
                        <span class="text-sm font-bold text-emerald-600">{{ $nurseCount }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-emerald-400 h-2 rounded-full" style="width: {{ $totalUsers > 0 ? ($nurseCount / $totalUsers) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-600">Doctors</span>
                        <span class="text-sm font-bold text-indigo-600">{{ $doctorCount }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-indigo-400 h-2 rounded-full" style="width: {{ $totalUsers > 0 ? ($doctorCount / $totalUsers) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-600">Parents</span>
                        <span class="text-sm font-bold text-blue-600">{{ $parentCount }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-400 h-2 rounded-full" style="width: {{ $totalUsers > 0 ? ($parentCount / $totalUsers) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-600">Guardians</span>
                        <span class="text-sm font-bold text-purple-600">{{ $guardianCount }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-purple-400 h-2 rounded-full" style="width: {{ $totalUsers > 0 ? ($guardianCount / $totalUsers) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div onclick="window.location='{{ route('children.index') }}'" class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 cursor-pointer group hover:bg-slate-50 transition">
                <div>
                    <h3 class="font-semibold text-gray-900">All Registered Children</h3>
                    <p class="text-sm text-gray-500">Open the full children register for system-wide records.</p>
                </div>
                <a href="{{ route('children.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-full text-xs font-medium hover:bg-blue-700 transition">
                    View all children
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Child</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Age</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sex</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Registered By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php $allChildren = \App\Models\Child::with('user')->active()->latest()->limit(8)->get(); @endphp
                        @foreach($allChildren as $child)
                        <tr onclick="window.location='{{ route('children.show', $child) }}'" class="hover:bg-gray-50 cursor-pointer" role="button">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-900">{{ $child->full_name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $child->unique_id }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $child->age_string }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $child->sex === 'male' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $child->sex === 'male' ? 'Male' : 'Female' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $child->user ? $child->user->name : 'Unknown' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Measurements -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Recent Measurements</h3>
        </div>
        <div class="p-6">
            @php
                $recent = \App\Models\GrowthMeasurement::with('child')
                    ->latest()->limit(5)->get();
            @endphp
            @if($recent->count() > 0)
                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach($recent as $m)
                        <li><div class="relative pb-8">@if(!$loop->last)<span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>@endif
                            <div class="relative flex space-x-3">
                                <div><span class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center ring-8 ring-white"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div>
                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                    <div><p class="text-sm text-gray-500"><span class="font-medium text-gray-900">{{ $m->child->full_name }}</span> - Weight: {{ number_format($m->weight, 2) }}kg, Height: {{ number_format($m->height, 1) }}cm</p></div>
                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">{{ \Carbon\Carbon::parse($m->measurement_date)->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div></li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No measurements recorded yet. Start by registering a child.</p>
            @endif
        </div>
    </div>
</div>
@endsection