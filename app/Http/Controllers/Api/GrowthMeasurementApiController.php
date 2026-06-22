<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\GrowthMeasurement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrowthMeasurementApiController extends Controller
{
    private function hasAnyMeasurementValue(array $validated): bool
    {
        foreach (['weight', 'height', 'head_circumference', 'mid_upper_arm_circumference', 'temperature'] as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] !== null && $validated[$field] !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get growth measurements list for DataTable
     */
    public function index(Request $request)
    {
        $query = GrowthMeasurement::with(['child', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('child', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('unique_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('child_id')) {
            $query->where('child_id', $request->child_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('measurement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('measurement_date', '<=', $request->date_to);
        }

        $measurements = $query->latest('measurement_date')->paginate(15);

        $measurements->getCollection()->transform(function($m) {
            return [
                'id' => $m->id,
                'child_id' => $m->child_id,
                'child_name' => $m->child ? $m->child->full_name : 'Unknown',
                'child_unique_id' => $m->child ? $m->child->unique_id : 'N/A',
                'measured_by' => $m->user ? $m->user->name : 'Unknown',
                'measurement_date' => $m->measurement_date ? $m->measurement_date->format('Y-m-d') : null,
                'weight' => $m->weight,
                'height' => $m->height,
                'head_circumference' => $m->head_circumference,
                'mid_upper_arm_circumference' => $m->mid_upper_arm_circumference,
                'bmi' => $m->bmi,
                'nutritional_status' => $m->nutritional_status,
                'stunting_status' => $m->stunting_status,
                'wasting_status' => $m->wasting_status,
                'weight_for_age_zscore' => $m->weight_for_age_zscore,
                'height_for_age_zscore' => $m->height_for_age_zscore,
                'weight_for_height_zscore' => $m->weight_for_height_zscore,
                'clinical_notes' => $m->clinical_notes,
                'is_from_device' => $m->is_from_device,
                'created_at' => $m->created_at ? $m->created_at->format('Y-m-d H:i:s') : null,
            ];
        });

        return response()->json($measurements);
    }

    /**
     * Store a new growth measurement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'measurement_date' => 'required|date',
            'weight' => 'nullable|numeric|min:0.1|max:100',
            'height' => 'nullable|numeric|min:10|max:200',
            'head_circumference' => 'nullable|numeric|min:20|max:60',
            'mid_upper_arm_circumference' => 'nullable|numeric|min:5|max:40',
            'temperature' => 'nullable|numeric|max:43',
            'clinical_notes' => 'nullable|string',
        ]);

        if (!$this->hasAnyMeasurementValue($validated)) {
            return response()->json([
                'message' => 'Please provide at least one measurement value (weight, height, head circumference, MUAC, or temperature).'
            ], 422);
        }

        $validated['user_id'] = Auth::id();

        // Calculate age in months
        $child = Child::findOrFail($validated['child_id']);
        $validated['age_in_months'] = $child->age_in_months;

        // Calculate BMI if weight and height provided
        if (!empty($validated['weight']) && !empty($validated['height'])) {
            $heightInMeters = $validated['height'] / 100;
            $validated['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        $measurement = GrowthMeasurement::create($validated);

        return response()->json(['success' => true, 'message' => 'Measurement added successfully.', 'measurement' => $measurement], 201);
    }

    /**
     * Get a single measurement
     */
    public function show($id)
    {
        $growthMeasurement = GrowthMeasurement::with(['child', 'user'])->findOrFail($id);
        return response()->json($growthMeasurement);
    }

    /**
     * Update a measurement
     */
    public function update(Request $request, $id)
    {
        $growthMeasurement = GrowthMeasurement::findOrFail($id);

        $validated = $request->validate([
            'measurement_date' => 'required|date',
            'weight' => 'nullable|numeric|min:0.1|max:100',
            'height' => 'nullable|numeric|min:10|max:200',
            'head_circumference' => 'nullable|numeric|min:20|max:60',
            'mid_upper_arm_circumference' => 'nullable|numeric|min:5|max:40',
            'temperature' => 'nullable|numeric|max:43',
            'clinical_notes' => 'nullable|string',
        ]);

        if (!$this->hasAnyMeasurementValue($validated)) {
            return response()->json([
                'message' => 'Please provide at least one measurement value (weight, height, head circumference, MUAC, or temperature).'
            ], 422);
        }

        // Recalculate BMI if weight or height changed
        if ((!empty($validated['weight']) || !empty($validated['height'])) && !empty($growthMeasurement->child)) {
            $weight = $validated['weight'] ?? $growthMeasurement->weight;
            $height = $validated['height'] ?? $growthMeasurement->height;
            if ($weight && $height) {
                $heightInMeters = $height / 100;
                $validated['bmi'] = round($weight / ($heightInMeters * $heightInMeters), 2);
            }
        }

        // Recalculate age in months
        if ($growthMeasurement->child) {
            $validated['age_in_months'] = $growthMeasurement->child->age_in_months;
        }

        $growthMeasurement->update($validated);

        return response()->json(['success' => true, 'message' => 'Measurement updated successfully.', 'measurement' => $growthMeasurement]);
    }

    /**
     * Delete a measurement
     */
    public function destroy($id)
    {
        $growthMeasurement = GrowthMeasurement::findOrFail($id);
        $growthMeasurement->delete();
        return response()->json(['success' => true, 'message' => 'Measurement deleted successfully.']);
    }
}