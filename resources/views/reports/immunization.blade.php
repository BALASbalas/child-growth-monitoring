<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>Immunization Reports</span>
            <a href="{{ route('reports.growth') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm">Growth Reports</a>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto">
        @if(!isset($statistics) || $statistics['total_scheduled'] + $statistics['total_administered'] == 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Immunization Data</h3>
                <p class="mt-2 text-gray-500">No immunizations have been recorded yet.</p></div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4"><p class="text-sm text-gray-500">Scheduled</p><p class="text-2xl font-bold text-blue-600">{{ $statistics['total_scheduled'] }}</p></div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4"><p class="text-sm text-gray-500">Administered</p><p class="text-2xl font-bold text-green-600">{{ $statistics['total_administered'] }}</p></div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4"><p class="text-sm text-gray-500">Missed</p><p class="text-2xl font-bold text-yellow-600">{{ $statistics['total_missed'] }}</p></div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4"><p class="text-sm text-gray-500">Overdue</p><p class="text-2xl font-bold text-red-600">{{ $statistics['total_overdue'] }}</p></div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">By Vaccine Type</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Vaccine</th><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Total</th><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Administered</th><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Scheduled</th><th class="px-4 py-3 text-xs font-medium text-gray-500 uppercase">Overdue</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($byVaccine ?? [] as $name => $stats)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium">{{ $name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $stats['total'] }}</td>
                            <td class="px-4 py-3 text-sm text-green-600">{{ $stats['administered'] }}</td>
                            <td class="px-4 py-3 text-sm text-blue-600">{{ $stats['scheduled'] }}</td>
                            <td class="px-4 py-3 text-sm text-red-600">{{ $stats['overdue'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
