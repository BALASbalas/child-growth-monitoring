<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Growth Reports & Analysis') }}</h2>
            <div class="flex space-x-2">
                <a href="{{ route('reports.immunization') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm">Immunization Report</a>
                <a href="{{ route('reports.statistics') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm">Statistics</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-indigo-100 rounded-full p-3"><svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
                        <div class="ml-4"><p class="text-sm text-gray-500">Total Children</p><p class="text-2xl font-bold text-gray-900">{{ $statistics['total_children'] }}</p></div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3"><svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                        <div class="ml-4"><p class="text-sm text-gray-500">Total Measurements</p><p class="text-2xl font-bold text-gray-900">{{ $statistics['total_measurements'] }}</p></div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="bg-red-100 rounded-full p-3"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                        <div class="ml-4"><p class="text-sm text-gray-500">Abnormal Z-scores</p><p class="text-2xl font-bold text-gray-900">{{ $statistics['children_with_abnormal'] }}</p></div>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="w-40"><label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                        <select name="sex" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option value="male" {{ request('sex') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('sex') == 'female' ? 'selected' : '' }}>Female</option>
                        </select></div>
                    <div class="w-48"><label class="block text-sm font-medium text-gray-700 mb-1">Nutritional Status</label>
                        <select name="nutritional_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option value="severe_underweight" {{ request('nutritional_status') == 'severe_underweight' ? 'selected' : '' }}>Severe Underweight</option>
                            <option value="moderate_underweight" {{ request('nutritional_status') == 'moderate_underweight' ? 'selected' : '' }}>Moderate Underweight</option>
                            <option value="normal" {{ request('nutritional_status') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="overweight" {{ request('nutritional_status') == 'overweight' ? 'selected' : '' }}>Overweight</option>
                        </select></div>
                    <div><button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Filter</button>
                        @if(request('sex') || request('nutritional_status'))<a href="{{ route('reports.growth') }}" class="ml-2 px-4 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm">Clear</a>@endif</div>
                </form>
            </div>

            <!-- Nutritional Status Groups -->
            <div class="space-y-6">
                @foreach($childrenByStatus as $status => $childrenInGroup)
                    @if(count($childrenInGroup) > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900 flex items-center">
                                    @php
                                        $statusColors = ['severe_underweight' => 'bg-red-100 text-red-800', 'moderate_underweight' => 'bg-yellow-100 text-yellow-800', 'normal' => 'bg-green-100 text-green-800', 'overweight' => 'bg-orange-100 text-orange-800', 'obese' => 'bg-red-200 text-red-900'];
                                        $statusLabels = ['severe_underweight' => 'Severe Underweight', 'moderate_underweight' => 'Moderate Underweight', 'normal' => 'Normal', 'overweight' => 'Overweight', 'obese' => 'Obese', 'no_data' => 'No Data'];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$status] ?? ucfirst($status) }}
                                    </span>
                                    <span class="ml-3 text-sm text-gray-500">{{ count($childrenInGroup) }} child(ren)</span>
                                </h3>
                                @if($status != 'no_data')
                                <a href="{{ route('reports.export-child', $childrenInGroup[0]) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Export Report</a>
                                @endif
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sex</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Latest Weight</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Latest Height</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($childrenInGroup as $child)
                                            @php
                                                $latest = $child->growthMeasurements->sortByDesc('measurement_date')->first();
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $child->full_name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $child->unique_id }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $child->age_string }}</td>
                                                <td class="px-4 py-3 text-sm capitalize">{{ $child->sex }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $latest ? number_format($latest->weight, 2) . ' kg' : 'N/A' }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $latest ? number_format($latest->height, 1) . ' cm' : 'N/A' }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex space-x-1">
                                                        <a href="{{ route('children.show', $child) }}" class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded text-xs hover:bg-indigo-200">View</a>
                                                        <a href="{{ route('reports.export-child', $child) }}" class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs hover:bg-green-200">Print</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach

                @if($statistics['total_children'] == 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                        <p class="text-gray-500">No children registered yet. Register a child to see growth reports.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
