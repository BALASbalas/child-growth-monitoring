<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>Immunizations for {{ $child->full_name }}</span>
            <div class="flex space-x-2">
                @if(Auth::user()->isHealthcareWorker())
                <a href="{{ route('immunizations.generate-schedule', $child) }}" class="px-3 py-2 bg-indigo-100 text-indigo-600 rounded-md hover:bg-indigo-200 text-sm" onclick="return confirm('Generate immunization schedule for this child?')">
                    Generate Schedule
                </a>
                <a href="{{ route('immunizations.create', ['child_id' => $child->id]) }}" class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">+ Record Vaccine</a>
                @endif
                <a href="{{ route('children.show', $child) }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-md">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-md">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Overdue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-red-50 border-b border-red-100">
                    <h3 class="font-semibold text-red-700">Overdue ⚠️</h3>
                </div>
                <div class="p-4">
                    @if($overdue->isNotEmpty())
                        <ul class="space-y-2">
                            @foreach($overdue as $v)
                                <li class="text-sm p-2 bg-red-50 rounded">{{ $v->vaccine_name }} - due {{ $v->next_due_date?->format('d/m/Y') ?? 'N/A' }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 text-center py-6">No overdue vaccinations 🎉</p>
                    @endif
                </div>
            </div>

            <!-- Upcoming -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-blue-50 border-b border-blue-100">
                    <h3 class="font-semibold text-blue-700">Upcoming 📅</h3>
                </div>
                <div class="p-4">
                    @if($upcoming->isNotEmpty())
                        <ul class="space-y-2">
                            @foreach($upcoming as $v)
                                <li class="text-sm p-2 bg-blue-50 rounded">{{ $v->vaccine_name }} - due {{ $v->next_due_date?->format('d/m/Y') ?? 'N/A' }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 text-center py-6">No upcoming vaccinations scheduled.</p>
                    @endif
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-green-50 border-b border-green-100">
                    <h3 class="font-semibold text-green-700">Completed ✅</h3>
                </div>
                <div class="p-4">
                    @if($completed->isNotEmpty())
                        <ul class="space-y-2">
                            @foreach($completed as $v)
                                <li class="text-sm p-2 bg-green-50 rounded">
                                    {{ $v->vaccine_name }} - {{ $v->date_administered?->format('d/m/Y') ?? 'N/A' }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 text-center py-6">No vaccines administered yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Full Immunization Record Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-6">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Full Immunization Record</h3>
            </div>
            @php $allImmunizations = $child->immunizations()->with('immunizationSchedule')->latest()->get(); @endphp
            
            @if($allImmunizations->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Vaccine</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Given Date</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Batch</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Given By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($allImmunizations as $imm)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium">{{ $imm->vaccine_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $imm->vaccine_type ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $imm->next_due_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $imm->date_administered?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $imm->batch_number ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($imm->status == 'administered') bg-green-100 text-green-700
                                        @elseif($imm->status == 'scheduled') bg-blue-100 text-blue-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ ucfirst($imm->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $imm->health_worker_name ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No Vaccination Records</h3>
                    <p class="mt-2 text-gray-500">{{ $child->full_name }} has no immunization records yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
