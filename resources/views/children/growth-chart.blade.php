@extends('layouts.app')

@section('title', 'Growth Chart - ' . $child->full_name)
@section('header')
Growth Chart - {{ $child->full_name }}
@endsection

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ $child->full_name }}</h3>
                <p class="text-sm text-gray-500">ID: {{ $child->unique_id }} | {{ ucfirst($child->sex) }} | {{ $child->age_string }}</p>
            </div>
            <a href="{{ route('children.show', $child) }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm">← Back to Profile</a>
        </div>
    </div>

    @if(!empty($chartData) && count($chartData['labels'] ?? []) > 0)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Measurement Parameter</label>
                    <select name="parameter" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="weight" {{ $parameter == 'weight' ? 'selected' : '' }}>Weight (kg)</option>
                        <option value="height" {{ $parameter == 'height' ? 'selected' : '' }}>Height (cm)</option>
                        <option value="head_circumference" {{ $parameter == 'head_circumference' ? 'selected' : '' }}>Head Circumference (cm)</option>
                        <option value="muac" {{ $parameter == 'muac' ? 'selected' : '' }}>MUAC (cm)</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                @switch($parameter)
                    @case('weight') Weight-for-Age @break
                    @case('height') Height-for-Age @break
                    @case('head_circumference') Head Circumference @break
                    @case('muac') MUAC @break
                    @default Growth @endswitch Chart
            </h3>
            <canvas id="growthChart" height="300"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('growthChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartData['labels'] ?? []) !!},
                        datasets: [
                            {
                                label: '{{ ucfirst(str_replace('_', ' ', $parameter)) }}',
                                data: {!! json_encode($chartData['values'] ?? []) !!},
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59,130,246,0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointBackgroundColor: '#3b82f6',
                            },
                            {
                                label: 'WHO Median (Z-score 0)',
                                data: {!! json_encode($chartData['who_median'] ?? []) !!},
                                borderColor: '#22c55e',
                                borderDash: [5,5],
                                fill: false,
                                tension: 0.4,
                                pointRadius: 0,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ' + context.parsed.y; } } }
                        },
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Measurement Value' } },
                            x: { title: { display: true, text: 'Age (months)' } }
                        }
                    }
                });
            </script>
            @if(isset($velocity) && $velocity !== null)
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-700"><strong>Growth Velocity:</strong> {{ number_format($velocity, 2) }} units/month</p>
            </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No Growth Data Available</h3>
            <p class="mt-2 text-gray-500">No growth measurements have been recorded for {{ $child->full_name }} yet.</p>
            @if(Auth::user()->isHealthcareWorker())
                <a href="{{ route('growth-measurements.create', ['child_id' => $child->id]) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Record First Measurement
                </a>
            @endif
        </div>
    @endif
</div>
@endsection