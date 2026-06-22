<?php

namespace App\Http\Controllers;

use App\Models\GrowthMeasurement;
use App\Models\Child;
use App\Services\WHOGrowthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrowthMeasurementController extends Controller
{
    protected $whoGrowthService;

    public function __construct(WHOGrowthService $whoGrowthService)
    {
        $this->whoGrowthService = $whoGrowthService;
    }

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
     * Check if the current user can access a child's data (cross-role visibility).
     */
    private function canAccessChild(Child $child): bool
    {
        $user = Auth::user();
        
        // Admin, nurse, doctor can access any child (cross-role visibility)
        if ($user->isAdmin() || $user->isNurse() || $user->isDoctor()) {
            return true;
        }
        
        // Parents/guardians can only access their own children
        return $child->user_id === $user->id;
    }

    /**
     * Apply child visibility scope to a query.
     */
    private function applyChildVisibility($query, $user): void
    {
        // Admin, nurse, doctor see ALL children; parents/guardians see only theirs
        if ($user->isAdmin() || $user->isNurse() || $user->isDoctor()) {
            return; // No restriction - see all
        }
        
        $query->where('user_id', $user->id);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = GrowthMeasurement::with('child')
            ->whereHas('child', function($q) use ($user) {
                $this->applyChildVisibility($q, $user);
            });

        if ($request->has('child_id')) {
            $query->where('child_id', $request->child_id);
        }

        if ($request->has('from_date')) {
            $query->fromDate($request->from_date);
        }

        if ($request->has('to_date')) {
            $query->untilDate($request->to_date);
        }

        if ($request->has('abnormal')) {
            $query->withAbnormalZScores();
        }

        $measurements = $query->latest('measurement_date')->paginate(15);

        return view('growth-measurements.index', compact('measurements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        $child = null;
        if ($request->has('child_id')) {
            $child = Child::where('id', $request->child_id);
            if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
                $child->where('user_id', $user->id);
            }
            $child = $child->first();
        }

        // Admin, nurse, doctor see all active children; parents/guardians see only theirs
        $childrenQuery = Child::active();
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            $childrenQuery->where('user_id', $user->id);
        }
        $children = $childrenQuery->get();

        return view('growth-measurements.create', compact('children', 'child'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'measurement_date' => 'required|date|before_or_equal:today',
            'weight' => 'nullable|numeric|min:0|max:50',
            'height' => 'nullable|numeric|min:30|max:200',
            'head_circumference' => 'nullable|numeric|min:20|max:70',
            'mid_upper_arm_circumference' => 'nullable|numeric|min:5|max:40',
            'temperature' => 'nullable|numeric|max:42',
            'clinical_notes' => 'nullable|string',
            'is_from_device' => 'boolean',
            'device_id' => 'nullable|string|max:255',
        ]);

        // Check authorization with cross-role visibility
        $childQuery = Child::where('id', $validated['child_id']);
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            $childQuery->where('user_id', $user->id);
        }
        $child = $childQuery->firstOrFail();

        if (!$this->hasAnyMeasurementValue($validated)) {
            return back()->withErrors([
                'measurement' => 'Please provide at least one measurement value (weight, height, head circumference, MUAC, or temperature).'
            ])->withInput();
        }

        $validated['user_id'] = $user->id;
        $validated['age_in_months'] = $this->whoGrowthService->calculateAgeInMonths($child->date_of_birth);

        $measurement = new GrowthMeasurement($validated);
        
        // Calculate Z-scores
        $zScoreResult = $this->whoGrowthService->calculateZScores($measurement, $child);
        
        if ($zScoreResult['success']) {
            try {
                $measurement->save();
                
                return redirect()->route('children.show', $child)
                    ->with('success', 'Growth measurement recorded successfully.');
            } catch (\Exception $e) {
                return back()->withErrors([
                    'error' => 'Failed to save measurement: ' . $e->getMessage()
                ])->withInput();
            }
        }

        return back()->withErrors(['error' => $zScoreResult['message']])
            ->withInput();
    }

    /**
     * Store measurement for a specific child (API endpoint for device integration)
     */
    public function storeForChild(Child $child, Request $request)
    {
        // Check authorization with cross-role visibility
        if (!$this->canAccessChild($child)) {
            abort(403);
        }

        $validated = $request->validate([
            'measurement_date' => 'required|date|before_or_equal:today',
            'weight' => 'nullable|numeric|min:0|max:50',
            'height' => 'nullable|numeric|min:30|max:200',
            'head_circumference' => 'nullable|numeric|min:20|max:70',
            'mid_upper_arm_circumference' => 'nullable|numeric|min:5|max:40',
            'temperature' => 'nullable|numeric|max:42',
            'clinical_notes' => 'nullable|string',
            'is_from_device' => 'boolean',
            'device_id' => 'nullable|string|max:255',
        ]);

        if (!$this->hasAnyMeasurementValue($validated)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Please provide at least one measurement value (weight, height, head circumference, MUAC, or temperature).'
                ], 422);
            }

            return back()->withErrors([
                'measurement' => 'Please provide at least one measurement value (weight, height, head circumference, MUAC, or temperature).'
            ])->withInput();
        }

        $validated['user_id'] = Auth::id();
        $validated['child_id'] = $child->id;
        $validated['age_in_months'] = $this->whoGrowthService->calculateAgeInMonths($child->date_of_birth);

        $measurement = new GrowthMeasurement($validated);
        $zScoreResult = $this->whoGrowthService->calculateZScores($measurement, $child);

        if ($zScoreResult['success']) {
            $measurement->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Measurement recorded successfully',
                    'measurement' => $measurement,
                    'z_scores' => $zScoreResult,
                ]);
            }

            return redirect()->route('children.show', $child)
                ->with('success', 'Growth measurement recorded successfully.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $zScoreResult['message'],
            ], 422);
        }

        return back()->withErrors(['error' => $zScoreResult['message']])
            ->withInput();
    }

    /**
     * Display measurements for a specific child
     */
    public function indexForChild(Child $child, Request $request)
    {
        // Check authorization with cross-role visibility
        if (!$this->canAccessChild($child)) {
            abort(403);
        }

        $measurements = $child->growthMeasurements()
            ->latest('measurement_date')
            ->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'child' => $child,
                'measurements' => $measurements,
            ]);
        }

        return view('growth-measurements.index', compact('measurements', 'child'));
    }

    /**
     * Display the specified resource.
     */
    public function show(GrowthMeasurement $growthMeasurement)
    {
        // Check authorization with cross-role visibility
        if (!$this->canAccessChild($growthMeasurement->child)) {
            abort(403);
        }

        return view('growth-measurements.show', compact('growthMeasurement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GrowthMeasurement $growthMeasurement)
    {
        // Check authorization with cross-role visibility
        if (!$this->canAccessChild($growthMeasurement->child)) {
            abort(403);
        }

        $user = Auth::user();
        $childrenQuery = Child::active();
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            $childrenQuery->where('user_id', $user->id);
        }
        $children = $childrenQuery->get();

        return view('growth-measurements.edit', compact('growthMeasurement', 'children'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GrowthMeasurement $growthMeasurement)
    {
        // Check authorization with cross-role visibility
        if (!$this->canAccessChild($growthMeasurement->child)) {
            abort(403);
        }

        $validated = $request->validate([
            'measurement_date' => 'required|date|before_or_equal:today',
            'weight' => 'nullable|numeric|min:0|max:50',
            'height' => 'nullable|numeric|min:30|max:200',
            'head_circumference' => 'nullable|numeric|min:20|max:70',
            'mid_upper_arm_circumference' => 'nullable|numeric|min:5|max:40',
            'temperature' => 'nullable|numeric|max:42',
            'clinical_notes' => 'nullable|string',
        ]);

        $growthMeasurement->update($validated);

        // Recalculate Z-scores
        $zScoreResult = $this->whoGrowthService->calculateZScores($growthMeasurement, $growthMeasurement->child);
        if ($zScoreResult['success']) {
            $growthMeasurement->save();
        }

        return redirect()->route('growth-measurements.show', $growthMeasurement)
            ->with('success', 'Growth measurement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GrowthMeasurement $growthMeasurement)
    {
        // Check authorization with cross-role visibility
        if (!$this->canAccessChild($growthMeasurement->child)) {
            abort(403);
        }

        $child = $growthMeasurement->child;
        $growthMeasurement->delete();

        return redirect()->route('children.show', $child)
            ->with('success', 'Growth measurement deleted successfully.');
    }
}