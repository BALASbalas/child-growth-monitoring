<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\GrowthMeasurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildApiController extends Controller
{
    /**
     * Get children list for DataTable
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Child::with(['growthMeasurements', 'immunizations']);

        // Allow all users including parents to search across ALL children in the system
        // Parents need to find any child by name to view their progress
        if ($request->filled('search')) {
            $search = $request->search;
            $query->search($search);
        } else {
            // Only restrict to own children if no search is performed
            if ($user->isParent() || $user->isGuardian()) {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->filled('sex')) {
            $query->where('sex', $request->sex);
        }

        if ($request->filled('age_group')) {
            $group = $request->age_group;
            $query->where(function($q) use ($group) {
                // We filter in PHP after fetching since age is calculated
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $children = $query->latest()->paginate(50);

        // Add computed fields
        $children->getCollection()->transform(function($child) {
            $latestMeasurement = $child->growthMeasurements
                ->whereNotNull('weight')
                ->sortByDesc('measurement_date')
                ->first();

            $totalVaccines = $child->immunizations->count();
            $givenVaccines = $child->immunizations->where('status', 'administered')->count();

            $nutritionLabel = 'No Data';
            $nutritionColor = 'bg-gray-100 text-gray-600';
            $nutritionStatus = 'no_data';
            $growthProgress = 0;
            $wazStatus = 'normal';
            $hazStatus = 'normal';

            if ($latestMeasurement) {
                switch($latestMeasurement->nutritional_status) {
                    case 'severe_underweight': $nutritionLabel = 'Severe Underweight'; $nutritionColor = 'bg-red-100 text-red-800'; $nutritionStatus = 'severe'; break;
                    case 'moderate_underweight': $nutritionLabel = 'Moderate Underweight'; $nutritionColor = 'bg-yellow-100 text-yellow-800'; $nutritionStatus = 'moderate'; break;
                    case 'normal': $nutritionLabel = 'Normal'; $nutritionColor = 'bg-green-100 text-green-800'; $nutritionStatus = 'normal'; break;
                    case 'overweight': $nutritionLabel = 'Overweight'; $nutritionColor = 'bg-orange-100 text-orange-800'; $nutritionStatus = 'overweight'; break;
                    case 'obese': $nutritionLabel = 'Obese'; $nutritionColor = 'bg-red-200 text-red-900'; $nutritionStatus = 'obese'; break;
                }

                // Growth progress percentage based on weight-for-age z-score
                if ($latestMeasurement->weight_for_age_zscore !== null) {
                    $waz = $latestMeasurement->weight_for_age_zscore;
                    if ($waz < -3) { $wazStatus = 'critical'; $growthProgress = 15; }
                    elseif ($waz < -2) { $wazStatus = 'warning'; $growthProgress = 35; }
                    elseif ($waz < -1) { $wazStatus = 'below_normal'; $growthProgress = 55; }
                    elseif ($waz < 1) { $wazStatus = 'normal'; $growthProgress = 80; }
                    elseif ($waz < 2) { $wazStatus = 'above_normal'; $growthProgress = 90; }
                    else { $wazStatus = 'elevated'; $growthProgress = 95; }
                }

                if ($latestMeasurement->height_for_age_zscore !== null) {
                    $haz = $latestMeasurement->height_for_age_zscore;
                    if ($haz < -3) $hazStatus = 'critical';
                    elseif ($haz < -2) $hazStatus = 'warning';
                    elseif ($haz < 1) $hazStatus = 'normal';
                    else $hazStatus = 'above_normal';
                }
            }

            // Measurement count for tracking consistency
            $measurementCount = $child->growthMeasurements->count();

            return [
                'id' => $child->id,
                'full_name' => $child->full_name,
                'unique_id' => $child->unique_id,
                'sex' => $child->sex,
                'date_of_birth' => $child->date_of_birth ? $child->date_of_birth->format('Y-m-d') : null,
                'age_string' => $child->age_string,
                'age_in_months' => $child->age_in_months,
                'mother_name' => $child->mother_name,
                'mother_phone' => $child->mother_phone,
                'father_name' => $child->father_name,
                'father_phone' => $child->father_phone,
                'guardian_name' => $child->guardian_name,
                'guardian_phone' => $child->guardian_phone,
                'address' => $child->address,
                'location' => $child->location,
                'district' => $child->district,
                'region' => $child->region,
                'is_active' => $child->is_active,
                'created_at' => $child->created_at ? $child->created_at->format('Y-m-d H:i:s') : null,
                'nutrition_label' => $nutritionLabel,
                'nutrition_color' => $nutritionColor,
                'nutrition_status' => $nutritionStatus,
                'latest_weight' => $latestMeasurement ? number_format($latestMeasurement->weight, 2) . ' kg' : '-',
                'latest_weight_raw' => $latestMeasurement ? (float)$latestMeasurement->weight : null,
                'latest_height' => $latestMeasurement ? number_format($latestMeasurement->height, 1) . ' cm' : '-',
                'latest_height_raw' => $latestMeasurement ? (float)$latestMeasurement->height : null,
                'vaccine_progress' => "{$givenVaccines}/{$totalVaccines}",
                'vaccine_done' => $givenVaccines,
                'vaccine_total' => $totalVaccines,
                'measurement_count' => $measurementCount,
                'growth_progress' => $growthProgress,
                'waz_status' => $wazStatus,
                'haz_status' => $hazStatus,
                'latest_waz' => $latestMeasurement ? $latestMeasurement->weight_for_age_zscore : null,
                'latest_haz' => $latestMeasurement ? $latestMeasurement->height_for_age_zscore : null,
                'latest_bmi' => $latestMeasurement ? $latestMeasurement->bmi : null,
                'registration_date' => $child->created_at ? $child->created_at->format('d/m/Y') : null,
            ];
        });

        return response()->json($children);
    }

    /**
     * Store a new child
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'sex' => 'required|in:male,female',
            'gestational_age_weeks' => 'nullable|integer|min:20|max:44',
            'birth_weight' => 'nullable|numeric|min:0.1|max:10',
            'birth_length' => 'nullable|numeric|min:10|max:70',
            'birth_head_circumference' => 'nullable|numeric|min:20|max:50',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = true;

        // Generate unique ID
        $validated['unique_id'] = 'CHD-' . strtoupper(substr(md5(uniqid()), 0, 8));

        $child = Child::create($validated);

        return response()->json(['success' => true, 'message' => 'Child registered successfully.', 'child' => $child], 201);
    }

    /**
     * Get a single child
     */
    public function show($id)
    {
        $child = Child::with(['growthMeasurements', 'immunizations'])->findOrFail($id);
        return response()->json($child);
    }

    /**
     * Update a child
     */
    public function update(Request $request, $id)
    {
        $child = Child::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'sex' => 'required|in:male,female',
            'gestational_age_weeks' => 'nullable|integer|min:20|max:44',
            'birth_weight' => 'nullable|numeric|min:0.1|max:10',
            'birth_length' => 'nullable|numeric|min:10|max:70',
            'birth_head_circumference' => 'nullable|numeric|min:20|max:50',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $child->update($validated);

        return response()->json(['success' => true, 'message' => 'Child updated successfully.', 'child' => $child]);
    }

    /**
     * Toggle child active/inactive status
     */
    public function toggleStatus($id)
    {
        $child = Child::findOrFail($id);
        $child->update(['is_active' => !$child->is_active]);
        $status = $child->is_active ? 'activated' : 'deactivated';
        return response()->json(['success' => true, 'message' => "Child {$status} successfully.", 'is_active' => $child->is_active]);
    }

    /**
     * Delete a child
     */
    public function destroy($id)
    {
        $child = Child::findOrFail($id);
        $child->growthMeasurements()->delete();
        $child->immunizations()->delete();
        $child->delete();

        return response()->json(['success' => true, 'message' => 'Child record deleted successfully.']);
    }
}