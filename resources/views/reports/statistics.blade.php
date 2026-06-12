<x-app-layout><x-slot name="header">Analytics & Statistics</x-slot>
<div class="max-w-7xl mx-auto space-y-6">
    @if(!isset($totalChildren) || $totalChildren == 0)
        <div class="bg-slate-900/90 rounded-xl shadow-2xl shadow-slate-950/40 border border-slate-800 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <h3 class="mt-4 text-lg font-medium text-slate-100">No Statistics Available</h3>
            <p class="mt-2 text-slate-400">Register children and record measurements to see analytics.</p></div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 rounded-xl shadow-lg shadow-slate-950/30 border border-slate-800 p-4"><p class="text-sm text-slate-400">Total Children</p><p class="text-2xl font-bold text-slate-100">{{ $totalChildren }}</p></div>
        <div class="bg-slate-900/80 rounded-xl shadow-lg shadow-slate-950/30 border border-slate-800 p-4"><p class="text-sm text-slate-400">Male / Female</p><p class="text-2xl font-bold text-slate-100">{{ $maleChildren }} / {{ $femaleChildren }}</p></div>
        <div class="bg-slate-900/80 rounded-xl shadow-lg shadow-slate-950/30 border border-slate-800 p-4"><p class="text-sm text-slate-400">Total Immunizations</p><p class="text-2xl font-bold text-slate-100">{{ $totalImmunizations }}</p></div>
        <div class="bg-slate-900/80 rounded-xl shadow-lg shadow-slate-950/30 border border-slate-800 p-4"><p class="text-sm text-slate-400">Overdue Vaccines</p><p class="text-2xl font-bold text-rose-300">{{ $overdueImmunizations }}</p></div>
    </div>

    <div class="bg-slate-900/90 rounded-xl shadow-2xl shadow-slate-950/40 border border-slate-800 p-6">
        <h3 class="font-semibold text-slate-100 mb-4">Age Distribution</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($ageDistribution as $age)<div class="text-center p-3 bg-slate-800 rounded"><p class="text-sm text-slate-400">{{ $age->age_group }}</p><p class="text-xl font-bold text-slate-100">{{ $age->count }}</p></div>@endforeach
        </div>
    </div>

    <div class="bg-slate-900/90 rounded-xl shadow-2xl shadow-slate-950/40 border border-slate-800 p-6">
        <h3 class="font-semibold text-slate-100 mb-4">Nutritional Status</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($nutritionalStatusDist as $ns)
                @php
                    $colors = ['severe_underweight'=>'bg-red-100 text-red-700','moderate_underweight'=>'bg-yellow-100 text-yellow-700','normal'=>'bg-green-100 text-green-700','overweight'=>'bg-orange-100 text-orange-700','obese'=>'bg-red-200 text-red-800'];
                @endphp
                <div class="text-center p-3 {{ $colors[$ns->nutritional_status] ?? 'bg-gray-100' }} rounded">
                    <p class="text-sm">{{ ucfirst(str_replace('_',' ',$ns->nutritional_status)) }}</p><p class="text-xl font-bold">{{ $ns->count }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div></x-app-layout>
