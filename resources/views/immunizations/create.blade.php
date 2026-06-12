<x-app-layout>
    <x-slot name="header">Record Vaccination</x-slot>
    <div class="max-w-3xl mx-auto">
        @if($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-md"><ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route('immunizations.store') }}" class="p-6">
                @csrf

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Vaccination Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Child *</label>
                            <select name="child_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Child</option>
                                @foreach($children as $c)
                                    <option value="{{ $c->id }}" {{ ($child && $child->id == $c->id) || old('child_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->unique_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vaccine Schedule</label>
                            <select name="immunization_schedule_id" id="immunization_schedule_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">No Schedule (Custom Vaccine)</option>
                                @foreach($schedules as $s)
                                    <option value="{{ $s->id }}" data-vaccine-name="{{ $s->vaccine_name }}" data-vaccine-type="{{ $s->vaccine_type }}" data-route="{{ $s->route }}" data-dose-volume="{{ $s->dose_volume }}" {{ old('immunization_schedule_id') == $s->id ? 'selected' : '' }}>{{ $s->vaccine_name }} @if($s->due_age_months) — due: {{ $s->due_age_months }}mo @elseif($s->due_age_weeks) — due: {{ $s->due_age_weeks }}wk @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vaccine Name *</label>
                            <select name="vaccine_name" id="vaccine_name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select Vaccine --</option>
                                @php
                                    $uniqueVaccines = $schedules->pluck('vaccine_name')->unique()->sort();
                                @endphp
                                @foreach($uniqueVaccines as $vaccine)
                                    <option value="{{ $vaccine }}" {{ old('vaccine_name') == $vaccine ? 'selected' : '' }}>{{ $vaccine }}</option>
                                @endforeach
                                <option value="__custom__">Other (Custom Vaccine)</option>
                            </select>
                            <input type="text" name="vaccine_name_custom" id="vaccine_name_custom" value="{{ old('vaccine_name') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mt-2 {{ old('vaccine_name') && !$schedules->pluck('vaccine_name')->contains(old('vaccine_name')) ? '' : 'hidden' }}" 
                                   placeholder="Enter custom vaccine name...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vaccine Type</label>
                            <select name="vaccine_type" id="vaccine_type_dropdown" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select Type --</option>
                                @php
                                    $uniqueTypes = $schedules->pluck('vaccine_type')->unique()->sort();
                                @endphp
                                @foreach($uniqueTypes as $type)
                                    <option value="{{ $type }}" {{ old('vaccine_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                                <option value="__custom__">Other (Custom Type)</option>
                            </select>
                            <input type="text" name="vaccine_type_custom" id="vaccine_type_custom" value="{{ old('vaccine_type') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mt-2 {{ old('vaccine_type') && !$schedules->pluck('vaccine_type')->contains(old('vaccine_type')) ? '' : 'hidden' }}"
                                   placeholder="Enter custom vaccine type...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                            <input type="text" name="batch_number" value="{{ old('batch_number') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Batch/Lot #">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dose Volume (ml)</label>
                            <input type="number" step="0.01" name="dose_volume" value="{{ old('dose_volume') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date Administered</label>
                            <input type="date" name="date_administered" value="{{ old('date_administered') }}" max="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Next Due Date</label>
                            <input type="date" name="next_due_date" value="{{ old('next_due_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="scheduled">Scheduled</option>
                                <option value="administered" selected>Administered</option>
                                <option value="missed">Missed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Route</label>
                            <select name="route" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Route</option>
                                <option value="IM" {{ old('route') == 'IM' ? 'selected' : '' }}>IM (Intramuscular)</option>
                                <option value="SC" {{ old('route') == 'SC' ? 'selected' : '' }}>SC (Subcutaneous)</option>
                                <option value="Oral" {{ old('route') == 'Oral' ? 'selected' : '' }}>Oral</option>
                                <option value="ID" {{ old('route') == 'ID' ? 'selected' : '' }}>ID (Intradermal)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Injection Site</label>
                            <input type="text" name="site" value="{{ old('site') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Left thigh, Right arm">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3">Additional Info</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Health Facility</label>
                            <input type="text" name="health_facility" value="{{ old('health_facility', Auth::user()->facility_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Health Worker Name</label>
                            <input type="text" name="health_worker_name" value="{{ old('health_worker_name', Auth::user()->name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adverse Reactions</label>
                            <textarea name="adverse_reactions" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Any reactions after vaccination...">{{ old('adverse_reactions') }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-200 pt-6">
                    <a href="{{ route('immunizations.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 mr-3">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Vaccination Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleSelect = document.getElementById('immunization_schedule_id');
            const vaccineNameSelect = document.getElementById('vaccine_name');
            const vaccineNameCustom = document.getElementById('vaccine_name_custom');
            const vaccineTypeSelect = document.getElementById('vaccine_type_dropdown');
            const vaccineTypeCustom = document.getElementById('vaccine_type_custom');
            const routeSelect = document.querySelector('select[name="route"]');
            const doseVolumeInput = document.querySelector('input[name="dose_volume"]');

            // When schedule is selected, auto-fill vaccine name, type, route, dose
            if (scheduleSelect) {
                scheduleSelect.addEventListener('change', function() {
                    const opt = scheduleSelect.options[scheduleSelect.selectedIndex];
                    if (!opt || !opt.value) return;
                    
                    const name = opt.dataset.vaccineName || '';
                    const type = opt.dataset.vaccineType || '';
                    const route = opt.dataset.route || '';
                    const doseVolume = opt.dataset.doseVolume || '';
                    
                    if (name) {
                        // Check if name exists in dropdown
                        let found = false;
                        for (let i = 0; i < vaccineNameSelect.options.length; i++) {
                            if (vaccineNameSelect.options[i].value === name) {
                                vaccineNameSelect.value = name;
                                found = true;
                                break;
                            }
                        }
                        if (!found) {
                            vaccineNameSelect.value = '__custom__';
                            vaccineNameCustom.value = name;
                            vaccineNameCustom.classList.remove('hidden');
                        }
                    }
                    
                    if (type) {
                        let found = false;
                        for (let i = 0; i < vaccineTypeSelect.options.length; i++) {
                            if (vaccineTypeSelect.options[i].value === type) {
                                vaccineTypeSelect.value = type;
                                found = true;
                                break;
                            }
                        }
                        if (!found) {
                            vaccineTypeSelect.value = '__custom__';
                            vaccineTypeCustom.value = type;
                            vaccineTypeCustom.classList.remove('hidden');
                        }
                    }
                    
                    if (route && routeSelect) routeSelect.value = route;
                    if (doseVolume && doseVolumeInput) doseVolumeInput.value = doseVolume;
                });
            }

            // When vaccine name select changes, show/hide custom input
            if (vaccineNameSelect) {
                vaccineNameSelect.addEventListener('change', function() {
                    if (this.value === '__custom__') {
                        vaccineNameCustom.classList.remove('hidden');
                        vaccineNameCustom.focus();
                    } else {
                        vaccineNameCustom.classList.add('hidden');
                        vaccineNameCustom.value = '';
                    }
                });
            }

            // When vaccine type select changes, show/hide custom input
            if (vaccineTypeSelect) {
                vaccineTypeSelect.addEventListener('change', function() {
                    if (this.value === '__custom__') {
                        vaccineTypeCustom.classList.remove('hidden');
                        vaccineTypeCustom.focus();
                    } else {
                        vaccineTypeCustom.classList.add('hidden');
                        vaccineTypeCustom.value = '';
                    }
                });
            }

            // Form submission handler to set correct values
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    // If custom vaccine name is active, set the select value
                    if (vaccineNameSelect.value === '__custom__' && vaccineNameCustom.value) {
                        // Add a hidden input with the custom name
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'vaccine_name';
                        hidden.value = vaccineNameCustom.value;
                        vaccineNameSelect.disabled = true;
                        form.appendChild(hidden);
                    }
                    
                    // If custom vaccine type is active, set the select value
                    if (vaccineTypeSelect.value === '__custom__' && vaccineTypeCustom.value) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'vaccine_type';
                        hidden.value = vaccineTypeCustom.value;
                        vaccineTypeSelect.disabled = true;
                        form.appendChild(hidden);
                    }
                });
            }
        });
    </script>
</x-app-layout>
