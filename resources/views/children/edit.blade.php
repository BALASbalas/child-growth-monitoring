@extends('layouts.app')

@section('header')
<div class="flex justify-between items-center">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Child - {{ $child->full_name }}</h2>
    <a href="{{ route('children.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back</a>
</div>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if($errors->any())
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route('children.update', $child) }}" class="p-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label><input type="text" name="first_name" value="{{ old('first_name', $child->first_name) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label><input type="text" name="middle_name" value="{{ old('middle_name', $child->middle_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label><input type="text" name="last_name" value="{{ old('last_name', $child->last_name) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth', $child->date_of_birth->format('Y-m-d')) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Sex *</label><select name="sex" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="male" {{ old('sex', $child->sex) == 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ old('sex', $child->sex) == 'female' ? 'selected' : '' }}>Female</option></select></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Birth Weight (kg)</label><input type="number" step="0.01" name="birth_weight" value="{{ old('birth_weight', $child->birth_weight) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label><input type="text" name="mother_name" value="{{ old('mother_name', $child->mother_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label><input type="text" name="father_name" value="{{ old('father_name', $child->father_name) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><input type="text" name="address" value="{{ old('address', $child->address) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Location/Village</label><input type="text" name="location" value="{{ old('location', $child->location) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                </div>
                <div class="flex justify-end border-t border-gray-200 pt-6">
                    <a href="{{ route('children.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 mr-3">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">Update Child</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection