<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Child Health Report - {{ $child->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            @page { margin: 10mm; size: A4; }
            .page-break { page-break-before: always; }
        }
        .print-container { max-width: 800px; margin: 0 auto; }
        .header-section { border-bottom: 3px solid #1e40af; padding-bottom: 15px; margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table th, .info-table td { border: 1px solid #d1d5db; padding: 8px 12px; text-align: left; }
        .info-table th { background-color: #eff6ff; color: #1e40af; font-weight: 600; width: 35%; }
        .section-title { color: #1e40af; font-weight: 700; font-size: 16px; border-bottom: 2px solid #93c5fd; padding-bottom: 5px; margin: 20px 0 10px 0; }
    </style>
</head>
<body class="bg-white text-gray-800 font-sans p-6">
    <div class="print-container">
        
        <!-- Print Button (hidden on print) -->
        <div class="no-print mb-4 flex justify-between items-center">
            <a href="{{ route('children.show', $child) }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back</a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                🖨️ Print
            </button>
        </div>

        <!-- Header -->
        <div class="header-section">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-blue-900">Child Health Report</h1>
                    <p class="text-gray-600 text-sm">Medical and Growth Information</p>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <p>Date: {{ now()->format('d/m/Y H:i') }}</p>
                    <p>Printed by: {{ Auth::user()->name }} ({{ Auth::user()->role_label }})</p>
                </div>
            </div>
        </div>

        <!-- Child Basic Information -->
        <h2 class="section-title">📋 Personal Information</h2>
        <table class="info-table">
            <tr>
                <th>Full Name</th>
                <td>{{ $child->full_name }}</td>
            </tr>
            <tr>
                <th>Unique ID</th>
                <td><strong>{{ $child->unique_id }}</strong></td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td>{{ $child->date_of_birth ? $child->date_of_birth->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Age</th>
                <td>{{ $child->age_string }}</td>
            </tr>
            <tr>
                <th>Sex</th>
                <td>{{ $child->sex === 'male' ? 'Male' : 'Female' }}</td>
            </tr>
        </table>

        <!-- Birth Information -->
        <h2 class="section-title">👶 Birth Information</h2>
        <table class="info-table">
            @if($child->gestational_age_weeks)
            <tr>
                <th>Gestational Age</th>
                <td>{{ $child->gestational_age_weeks }} weeks</td>
            </tr>
            @endif
            @if($child->birth_weight)
            <tr>
                <th>Birth Weight</th>
                <td>{{ number_format($child->birth_weight, 2) }} kg</td>
            </tr>
            @endif
            @if($child->birth_length)
            <tr>
                <th>Birth Length</th>
                <td>{{ number_format($child->birth_length, 1) }} cm</td>
            </tr>
            @endif
            @if($child->birth_head_circumference)
            <tr>
                <th>Head Circumference</th>
                <td>{{ number_format($child->birth_head_circumference, 1) }} cm</td>
            </tr>
            @endif
        </table>

        <!-- Parent/Guardian Information -->
        <h2 class="section-title">👨‍👩‍👧 Parent / Guardian Information</h2>
        <table class="info-table">
            @if($child->mother_name)
            <tr>
                <th>Mother Name</th>
                <td>{{ $child->mother_name }} @if($child->mother_phone) - {{ $child->mother_phone }} @endif</td>
            </tr>
            @endif
            @if($child->father_name)
            <tr>
                <th>Father Name</th>
                <td>{{ $child->father_name }} @if($child->father_phone) - {{ $child->father_phone }} @endif</td>
            </tr>
            @endif
            @if($child->guardian_name)
            <tr>
                <th>Guardian Name</th>
                <td>{{ $child->guardian_name }} @if($child->guardian_phone) - {{ $child->guardian_phone }} @endif</td>
            </tr>
            @endif
            @if($child->address)
            <tr>
                <th>Address</th>
                <td>{{ $child->address }}</td>
            </tr>
            @endif
            @if($child->location || $child->district || $child->region)
            <tr>
                <th>Location</th>
                <td>{{ collect([$child->location, $child->district, $child->region])->filter()->implode(', ') }}</td>
            </tr>
            @endif
        </table>

        <!-- Latest Growth Measurements -->
        <h2 class="section-title">📊 Latest Growth Measurements</h2>
        @if($latestMeasurement)
        <table class="info-table">
            <tr>
                <th>Date</th>
                <td>{{ \Carbon\Carbon::parse($latestMeasurement->measurement_date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Weight</th>
                <td>{{ number_format($latestMeasurement->weight, 2) }} kg</td>
            </tr>
            <tr>
                <th>Height/Length</th>
                <td>{{ number_format($latestMeasurement->height, 1) }} cm</td>
            </tr>
            @if($latestMeasurement->head_circumference)
            <tr>
                <th>Head Circumference</th>
                <td>{{ number_format($latestMeasurement->head_circumference, 1) }} cm</td>
            </tr>
            @endif
            @if($latestMeasurement->mid_upper_arm_circumference)
            <tr>
                <th>MUAC</th>
                <td>{{ number_format($latestMeasurement->mid_upper_arm_circumference, 1) }} cm</td>
            </tr>
            @endif
            @if($latestMeasurement->notes)
            <tr>
                <th>Notes</th>
                <td>{{ $latestMeasurement->notes }}</td>
            </tr>
            @endif
        </table>
        @else
        <p class="text-gray-500 italic">No measurements recorded yet.</p>
        @endif

        <!-- Growth Measurements History -->
        @php
            $measurements = $child->growthMeasurements()->latest('measurement_date')->limit(10)->get();
        @endphp
        @if($measurements->count() > 0)
        <h2 class="section-title page-break">📈 Measurement History</h2>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Weight</th>
                    <th>Height</th>
                    <th>HC</th>
                    <th>MUAC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($measurements as $m)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($m->measurement_date)->format('d/m/Y') }}</td>
                    <td>{{ number_format($m->weight, 2) }} kg</td>
                    <td>{{ number_format($m->height, 1) }} cm</td>
                    <td>{{ $m->head_circumference ? number_format($m->head_circumference, 1).' cm' : '-' }}</td>
                    <td>{{ $m->mid_upper_arm_circumference ? number_format($m->mid_upper_arm_circumference, 1).' cm' : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Immunization Records -->
        @php
            $allImmunizations = $child->immunizations()->latest('date_administered')->get();
        @endphp
        @if($allImmunizations->count() > 0)
        <h2 class="section-title">💉 Immunization Records</h2>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Vaccine</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allImmunizations as $imm)
                <tr>
                    <td>{{ $imm->vaccine_name ?? ($imm->immunizationSchedule ? $imm->immunizationSchedule->vaccine_name : 'N/A') }}</td>
                    <td>{{ $imm->date_administered ? \Carbon\Carbon::parse($imm->date_administered)->format('d/m/Y') : ($imm->scheduled_date ? \Carbon\Carbon::parse($imm->scheduled_date)->format('d/m/Y') : '-') }}</td>
                    <td>
                        @if($imm->status === 'administered')
                            <span class="text-green-600">✅ Administered</span>
                        @elseif($imm->status === 'scheduled')
                            <span class="text-yellow-600">📅 Scheduled</span>
                        @elseif($imm->status === 'overdue')
                            <span class="text-red-600">⚠️ Overdue</span>
                        @else
                            {{ ucfirst($imm->status) }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Medical History -->
        @if($child->medical_history)
        <h2 class="section-title">🏥 Medical History</h2>
        <p class="bg-gray-50 border border-gray-200 rounded p-4 text-sm">{{ $child->medical_history }}</p>
        @endif

        <!-- Notes -->
        @if($child->notes)
        <h2 class="section-title">📝 Additional Notes</h2>
        <p class="bg-gray-50 border border-gray-200 rounded p-4 text-sm">{{ $child->notes }}</p>
        @endif

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-gray-300 text-center text-xs text-gray-400">
            <p>Report generated by Child Growth Monitoring System | {{ now()->format('d/m/Y H:i') }}</p>
            <p>Registered by: {{ $child->user ? $child->user->name : 'N/A' }} ({{ $child->user ? $child->user->role_label : 'N/A' }})</p>
        </div>

    </div>
</body>
</html>
