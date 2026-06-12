<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>Immunization Details</span>
            <a href="{{ route('immunizations.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm">← Back</a>
        </div>
    </x-slot>
    <div class="max-w-3xl mx-auto">
        @php $imm = $immunization; @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200"><h3 class="font-semibold text-gray-900">{{ $imm->vaccine_name }} - {{ $imm->child->full_name }}</h3></div>
            <div class="p-6 space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="text-sm text-gray-500">Child</span><p class="font-medium">{{ $imm->child->full_name }}</p></div>
                    <div><span class="text-sm text-gray-500">Vaccine</span><p class="font-medium">{{ $imm->vaccine_name }} @if($imm->vaccine_type)({{ $imm->vaccine_type }})@endif</p></div>
                    <div><span class="text-sm text-gray-500">Status</span>
                        <p><span class="px-2 py-1 text-xs rounded-full @if($imm->status=='administered')bg-green-100 text-green-700 @elseif($imm->status=='scheduled')bg-blue-100 text-blue-700 @else bg-red-100 text-red-700 @endif">{{ ucfirst($imm->status) }}</span></p></div>
                    <div><span class="text-sm text-gray-500">Date Administered</span><p class="font-medium">{{ $imm->date_administered?->format('d/m/Y') ?? 'Not yet' }}</p></div>
                    <div><span class="text-sm text-gray-500">Due Date</span><p class="font-medium">{{ $imm->next_due_date?->format('d/m/Y') ?? 'N/A' }}</p></div>
                    <div><span class="text-sm text-gray-500">Batch Number</span><p class="font-medium">{{ $imm->batch_number ?? 'N/A' }}</p></div>
                    <div><span class="text-sm text-gray-500">Route</span><p class="font-medium">{{ $imm->route ?? 'N/A' }}</p></div>
                    <div><span class="text-sm text-gray-500">Injection Site</span><p class="font-medium">{{ $imm->site ?? 'N/A' }}</p></div>
                    <div><span class="text-sm text-gray-500">Health Facility</span><p class="font-medium">{{ $imm->health_facility ?? 'N/A' }}</p></div>
                    <div><span class="text-sm text-gray-500">Health Worker</span><p class="font-medium">{{ $imm->health_worker_name ?? 'N/A' }}</p></div>
                </div>
                @if($imm->adverse_reactions)
                    <div class="mt-4 p-3 bg-red-50 rounded"><span class="text-sm font-medium text-red-700">Adverse Reactions:</span><p class="text-sm text-red-600 mt-1">{{ $imm->adverse_reactions }}</p></div>
                @endif
                @if($imm->notes)
                    <div class="mt-2 p-3 bg-gray-50 rounded"><span class="text-sm font-medium text-gray-700">Notes:</span><p class="text-sm text-gray-600 mt-1">{{ $imm->notes }}</p></div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
