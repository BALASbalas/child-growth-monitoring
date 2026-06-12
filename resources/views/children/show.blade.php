<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $child->full_name }}
                    <span class="text-sm font-normal text-gray-500 ml-2">ID: {{ $child->unique_id }}</span>
                </h2>
            </div>
            <div class="flex space-x-2">
                @if(Auth::user()->isHealthcareWorker())
                    <a href="{{ route('children.edit', $child) }}" class="inline-flex items-center px-3 py-2 bg-amber-500 text-white text-sm rounded-md hover:bg-amber-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit / Update
                    </a>
                    <form method="POST" action="{{ route('children.destroy', $child) }}" onsubmit="return confirm('Delete this child record? This cannot be undone.')" class="inline-flex">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-500 text-white text-sm rounded-md hover:bg-red-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m2 0H7"/></svg>
                            Delete
                        </button>
                    </form>
                    <a href="{{ route('reports.growth', ['child_id' => $child->id]) }}" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Report
                    </a>
                @endif
                <a href="{{ route('children.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-md hover:bg-gray-200">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">{{ session('success') }}</div>
            @endif

            <!-- Growth Alerts -->
            @if(!empty($alerts) && count($alerts) > 0)
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm">
                    <h4 class="font-bold text-red-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Health Alerts
                    </h4>
                    <ul class="list-disc list-inside text-red-700 text-sm">
                        @foreach($alerts as $alert)
                            <li>{{ $alert }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Child Profile Card -->
                <div class="lg:col-span-1">
                    <div class="page-card rounded-3xl overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-center text-white">
                            <div class="w-20 h-20 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold">{{ $child->full_name }}</h3>
                            <p class="text-indigo-200 text-sm">ID: {{ $child->unique_id }}</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white mt-2">
                                {{ ucfirst($child->sex) }} | {{ $child->age_string }} old
                            </span>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between text-sm py-1 border-b border-gray-100"><span class="text-gray-500">Date of Birth</span><span class="font-medium">{{ $child->date_of_birth->format('d/m/Y') }}</span></div>
                            @if($child->mother_name)<div class="flex justify-between text-sm py-1 border-b border-gray-100"><span class="text-gray-500">Mother</span><span class="font-medium">{{ $child->mother_name }}</span></div>@endif
                            @if($child->father_name)<div class="flex justify-between text-sm py-1 border-b border-gray-100"><span class="text-gray-500">Father</span><span class="font-medium">{{ $child->father_name }}</span></div>@endif
                            @if($child->guardian_name)<div class="flex justify-between text-sm py-1 border-b border-gray-100"><span class="text-gray-500">Guardian</span><span class="font-medium">{{ $child->guardian_name }}</span></div>@endif
                            @if($child->district)<div class="flex justify-between text-sm py-1 border-b border-gray-100"><span class="text-gray-500">District</span><span class="font-medium">{{ $child->district }}</span></div>@endif
                            @if($child->region)<div class="flex justify-between text-sm py-1 border-b border-gray-100"><span class="text-gray-500">Region</span><span class="font-medium">{{ $child->region }}</span></div>@endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="page-card rounded-3xl p-4 mt-6">
                        <h4 class="font-semibold text-slate-100 mb-3">Quick Actions</h4>
                        <div class="space-y-2">
                            @if(Auth::user()->isHealthcareWorker())
                            <a href="{{ route('growth-measurements.create', ['child_id' => $child->id]) }}" class="flex items-center px-3 py-2 bg-green-50 text-green-700 rounded-md hover:bg-green-100 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                Record Measurement
                            </a>
                            <button type="button" id="toggleVaccinationForm" class="flex items-center w-full px-3 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Record Vaccination
                            </button>
                            @endif
                            <a href="{{ route('children.growth-chart', $child) }}" class="flex items-center px-3 py-2 bg-purple-50 text-purple-700 rounded-md hover:bg-purple-100 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                                View Growth Chart
                            </a>
                            <a href="{{ route('children.immunizations', $child) }}" class="flex items-center px-3 py-2 bg-amber-50 text-amber-700 rounded-md hover:bg-amber-100 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Immunization Record
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Latest Measurement -->
                    <div class="page-card rounded-3xl overflow-hidden">
                        <div class="px-6 py-4 bg-slate-900/80 border-b border-slate-700">
                            <h3 class="font-semibold text-slate-100">Latest Growth Measurement</h3>
                        </div>
                        <div class="p-6">
                    @if($latestMeasurement)
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                                        <p class="text-xs text-gray-500">Weight</p>
                                        <p class="text-xl font-bold text-blue-700">{{ number_format($latestMeasurement->weight, 2) }} kg</p>
                                        @if($latestMeasurement->weight_for_age_zscore)<p class="text-xs text-gray-400">Z-score: {{ number_format($latestMeasurement->weight_for_age_zscore, 2) }}</p>@endif
                                    </div>
                                    <div class="text-center p-3 bg-green-50 rounded-lg">
                                        <p class="text-xs text-gray-500">Height</p>
                                        <p class="text-xl font-bold text-green-700">{{ number_format($latestMeasurement->height, 1) }} cm</p>
                                        @if($latestMeasurement->height_for_age_zscore)<p class="text-xs text-gray-400">Z-score: {{ number_format($latestMeasurement->height_for_age_zscore, 2) }}</p>@endif
                                    </div>
                                    <div class="text-center p-3 bg-purple-50 rounded-lg">
                                        <p class="text-xs text-gray-500">Head Circumference</p>
                                        <p class="text-xl font-bold text-purple-700">{{ $latestMeasurement->head_circumference ? number_format($latestMeasurement->head_circumference, 1) . ' cm' : 'N/A' }}</p>
                                    </div>
                                    <div class="text-center p-3 bg-amber-50 rounded-lg">
                                        <p class="text-xs text-gray-500">MUAC</p>
                                        <p class="text-xl font-bold text-amber-700">{{ $latestMeasurement->mid_upper_arm_circumference ? number_format($latestMeasurement->mid_upper_arm_circumference, 1) . ' cm' : 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 text-sm text-gray-500 text-center">
                                    Measured on {{ $latestMeasurement->measurement_date->format('d/m/Y') }}
                                    @if($latestMeasurement->device_id)
                                        | Device: {{ $latestMeasurement->device_id }}
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-6 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    <p>No growth measurements recorded yet.</p>
                                    @if(Auth::user()->isHealthcareWorker())
                                    <a href="{{ route('growth-measurements.create', ['child_id' => $child->id]) }}" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm">Record first measurement →</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Growth History -->
                    <div class="page-card rounded-3xl overflow-hidden">
                        <div class="px-6 py-4 bg-slate-900/80 border-b border-slate-700">
                            <h3 class="font-semibold text-slate-100">Growth History (Last 5 Measurements)</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Weight</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Height</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Head Circ</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-700">
                                    @forelse($child->growthMeasurements()->latest()->limit(5)->get() as $measurement)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm">{{ $measurement->measurement_date->format('d/m/Y') }}</td>
                                            @php
                                                $measurementMonths = $measurement->measurement_date->diffInMonths($child->date_of_birth);
                                                $measurementDays = $measurement->measurement_date->diffInDays($child->date_of_birth);
                                            @endphp
                                            <td class="px-4 py-3 text-sm">{{ $measurementMonths > 0 ? $measurementMonths . ' months' : $measurementDays . ' days' }}</td>
                                            <td class="px-4 py-3 text-sm font-medium">{{ number_format($measurement->weight, 2) }} kg</td>
                                            <td class="px-4 py-3 text-sm">{{ number_format($measurement->height, 1) }} cm</td>
                                            <td class="px-4 py-3 text-sm">{{ $measurement->head_circumference ? number_format($measurement->head_circumference, 1) . ' cm' : 'N/A' }}</td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $status = 'normal';
                                                    if($measurement->weight_for_age_zscore < -3) $status = 'severe';
                                                    elseif($measurement->weight_for_age_zscore < -2) $status = 'moderate';
                                                    elseif($measurement->weight_for_age_zscore > 2) $status = 'overweight';
                                                @endphp
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($status == 'severe') bg-red-100 text-red-800
                                                    @elseif($status == 'moderate') bg-yellow-100 text-yellow-800
                                                    @elseif($status == 'overweight') bg-orange-100 text-orange-800
                                                    @else bg-green-100 text-green-800
                                                    @endif">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No measurements yet</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Immunization Status -->
                    <div class="page-card rounded-3xl overflow-hidden">
                        <div class="px-6 py-4 bg-slate-900/80 border-b border-slate-700 flex justify-between items-center">
                            <h3 class="font-semibold text-slate-100">Immunization Status</h3>
                            <span class="text-sm text-gray-500">{{ $child->immunizations()->administered()->count() }} of {{ $child->immunizations()->count() }} administered</span>
                        </div>
                        <div class="p-6">
                            @php
                                $upcomingVaccines = $child->immunizations()->scheduled()->upcoming()->limit(3)->get();
                                $overdueVaccines = $child->immunizations()->overdue()->get();
                            @endphp
                            
                            @if($overdueVaccines->count() > 0)
                                <div class="mb-3 p-3 bg-red-50 rounded-lg">
                                    <p class="text-sm font-medium text-red-800">{{ $overdueVaccines->count() }} overdue vaccine(s)!</p>
                                    @foreach($overdueVaccines as $v)
                                        <p class="text-xs text-red-600 ml-2">• {{ $v->vaccine_name }} (due: {{ $v->next_due_date?->format('d/m/Y') ?? 'N/A' }})</p>
                                    @endforeach
                                </div>
                            @endif

                            @if($upcomingVaccines->count() > 0)
                                <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                                    <p class="text-sm font-medium text-blue-800">Upcoming Vaccinations</p>
                                    @foreach($upcomingVaccines as $v)
                                        <p class="text-xs text-blue-600 ml-2">• {{ $v->vaccine_name }} (due: {{ $v->next_due_date?->format('d/m/Y') ?? 'N/A' }})</p>
                                    @endforeach
                                </div>
                            @endif

                            @if($overdueVaccines->count() == 0 && $upcomingVaccines->count() == 0)
                                <p class="text-gray-500 text-sm">All vaccinations are up to date.</p>
                            @endif

                            <a href="{{ route('children.immunizations', $child) }}" class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                View full immunization record →
                            </a>
                        </div>
                    </div>

                    <!-- Inline Vaccination Form (hidden by default) -->
                    @if(Auth::user()->isHealthcareWorker())
                    <div id="inlineVaccinationForm" class="page-card rounded-3xl overflow-hidden hidden">
                        <div class="px-6 py-4 bg-slate-900/80 border-b border-slate-700 flex justify-between items-center">
                            <h3 class="font-semibold text-slate-100">Quick Vaccination Record</h3>
                            <button type="button" id="closeVaccinationForm" class="text-slate-400 hover:text-slate-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('immunizations.store') }}">
                                @csrf
                                <input type="hidden" name="child_id" value="{{ $child->id }}">
                                <input type="hidden" name="status" value="administered">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vaccine Name *</label>
                                        <input type="text" name="vaccine_name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. BCG, OPV">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vaccine Type</label>
                                        <input type="text" name="vaccine_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Pentavalent, IPV">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date Administered *</label>
                                        <input type="date" name="date_administered" value="{{ date('Y-m-d') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Next Due Date</label>
                                        <input type="date" name="next_due_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                                        <input type="text" name="batch_number" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dose Volume (ml)</label>
                                        <input type="number" step="0.01" name="dose_volume" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.5">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Route</label>
                                        <select name="route" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select Route</option>
                                            <option value="IM">IM (Intramuscular)</option>
                                            <option value="SC">SC (Subcutaneous)</option>
                                            <option value="Oral">Oral</option>
                                            <option value="ID">ID (Intradermal)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Injection Site</label>
                                        <input type="text" name="site" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Left thigh">
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Additional notes..."></textarea>
                                </div>
                                <div class="flex justify-end border-t border-gray-200 pt-4 mt-4">
                                    <button type="button" id="cancelVaccinationForm" class="px-4 py-2 text-gray-600 hover:text-gray-900 mr-3 text-sm">Cancel</button>
                                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Save Vaccination
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleVaccinationForm');
        const form = document.getElementById('inlineVaccinationForm');
        const closeBtn = document.getElementById('closeVaccinationForm');
        const cancelBtn = document.getElementById('cancelVaccinationForm');

        if (toggleBtn && form) {
            toggleBtn.addEventListener('click', function() {
                form.classList.toggle('hidden');
                // Scroll to the form
                if (!form.classList.contains('hidden')) {
                    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    form.classList.add('hidden');
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    form.classList.add('hidden');
                });
            }
        }
    });
    </script>
</x-app-layout>
