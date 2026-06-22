<?php

namespace App\Http\Controllers;

use App\Models\Immunization;
use App\Models\ImmunizationSchedule;
use App\Models\Child;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImmunizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Immunization::with(['child', 'immunizationSchedule'])
            ->whereHas('child', function($q) {
                $q->where('user_id', Auth::id());
            });

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('vaccine_name')) {
            $query->where('vaccine_name', $request->vaccine_name);
        }

        if ($request->has('child_id')) {
            $query->where('child_id', $request->child_id);
        }

        $immunizations = $query->latest('next_due_date')->paginate(15);

        return view('immunizations.index', compact('immunizations'));
    }

    /**
     * Show upcoming immunizations
     */
    public function upcoming()
    {
        $user = Auth::user();
        
        $query = Immunization::with(['child', 'immunizationSchedule'])
            ->scheduled()
            ->upcoming()
            ->orderBy('next_due_date');

        // Healthcare workers (admin, nurse, doctor) see ALL children's upcoming vaccinations
        // Parents/guardians see only their own children's
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            $query->whereHas('child', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $immunizations = $query->get();

        return view('immunizations.upcoming', compact('immunizations'));
    }

    /**
     * Show overdue immunizations
     */
    public function overdue()
    {
        $user = Auth::user();
        
        $query = Immunization::with(['child', 'immunizationSchedule'])
            ->overdue()
            ->orderBy('next_due_date');

        // Healthcare workers (admin, nurse, doctor) see ALL children's overdue vaccinations
        // Parents/guardians see only their own children's
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            $query->whereHas('child', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $immunizations = $query->get();

        return view('immunizations.overdue', compact('immunizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $child = null;
        if ($request->has('child_id')) {
            $child = Child::where('id', $request->child_id)
                ->where('user_id', Auth::id())
                ->first();
        }

        $children = Child::where('user_id', Auth::id())->active()->get();
        $schedules = ImmunizationSchedule::active()->ordered()->get();

        return view('immunizations.create', compact('children', 'child', 'schedules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $vaccineName = $request->vaccine_name;
        $vaccineType = $request->vaccine_type;

        // Handle custom vaccine name (when user selects "Other" and types a custom name)
        if ($vaccineName === '__custom__' && $request->filled('vaccine_name_custom')) {
            $vaccineName = $request->vaccine_name_custom;
        }

        // Handle custom vaccine type
        if ($vaccineType === '__custom__' && $request->filled('vaccine_type_custom')) {
            $vaccineType = $request->vaccine_type_custom;
        }

        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'immunization_schedule_id' => 'nullable|exists:immunization_schedules,id',
            'batch_number' => 'nullable|string|max:255',
            'date_administered' => 'nullable|date|before_or_equal:today',
            'next_due_date' => 'nullable|date|after:today',
            'status' => 'required|in:scheduled,administered,missed,cancelled',
            'site' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'dose_volume' => 'nullable|numeric|min:0',
            'adverse_reactions' => 'nullable|string',
            'notes' => 'nullable|string',
            'health_facility' => 'nullable|string|max:255',
            'health_worker_name' => 'nullable|string|max:255',
        ]);

        $validated['vaccine_name'] = $vaccineName;
        $validated['vaccine_type'] = $vaccineType;

        // If status is administered but date_administered is null, default to today
        if ($validated['status'] === 'administered' && empty($validated['date_administered'])) {
            $validated['date_administered'] = now()->format('Y-m-d');
        }

        // Check authorization - healthcare workers can record for any child (cross-role)
        $childQuery = Child::where('id', $validated['child_id']);
        if (!($user->isAdmin() || $user->isNurse() || $user->isDoctor())) {
            $childQuery->where('user_id', $user->id);
        }
        $child = $childQuery->firstOrFail();

        $validated['user_id'] = Auth::id();

        $immunization = Immunization::create($validated);

        return redirect()->route('children.immunizations', $child)
            ->with('success', 'Immunization record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Immunization $immunization)
    {
        // Check authorization
        if ($immunization->child->user_id !== Auth::id()) {
            abort(403);
        }

        return view('immunizations.show', compact('immunization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Immunization $immunization)
    {
        // Check authorization
        if ($immunization->child->user_id !== Auth::id()) {
            abort(403);
        }

        $children = Child::where('user_id', Auth::id())->active()->get();
        $schedules = ImmunizationSchedule::active()->ordered()->get();

        return view('immunizations.edit', compact('immunization', 'children', 'schedules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Immunization $immunization)
    {
        // Check authorization
        if ($immunization->child->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'date_administered' => 'nullable|date|before_or_equal:today',
            'next_due_date' => 'nullable|date',
            'status' => 'required|in:scheduled,administered,missed,cancelled',
            'site' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'dose_volume' => 'nullable|numeric|min:0',
            'adverse_reactions' => 'nullable|string',
            'notes' => 'nullable|string',
            'health_facility' => 'nullable|string|max:255',
            'health_worker_name' => 'nullable|string|max:255',
        ]);

        $immunization->update($validated);

        return redirect()->route('immunizations.show', $immunization)
            ->with('success', 'Immunization record updated successfully.');
    }

    /**
     * Mark an immunization as administered
     */
    public function administer(Immunization $immunization, Request $request)
    {
        // Check authorization
        if ($immunization->child->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'date_administered' => 'required|date|before_or_equal:today',
            'site' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'health_worker_name' => 'nullable|string|max:255',
            'health_facility' => 'nullable|string|max:255',
            'adverse_reactions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $immunization->update([
            'status' => 'administered',
            'date_administered' => $validated['date_administered'],
            'site' => $validated['site'] ?? $immunization->site,
            'batch_number' => $validated['batch_number'] ?? $immunization->batch_number,
            'health_worker_name' => $validated['health_worker_name'] ?? Auth::user()->name,
            'health_facility' => $validated['health_facility'] ?? $immunization->health_facility,
            'adverse_reactions' => $validated['adverse_reactions'] ?? $immunization->adverse_reactions,
            'notes' => $validated['notes'] ?? $immunization->notes,
        ]);

        return redirect()->route('children.immunizations', $immunization->child)
            ->with('success', 'Immunization marked as administered.');
    }

    /**
     * Generate immunization schedule for a child
     */
    public function generateSchedule(Child $child, Request $request)
    {
        // Check authorization
        if ($child->user_id !== Auth::id()) {
            abort(403);
        }

        $dob = Carbon::parse($child->date_of_birth);
        $schedules = ImmunizationSchedule::active()->ordered()->get();
        $existingImmunizations = $child->immunizations;

        DB::beginTransaction();
        try {
            $created = 0;
            foreach ($schedules as $schedule) {
                // Check if this immunization already exists
                $exists = $existingImmunizations->first(function($imm) use ($schedule) {
                    return $imm->vaccine_name === $schedule->vaccine_name && 
                           $imm->immunization_schedule_id === $schedule->id;
                });

                if ($exists) {
                    continue;
                }

                // Calculate due date based on child's date of birth
                $dueDate = $dob->copy();
                if ($schedule->due_age_weeks !== null && $schedule->due_age_weeks > 0) {
                    $dueDate->addWeeks($schedule->due_age_weeks);
                } elseif ($schedule->due_age_months !== null && $schedule->due_age_months > 0) {
                    $dueDate->addMonths($schedule->due_age_months);
                }

                // Only create if due date is in the future or today
                if ($dueDate->isFuture() || $dueDate->isToday()) {
                    Immunization::create([
                        'child_id' => $child->id,
                        'user_id' => Auth::id(),
                        'immunization_schedule_id' => $schedule->id,
                        'vaccine_name' => $schedule->vaccine_name,
                        'vaccine_type' => $schedule->vaccine_type,
                        'route' => $schedule->route,
                        'dose_volume' => $schedule->dose_volume,
                        'next_due_date' => $dueDate,
                        'status' => 'scheduled',
                    ]);
                    $created++;
                }
            }

            DB::commit();

            return redirect()->route('children.immunizations', $child)
                ->with('success', "{$created} immunization schedules generated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('children.immunizations', $child)
                ->with('error', 'Failed to generate immunization schedule: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Immunization $immunization)
    {
        // Check authorization
        if ($immunization->child->user_id !== Auth::id()) {
            abort(403);
        }

        $child = $immunization->child;
        $immunization->delete();

        return redirect()->route('children.immunizations', $child)
            ->with('success', 'Immunization record deleted successfully.');
    }
}