@extends('layouts.app')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Register New Child') }}
    </h2>
    <a href="{{ route('children.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 text-sm font-medium">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to List
    </a>
</div>
@endsection

@section('content')
<div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="page-card rounded-3xl shadow-sm border border-slate-700 overflow-hidden">
                <form method="POST" action="{{ route('children.store') }}" class="p-6 bg-slate-950/80 backdrop-blur-sm">
                    @csrf

                    <!-- Section: Basic Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Basic Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Birth Details -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Birth Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required max="{{ date('Y-m-d') }}" onchange="calculateAge()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Age</label>
                                <input type="text" id="age_display" readonly class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm text-gray-600 cursor-not-allowed" placeholder="Select DOB to calculate age">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Sex <span class="text-red-500">*</span></label>
                                <select name="sex" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Sex</option>
                                    <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Birth Weight (kg)</label>
                                <input type="number" step="0.01" name="birth_weight" value="{{ old('birth_weight') }}" min="0.1" max="10" placeholder="3.5 kg" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Birth Length (cm)</label>
                                <input type="number" step="0.1" name="birth_length" value="{{ old('birth_length') }}" min="30" max="70" placeholder="50 cm" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Head Circumference (cm)</label>
                                <input type="number" step="0.1" name="birth_head_circumference" value="{{ old('birth_head_circumference') }}" min="25" max="50" placeholder="35 cm" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Parent/Guardian Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Parent / Guardian Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Mother's Name</label>
                                <input type="text" name="mother_name" value="{{ old('mother_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Mother's Phone</label>
                                <input type="text" name="mother_phone" value="{{ old('mother_phone') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Father's Name</label>
                                <input type="text" name="father_name" value="{{ old('father_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Father's Phone</label>
                                <input type="text" name="father_phone" value="{{ old('father_phone') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Guardian's Name (if applicable)</label>
                                <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Guardian's Phone</label>
                                <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Location -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Location
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Address</label>
                                <input type="text" name="address" value="{{ old('address') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Location/Village</label>
                                <input type="text" name="location" value="{{ old('location') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">District</label>
                                <select name="district" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select District</option>
                                    @php
                                        $tanzaniaDistricts = [
                                            'Arusha', 'Arusha (CC)', 'Babati', 'Babati (TC)', 'Bagamoyo', 'Bukoba', 'Bukoba (TC)',
                                            'Bunda', 'Bunda (TC)', 'Chake Chake', 'Chamwino', 'Chato', 'Dodoma', 'Dodoma (CC)',
                                            'Geita', 'Geita (TC)', 'Hai', 'Hanang', 'Handeni', 'Handeni (TC)', 'Ileje',
                                            'Ilala', 'Iringa', 'Iringa (TC)', 'Itilima', 'Itigi', 'Kager a', 'Kahama',
                                            'Kahama (TC)', 'Kakonko', 'Kaskazini A', 'Kaskazini B', 'Kasulu', 'Kasulu (TC)',
                                            'Kibaha', 'Kibaha (TC)', 'Kibondo', 'Kibondo (TC)', 'Kigoma', 'Kigoma (CC)',
                                            'Kilindi', 'Kilolo', 'Kilombero', 'Kilwa', 'Kinondoni', 'Kishapu', 'Kisarawe',
                                            'Kiteto', 'Kondoa', 'Korogwe', 'Korogwe (TC)', 'Kusini', 'Kyerwa', 'Lindi',
                                            'Lindi (TC)', 'Liwale', 'Longido', 'Ludewa', 'Mafia', 'Magharibi',
                                            'Makambako (TC)', 'Makenge', 'Makete', 'Manyara', 'Masasi', 'Masasi (TC)',
                                            'Mbarali', 'Mbeya', 'Mbeya (CC)', 'Mbozi', 'Mbulu', 'Mchinga', 'Mikindani',
                                            'Mkinga', 'Mkuranga', 'Mlimba', 'Monduli', 'Morogoro', 'Morogoro (CC)',
                                            'Moshi', 'Moshi (TC)', 'Mpanda', 'Mpanda (TC)', 'Mufindi', 'Muheza', 'Muleba',
                                            'Musoma', 'Musoma (TC)', 'Mvomero', 'Mwanga', 'Mwanza', 'Mwanza (CC)',
                                            'Mbarali', 'Mkuranga', 'Nachingwea', 'Namanga', 'Namtumbo', 'Newala',
                                            'Newala (TC)', 'Ngara', 'Ngorongoro', 'Njombe', 'Njombe (TC)', 'Nkasi',
                                            'Nyamagana', 'Nyarugusu', 'Nzega', 'Nzega (TC)', 'Pangani', 'Ruangwa',
                                            'Rufiji', 'Rungwe', 'Saha', 'Same', 'Sengerema', 'Serengeti', 'Shinyanga',
                                            'Shinyanga (TC)', 'Simanjiro', 'Singida', 'Singida (TC)', 'Siha', 'Sikonge',
                                            'Songea', 'Songea (TC)', 'Sumbawanga', 'Sumbawanga (TC)', 'Sumve', 'Tabora',
                                            'Tabora (TC)', 'Tandahimba', 'Tanga', 'Tanga (CC)', 'Tarime', 'Tarime (TC)',
                                            'Temeke', 'Tunduru', 'Ulanga', 'Urambo', 'Ushetu', 'Uvinza', 'Wanging\'ombe',
                                            'Wete', 'Zanzibar', 'Zanzibar (CC)', 'Ziwa', 'Magu', 'Misungwi', 'Kwimba',
                                            'Bariadi', 'Busega', 'Butiama', 'Rorya', 'Rocho', 'Ukerewe', 'Ilemela',
                                            'Kwinba', 'Maswa', 'Meatu', 'Tinde', 'Karumo', 'Katoro', 'Msalala',
                                            'Bukombe', 'Mbogwe', 'Moyowosi', 'Kaliua', 'Igunga', 'Manonyama',
                                            'Mlele', 'Mpimbwe', 'Sikonge', 'Inyonga', 'Kipili', 'Nanyumbu',
                                            'Mtwara', 'Mtwara (MC)', 'Mtwara (DC)', 'Masasi', 'Masasi (TC)',
                                            'Nanyumbu', 'Tandahimba', 'Newala', 'Newala (TC)'
                                        ];
                                        sort($tanzaniaDistricts);
                                        $tanzaniaDistricts = array_unique($tanzaniaDistricts);
                                    @endphp
                                    @foreach($tanzaniaDistricts as $districtOption)
                                        <option value="{{ $districtOption }}" {{ old('district') == $districtOption ? 'selected' : '' }}>{{ $districtOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Medical -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Medical Information
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Medical History</label>
                                <textarea name="medical_history" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Any relevant medical history, allergies, conditions...">{{ old('medical_history') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-200 mb-1">Additional Notes</label>
                                <textarea name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end border-t border-gray-200 pt-6">
                        <a href="{{ route('children.index') }}" class="px-4 py-2 text-slate-200 hover:text-white mr-3">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium shadow-sm">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Register Child
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function calculateAge() {
            var dobInput = document.getElementById('date_of_birth');
            var ageDisplay = document.getElementById('age_display');
            
            if (!dobInput.value) {
                ageDisplay.value = '';
                return;
            }
            
            var dob = new Date(dobInput.value);
            var today = new Date();
            
            var years = today.getFullYear() - dob.getFullYear();
            var months = today.getMonth() - dob.getMonth();
            var days = today.getDate() - dob.getDate();
            
            if (days < 0) {
                months--;
                var prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                days += prevMonth.getDate();
            }
            
            if (months < 0) {
                years--;
                months += 12;
            }
            
            var totalMonths = years * 12 + months;
            
            if (years > 0) {
                ageDisplay.value = years + ' years, ' + months + ' months, ' + days + ' days';
            } else if (totalMonths > 0) {
                ageDisplay.value = totalMonths + ' months, ' + days + ' days';
            } else {
                ageDisplay.value = days + ' days';
            }
        }
        
        // Calculate on load if DOB is already set
        document.addEventListener('DOMContentLoaded', function() {
            calculateAge();
        });
    </script>
@endsection
