<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>Overdue Vaccinations ⚠️</span>
            <a href="{{ route('immunizations.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm">← All Vaccinations</a>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto">
        @if($immunizations->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Overdue Vaccinations</h3>
                <p class="mt-2 text-gray-500">All children are up to date with their vaccinations! 🎉</p>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Child</th><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Vaccine</th><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Due Date</th><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Days Overdue</th><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($immunizations as $imm)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium">{{ $imm->child->full_name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $imm->vaccine_name }}</td>
                                <td class="px-4 py-3 text-sm text-red-600 font-medium">{{ $imm->next_due_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $imm->next_due_date ? now()->diffInDays($imm->next_due_date) : 'N/A' }} days</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('immunizations.administer', $imm) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="date_administered" value="{{ date('Y-m-d') }}">
                                        <button type="submit" class="px-2 py-1 bg-green-50 text-green-600 rounded text-xs hover:bg-green-100">Mark Given</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
