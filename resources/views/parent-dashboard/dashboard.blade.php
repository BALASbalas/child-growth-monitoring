istration @extends('layouts.app')

@section('header')
Parent Dashboard
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</h3>
                <p class="text-blue-100 mt-1">Parent - Track your child's growth and immunizations</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm">
                    {{ Auth::user()->role_label }}
                </span>
            </div>
        </div>
    </div>

    @php
        $totalChildren = \App\Models\Child::where('user_id', Auth::id())->active()->count();
        $totalMeasurements = \App\Models\GrowthMeasurement::whereHas('child', fn($q) => $q->where('user_id', Auth::id()))->count();
        $dueVaccines = \App\Models\Immunization::whereHas('child', fn($q) => $q->where('user_id', Auth::id()))->scheduled()->upcoming()->count();
        $overdueVaccines = \App\Models\Immunization::whereHas('child', fn($q) => $q->where('user_id', Auth::id()))->overdue()->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div><p class="text-sm text-gray-500">My Children</p><p class="text-2xl font-bold text-gray-900">{{ $totalChildren }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg></div>
            <div><p class="text-sm text-gray-500">Measurements</p><p class="text-2xl font-bold text-gray-900">{{ $totalMeasurements }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg></div>
            <div><p class="text-sm text-gray-500">Due Vaccines</p><p class="text-2xl font-bold text-gray-900">{{ $dueVaccines }}</p></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center space-x-4">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
            <div><p class="text-sm text-gray-500">Overdue</p><p class="text-2xl font-bold text-red-600">{{ $overdueVaccines }}</p></div>
        </div>
    </div>

    <!-- Search Children Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="parentChildSearch()">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="font-semibold text-gray-900 text-lg">Find Your Child's Progress</h3>
                <p class="text-sm text-gray-500">Type your child's name to view growth, immunizations and health records</p>
            </div>
        </div>
        <div class="relative">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" x-model="searchQuery" @input.debounce.200ms="searchChildren()" 
                   class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none text-base transition-all"
                   placeholder="Type your child's name here..." style="font-size:1rem;">
        </div>
        <div class="mt-4" x-show="searched" x-cloak>
            <template x-if="results.length === 0">
                <div class="text-center py-8 text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-sm">No child found with that name.</p>
                    <p class="text-xs text-gray-400 mt-1">Try typing a different name or contact your healthcare provider.</p>
                </div>
            </template>
            <template x-for="child in results" :key="child.id">
                <a :href="'/children/' + child.id" class="block bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-5 mb-3 hover:shadow-md transition-all hover:border-blue-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl"
                                 :style="'background:' + (child.sex === 'male' ? '#2563EB' : '#EC4899')">
                                <span x-text="child.full_name?.charAt(0) || 'C'"></span>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900" x-text="child.full_name"></h4>
                                <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full" :class="child.sex === 'male' ? 'bg-blue-500' : 'bg-pink-500'"></span>
                                        <span x-text="child.sex === 'male' ? 'Male' : 'Female'"></span>
                                    </span>
                                    <span>|</span>
                                    <span x-text="child.age_string"></span>
                                    <span>|</span>
                                    <span x-text="child.unique_id"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs text-gray-400">Vaccines</p>
                                <p class="font-bold" :class="child.vaccine_progress?.split('/')[0] === child.vaccine_progress?.split('/')[1] && parseInt(child.vaccine_progress?.split('/')[1]) > 0 ? 'text-green-600' : 'text-amber-600'" x-text="child.vaccine_progress || '0/0'"></p>
                            </div>
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                    <!-- Mini progress bars -->
                    <div class="mt-3 grid grid-cols-3 gap-2">
                        <div class="bg-white/70 rounded-lg p-2 text-center">
                            <p class="text-xs text-gray-400">Nutrition</p>
                            <p class="text-sm font-semibold" :class="child.nutrition_color === 'green' ? 'text-green-600' : child.nutrition_color === 'orange' ? 'text-orange-600' : 'text-red-600'" x-text="child.nutrition_label || 'N/A'"></p>
                        </div>
                        <div class="bg-white/70 rounded-lg p-2 text-center">
                            <p class="text-xs text-gray-400">Weight</p>
                            <p class="text-sm font-semibold text-gray-700" x-text="child.latest_weight ? child.latest_weight + ' kg' : 'N/A'"></p>
                        </div>
                        <div class="bg-white/70 rounded-lg p-2 text-center">
                            <p class="text-xs text-gray-400">Height</p>
                            <p class="text-sm font-semibold text-gray-700" x-text="child.latest_height ? child.latest_height + ' cm' : 'N/A'"></p>
                        </div>
                    </div>
                </a>
            </template>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('children.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex items-center space-x-4">
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center"><svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div><h4 class="font-semibold text-gray-900">All My Children</h4><p class="text-sm text-gray-500">View your children's growth progress</p></div>
        </a>
        <a href="{{ route('reports.growth') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex items-center space-x-4">
            <div class="w-14 h-14 bg-cyan-100 rounded-xl flex items-center justify-center"><svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg></div>
            <div><h4 class="font-semibold text-gray-900">Growth Reports</h4><p class="text-sm text-gray-500">View detailed growth charts</p></div>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Recent Growth Measurements</h3>
        </div>
        <div class="p-6">
            @php
                $recent = \App\Models\GrowthMeasurement::with('child')
                    ->whereHas('child', fn($q) => $q->where('user_id', Auth::id()))
                    ->latest()->limit(5)->get();
            @endphp
            @if($recent->count() > 0)
                <div class="flow-root">
                    <ul class="-mb-8">
                        @foreach($recent as $m)
                        <li><div class="relative pb-8">@if(!$loop->last)<span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>@endif
                            <div class="relative flex space-x-3">
                                <div><span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div>
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
                <p class="text-gray-500 text-center py-4">No measurements recorded yet. Add your first child to get started.</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function parentChildSearch() {
        return {
            searchQuery: '',
            results: [],
            searched: false,

            async searchChildren() {
                if (!this.searchQuery || this.searchQuery.length < 2) {
                    this.results = [];
                    this.searched = false;
                    return;
                }

                try {
                    const response = await fetch(`/api/children?search=${encodeURIComponent(this.searchQuery)}&per_page=20`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();
                    // data.data contains the transformed children array
                    this.results = data.data || [];
                    this.searched = true;
                } catch (e) {
                    this.results = [];
                    this.searched = true;
                }
            }
        };
    }
</script>
@endpush
@endsection
