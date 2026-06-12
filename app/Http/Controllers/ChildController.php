<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Services\WHOGrowthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChildController extends Controller
{
    protected $whoGrowthService;

    public function __construct(WHOGrowthService $whoGrowthService)
    {
        $this->whoGrowthService = $whoGrowthService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user && ($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            $query = Child::query();
        } else {
            $query = Child::where('user_id', $user?->id);
        }

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($sex = $request->get('sex')) {
            $query->where('sex', $sex);
        }

        $children = $query->with(['growthMeasurements', 'immunizations'])->latest()->paginate(15)->withQueryString();

        return view('children.index', compact('children'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user || !($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            abort(403, 'Only healthcare workers can register children.');
        }

        return view('children.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || !($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            abort(403, 'Only healthcare workers can register children.');
        }

        Log::info('ChildController@store called', ['user_id' => $user->id]);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'unique_id' => 'nullable|string|max:255|unique:children,unique_id',
            'date_of_birth' => 'required|date|before:today',
            'sex' => 'required|in:male,female',
            'gestational_age_weeks' => 'nullable|integer|min:20|max:45',
            'birth_weight' => 'nullable|numeric|min:0.1|max:10',
            'birth_length' => 'nullable|numeric|min:30|max:70',
            'birth_head_circumference' => 'nullable|numeric|min:25|max:50',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:50',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:50',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['unique_id'])) {
            $validated['unique_id'] = 'CHD-' . strtoupper(Str::random(8));
        }

        $validated['user_id'] = $user->id;

        $child = Child::create($validated);

        Log::info('Child created', ['child_id' => $child->id, 'user_id' => $user->id]);

        return redirect()->route('children.show', $child)->with('success', 'Child registered successfully.');
    }
    /**
     * Display the specified child record and recent measurements.
     */
    public function show(Child $child)
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isNurse() || $user->isDoctor()) {
            // allowed
        } else {
            if ($child->user_id !== $user->id) {
                abort(403);
            }
        }

        $measurements = $child->growthMeasurements()->orderBy('measurement_date')->get();

        $latest = $measurements->last();
        $zScores = null;
        if ($latest) {
            $zScores = $this->whoGrowthService->calculateZScores($latest, $child);
        }

        $alerts = $this->whoGrowthService->checkAlarmingPatterns($child);

        $latestMeasurement = $latest;

        return view('children.show', compact('child', 'measurements', 'zScores', 'alerts', 'latestMeasurement'));
    }
    /**
     * Show the form for editing the specified resource.
     * - Admin, Doctor, Nurse: can edit any child (cross-role)
     */
    public function edit(Child $child)
    {
        $user = Auth::user();
        
        // Admin, nurse, doctor can edit any child
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            abort(403, 'Only healthcare workers can edit child records.');
        }

        return view('children.edit', compact('child'));
    }

    /**
     * Update the specified resource in storage.
     * - Admin, Doctor, Nurse: can update any child (cross-role)
     */
    public function update(Request $request, Child $child)
    {
        $user = Auth::user();
        
        // Admin, nurse, doctor can update any child
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            abort(403, 'Only healthcare workers can update child records.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'unique_id' => 'nullable|string|max:255|unique:children,unique_id,' . $child->id,
            'date_of_birth' => 'required|date|before:today',
            'sex' => 'required|in:male,female',
            'gestational_age_weeks' => 'nullable|integer|min:20|max:45',
            'birth_weight' => 'nullable|numeric|min:0.1|max:10',
            'birth_length' => 'nullable|numeric|min:30|max:70',
            'birth_head_circumference' => 'nullable|numeric|min:25|max:50',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => ['nullable', 'string', 'regex:/^(07|06)[0-9]{8}$/'],
            'father_name' => 'nullable|string|max:255',
            'father_phone' => ['nullable', 'string', 'regex:/^(07|06)[0-9]{8}$/'],
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => ['nullable', 'string', 'regex:/^(07|06)[0-9]{8}$/'],
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $child->update($validated);

        return redirect()->route('children.show', $child)
            ->with('success', 'Taarifa za mtoto zimesasishwa. (Child information updated successfully.)');
    }

    /**
     * Remove the specified resource from storage.
     * - Admin, Doctor, Nurse: can delete/deactivate any child (cross-role)
     */
    public function destroy(Child $child)
    {
        $user = Auth::user();
        
        // Admin, nurse, doctor can deactivate any child
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            abort(403, 'Only healthcare workers can deactivate child records.');
        }

        $child->update(['is_active' => false]);

        return redirect()->route('children.index')
            ->with('success', 'Taarifa za mtoto zimefutwa. (Child record deactivated successfully.)');
    }

    /**
     * Print details for a child
     */
    public function printDetails(Child $child)
    {
        $user = Auth::user();

        // Admin, nurse, doctor can view any child (cross-role)
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            if ($child->user_id !== $user->id) {
                abort(403);
            }
        }

        $latestMeasurement = $child->growthMeasurements()->latest('measurement_date')->first();

        return view('children.print', compact('child', 'latestMeasurement'));
    }

    /**
     * Show growth chart for a child
     * - Admin, Doctor, Nurse: can view any child's chart
     * - Parents/Guardians: can view only their own children's chart
     */
    public function growthChart(Child $child, Request $request)
    {
        $user = Auth::user();
        
        // Admin, nurse, doctor can view any child's chart (cross-role)
        if ($user->isAdmin() || $user->isNurse() || $user->isDoctor()) {
            // Allow access
        } else {
            // Parents/guardians only their own children
            if ($child->user_id !== $user->id) {
                abort(403);
            }
        }

        $parameter = $request->get('parameter', 'weight');
        $chartData = $this->whoGrowthService->getGrowthChartData($child, $parameter);
        $velocity = $this->whoGrowthService->calculateGrowthVelocity($child, $parameter);

        return view('children.growth-chart', compact('child', 'chartData', 'parameter', 'velocity'));
    }

    /**
     * Show immunizations for a child
     * - Admin, Doctor, Nurse: can view any child's immunizations
     * - Parents/Guardians: can view only their own children's immunizations
     */
    public function immunizations(Child $child)
    {
        $user = Auth::user();
        
        // Admin, nurse, doctor can view any child's immunizations (cross-role)
        if ($user->isAdmin() || $user->isNurse() || $user->isDoctor()) {
            // Allow access
        } else {
            // Parents/guardians only their own children
            if ($child->user_id !== $user->id) {
                abort(403);
            }
        }

        $child->load(['immunizations', 'immunizations.immunizationSchedule']);
        $upcoming = $child->immunizations()->scheduled()->upcoming()->get();
        $overdue = $child->immunizations()->overdue()->get();
        $completed = $child->immunizations()->administered()->latest('date_administered')->get();

        return view('children.immunizations', compact('child', 'upcoming', 'overdue', 'completed'));
    }
}