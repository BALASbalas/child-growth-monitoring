<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Growth Report - {{ $child->full_name }}</title>
    <style>
        @page { margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1a202c; line-height: 1.6; font-size: 12px; }
        .header { text-align: center; padding: 20px 0; border-bottom: 3px solid #4f46e5; margin-bottom: 20px; }
        .header h1 { font-size: 22px; color: #4f46e5; margin-bottom: 5px; }
        .header p { color: #718096; font-size: 12px; }
        .report-title { font-size: 18px; text-align: center; margin: 15px 0; color: #2d3748; }
        
        .section { margin: 20px 0; }
        .section-title { font-size: 14px; font-weight: 700; color: #4f46e5; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .info-item { padding: 6px 0; border-bottom: 1px dashed #e2e8f0; }
        .info-item .label { color: #718096; font-weight: 600; font-size: 11px; }
        .info-item .value { color: #1a202c; font-weight: 500; }
        
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background: #ebf4ff; color: #4a5568; font-weight: 600; padding: 8px 6px; text-align: left; font-size: 11px; border: 1px solid #e2e8f0; }
        td { padding: 6px; border: 1px solid #e2e8f0; font-size: 11px; }
        tr:nth-child(even) { background: #f7fafc; }
        
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .status-normal { background: #c6f6d5; color: #22543d; }
        .status-moderate { background: #fefcbf; color: #744210; }
        .status-severe { background: #fed7d7; color: #822727; }
        .status-overweight { background: #feebc8; color: #7b341e; }
        
        .alerts { background: #fff5f5; border: 1px solid #feb2b2; border-radius: 8px; padding: 12px; margin: 15px 0; }
        .alerts h4 { color: #c53030; margin-bottom: 8px; }
        .alerts li { color: #9b2c2c; margin-left: 15px; }
        
        .footer { text-align: center; color: #a0aec0; font-size: 10px; padding: 15px 0; border-top: 1px solid #e2e8f0; margin-top: 30px; }
        
        .signature { margin-top: 30px; }
        .signature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 10px; }
        .signature-line { border-top: 1px solid #000; width: 200px; margin-top: 35px; padding-top: 5px; font-size: 11px; text-align: center; }

        .page-break { page-break-before: always; }
        
        @media print {
            .no-print { display: none; }
            body { font-size: 11px; }
        }
        .no-print { text-align: center; margin: 20px 0; }
        .no-print button { background: #4f46e5; color: white; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .no-print button:hover { background: #4338ca; }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Print / Save as PDF</button>
        <button onclick="window.close()" style="background:#718096;margin-left:10px;">Close</button>
    </div>

    <div class="header">
        <h1>🏥 Child Growth Monitoring System</h1>
        <p>Comprehensive Child Health Progress Report</p>
    </div>

    <div class="report-title">Child Growth & Immunization Report</div>

    <!-- Child Information -->
    <div class="section">
        <div class="section-title">Child Information</div>
        <div class="info-grid">
            <div class="info-item"><span class="label">Full Name</span><br><span class="value">{{ $child->full_name }}</span></div>
            <div class="info-item"><span class="label">Unique ID</span><br><span class="value">{{ $child->unique_id }}</span></div>
            <div class="info-item"><span class="label">Date of Birth</span><br><span class="value">{{ $child->date_of_birth->format('d/m/Y') }}</span></div>
            <div class="info-item"><span class="label">Sex</span><br><span class="value">{{ ucfirst($child->sex) }}</span></div>
            <div class="info-item"><span class="label">Age</span><br><span class="value">{{ $child->age_string }}</span></div>
            <div class="info-item"><span class="label">Mother</span><br><span class="value">{{ $child->mother_name ?? 'N/A' }}</span></div>
            <div class="info-item"><span class="label">Father</span><br><span class="value">{{ $child->father_name ?? 'N/A' }}</span></div>
            <div class="info-item"><span class="label">District</span><br><span class="value">{{ $child->district ?? 'N/A' }}</span></div>
        </div>
    </div>

    <!-- Alerts -->
    @if(!empty($report['alerts']) && count($report['alerts']) > 0)
        <div class="alerts">
            <h4>⚠️ Health Alerts</h4>
            <ul>
                @foreach($report['alerts'] as $alert)
                    <li>{{ $alert }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Growth Measurements -->
    <div class="section">
        <div class="section-title">Growth Measurements History</div>
        @if(count($report['growth_history']) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Age</th>
                        <th>Weight (kg)</th>
                        <th>Height (cm)</th>
                        <th>Head Circ (cm)</th>
                        <th>MUAC (cm)</th>
                        <th>WAZ</th>
                        <th>HAZ</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['growth_history'] as $measurement)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($measurement['date'])->format('d/m/Y') }}</td>
                            <td>{{ $measurement['age_months'] }}m</td>
                            <td>{{ $measurement['weight'] }}</td>
                            <td>{{ $measurement['height'] }}</td>
                            <td>{{ $measurement['head_circumference'] ?? 'N/A' }}</td>
                            <td>{{ $measurement['muac'] ?? 'N/A' }}</td>
                            <td>{{ $measurement['waz'] ?? 'N/A' }}</td>
                            <td>{{ $measurement['haz'] ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $status = $measurement['nutritional_status'] ?? 'normal';
                                @endphp
                                <span class="status-badge status-{{ $status == 'severe_underweight' ? 'severe' : ($status == 'moderate_underweight' ? 'moderate' : ($status == 'overweight' ? 'overweight' : 'normal')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color:#718096;">No growth measurements recorded.</p>
        @endif
    </div>

    <!-- Immunization History -->
    <div class="section page-break">
        <div class="section-title">Immunization History</div>
        @if(count($report['immunization_history']) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Vaccine</th>
                        <th>Type</th>
                        <th>Due Date</th>
                        <th>Date Administered</th>
                        <th>Status</th>
                        <th>Batch #</th>
                        <th>Site</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['immunization_history'] as $imm)
                        <tr>
                            <td><strong>{{ $imm['vaccine'] }}</strong></td>
                            <td>{{ $imm['type'] ?? 'N/A' }}</td>
                            <td>{{ $imm['due_date'] ? \Carbon\Carbon::parse($imm['due_date'])->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $imm['administered_date'] ? \Carbon\Carbon::parse($imm['administered_date'])->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                @php
                                    $statusClass = match($imm['status']) {
                                        'administered' => 'status-normal',
                                        'scheduled' => 'status-moderate',
                                        default => 'status-severe'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ ucfirst($imm['status']) }}</span>
                            </td>
                            <td>{{ $imm['batch_number'] ?? 'N/A' }}</td>
                            <td>{{ $imm['site'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color:#718096;">No immunizations recorded.</p>
        @endif
    </div>

    <!-- Summary -->
    <div class="section">
        <div class="section-title">Growth Summary</div>
        <div class="info-grid">
            @php
                $latestGrowth = $report['growth_history'][0] ?? null;
                $totalGrowth = count($report['growth_history']);
                $totalImmunizations = count($report['immunization_history']);
                $administered = collect($report['immunization_history'])->where('status', 'administered')->count();
            @endphp
            @if($latestGrowth)
                <div class="info-item"><span class="label">Latest Weight</span><br><span class="value">{{ $latestGrowth['weight'] }} kg</span></div>
                <div class="info-item"><span class="label">Latest Height</span><br><span class="value">{{ $latestGrowth['height'] }} cm</span></div>
                <div class="info-item"><span class="label">Last Measurement</span><br><span class="value">{{ \Carbon\Carbon::parse($latestGrowth['date'])->format('d/m/Y') }}</span></div>
            @endif
            <div class="info-item"><span class="label">Total Visits</span><br><span class="value">{{ $totalGrowth }}</span></div>
            <div class="info-item"><span class="label">Immunizations</span><br><span class="value">{{ $administered }}/{{ $totalImmunizations }} completed</span></div>
            <div class="info-item"><span class="label">Report Generated</span><br><span class="value">{{ \Carbon\Carbon::parse($report['generated_at'])->format('d/m/Y H:i') }}</span></div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signature">
        <div class="section-title">Authorizations</div>
        <div class="signature-grid">
            <div>
                <div class="signature-line">Healthcare Worker Signature</div>
                <p style="margin-top:5px;font-size:10px;color:#718096;">{{ Auth::user()->name }}<br>{{ Auth::user()->facility_name ?? 'Healthcare Facility' }}</p>
            </div>
            <div>
                <div class="signature-line">Parent/Guardian Signature</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This report is generated automatically by the Digital Child Growth Monitoring System</p>
        <p>© {{ date('Y') }} Child Growth Monitor. All rights reserved.</p>
    </div>
</body>
</html>
