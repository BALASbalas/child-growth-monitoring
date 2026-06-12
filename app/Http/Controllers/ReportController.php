<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\GrowthMeasurement;
use App\Models\Immunization;
use App\Models\User;
use App\Services\WHOGrowthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $whoGrowthService;

    public function __construct(WHOGrowthService $whoGrowthService)
    {
        $this->whoGrowthService = $whoGrowthService;
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
     * Growth report page
     */
    public function growthReport(Request $request)
    {
        $user = Auth::user();

        // Admin, nurse, doctor see ALL children; parents/guardians see only theirs
        if ($user->isAdmin() || $user->isNurse() || $user->isDoctor()) {
            $query = Child::active();
        } else {
            $query = Child::where('user_id', Auth::id())->active();
        }

        if ($request->has('sex')) {
            $query->where('sex', $request->sex);
        }

        if ($request->has('nutritional_status')) {
            $childIds = GrowthMeasurement::select('child_id')
                ->whereIn('nutritional_status', [$request->nutritional_status])
                ->pluck('child_id');
            $query->whereIn('id', $childIds);
        }

        $children = $query->with('growthMeasurements')->get();

        $childrenByStatus = [
            'severe_underweight' => [],
            'moderate_underweight' => [],
            'normal' => [],
            'overweight' => [],
            'obese' => [],
            'no_data' => [],
        ];

        foreach ($children as $child) {
            $latestMeasurement = $child->growthMeasurements->sortByDesc('measurement_date')->first();

            if ($latestMeasurement && $latestMeasurement->nutritional_status) {
                $status = $latestMeasurement->nutritional_status;
                $childrenByStatus[$status][] = $child;
            } else {
                $childrenByStatus['no_data'][] = $child;
            }
        }

        $statistics = [
            'total_children' => $children->count(),
            'total_measurements' => $children->sum(function($c) { return $c->growthMeasurements->count(); }),
            'children_with_abnormal' => GrowthMeasurement::withAbnormalZScores()
                ->when(!($user->isAdmin() || $user->isNurse() || $user->isDoctor()), function($q) {
                    $q->whereHas('child', function($sq) { $sq->where('user_id', Auth::id()); });
                })
                ->distinct('child_id')
                ->count('child_id'),
        ];

        return view('reports.growth', compact('childrenByStatus', 'statistics'));
    }

    /**
     * Admin: Users report page
     */
    public function usersReport(Request $request)
    {
        $query = \App\Models\User::withCount(['children' => function($q) { $q->active(); }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->latest()->paginate(15);

        $totalByRole = [
            'admin' => \App\Models\User::where('role', 'admin')->count(),
            'nurse' => \App\Models\User::where('role', 'nurse')->count(),
            'doctor' => \App\Models\User::where('role', 'doctor')->count(),
            'parent' => \App\Models\User::where('role', 'parent')->count(),
            'guardian' => \App\Models\User::where('role', 'guardian')->count(),
        ];

        return view('admin.users', compact('users', 'totalByRole'));
    }

    /**
     * Delete a user account (admin only).
     */
    public function destroyUser(User $user, Request $request)
    {
        if ($user->id === Auth::id()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 422);
            }
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        }

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle active/inactive status for a user account.
     */
    public function toggleUserStatus(User $user, Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403, 'Only admin users may change account status.');

        if ($user->id === Auth::id()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot deactivate your own account.'], 422);
            }
            return redirect()->route('admin.users')->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => "User account has been {$status}.", 'is_active' => $user->is_active]);
        }

        return redirect()->route('admin.users')->with('success', "User account has been {$status}.");
    }

    /**
     * Admin: System-wide report
     */
    public function systemReport()
    {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_children' => Child::active()->count(),
            'total_measurements' => GrowthMeasurement::count(),
            'total_immunizations' => Immunization::count(),
            'total_nurses' => \App\Models\User::where('role', 'nurse')->count(),
            'total_doctors' => \App\Models\User::where('role', 'doctor')->count(),
            'total_parents' => \App\Models\User::where('role', 'parent')->count(),
            'total_guardians' => \App\Models\User::where('role', 'guardian')->count(),
            'overdue_vaccines' => Immunization::overdue()->count(),
            'upcoming_vaccines' => Immunization::scheduled()->upcoming()->count(),
            'children_by_gender' => [
                'male' => Child::active()->male()->count(),
                'female' => Child::active()->female()->count(),
            ],
            'recent_registrations' => Child::active()->where('created_at', '>=', Carbon::now()->subMonth())->count(),
            'recent_measurements' => GrowthMeasurement::where('measurement_date', '>=', Carbon::now()->subMonth())->count(),
        ];

        return view('admin.system-report', compact('stats'));
    }

    /**
     * Admin: Export all data
     */
    public function exportAll()
    {
        $allData = [
            'users' => \App\Models\User::all(),
            'children' => Child::with('growthMeasurements', 'immunizations')->active()->get(),
            'generated_at' => now()->toDateTimeString(),
        ];

        return response()->json($allData);
    }

    /**
     * Immunization report page
     */
    public function immunizationReport(Request $request)
    {
        $user = Auth::user();

        $query = Immunization::with(['child', 'immunizationSchedule']);

        // Admin, nurse, doctor see ALL immunizations; parents/guardians see only theirs
        if ($user->isAdmin() || $user->isNurse() || $user->isDoctor()) {
            // See all
        } else {
            $query->whereHas('child', function($q) {
                $q->where('user_id', Auth::id());
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('vaccine_name')) {
            $query->where('vaccine_name', $request->vaccine_name);
        }

        $immunizations = $query->get();

        $statistics = [
            'total_scheduled' => $immunizations->where('status', 'scheduled')->count(),
            'total_administered' => $immunizations->where('status', 'administered')->count(),
            'total_missed' => $immunizations->where('status', 'missed')->count(),
            'total_overdue' => $immunizations->filter(function($imm) {
                return $imm->is_overdue;
            })->count(),
        ];

        // Group by vaccine type
        $byVaccine = $immunizations->groupBy('vaccine_name')->map(function($group) {
            return [
                'total' => $group->count(),
                'administered' => $group->where('status', 'administered')->count(),
                'scheduled' => $group->where('status', 'scheduled')->count(),
                'overdue' => $group->filter(function($imm) {
                    return $imm->is_overdue;
                })->count(),
            ];
        });

        return view('reports.immunization', compact('immunizations', 'statistics', 'byVaccine'));
    }

    /**
     * Export child report as JSON/PDF
     */
    public function exportChildReport(Child $child, Request $request)
    {
        // Check authorization with cross-role visibility
        if (!$this->canAccessChild($child)) {
            abort(403);
        }

        $child->load(['growthMeasurements', 'immunizations']);

        $report = [
            'child_info' => [
                'name' => $child->full_name,
                'unique_id' => $child->unique_id,
                'date_of_birth' => $child->date_of_birth,
                'sex' => $child->sex,
                'age_months' => $child->age_in_months,
                'mother_name' => $child->mother_name,
                'father_name' => $child->father_name,
                'guardian_phone' => $child->guardian_phone,
                'medical_history' => $child->medical_history,
            ],
            'growth_history' => $child->growthMeasurements->map(function($measurement) {
                return [
                    'date' => $measurement->measurement_date,
                    'age_months' => $measurement->age_in_months,
                    'weight' => $measurement->weight_kg,
                    'height' => $measurement->height_cm,
                    'head_circumference' => $measurement->head_circumference,
                    'muac' => $measurement->mid_upper_arm_circumference,
                    'waz' => $measurement->weight_for_age_zscore,
                    'haz' => $measurement->height_for_age_zscore,
                    'whz' => $measurement->weight_for_height_zscore,
                    'bmi' => $measurement->bmi,
                    'nutritional_status' => $measurement->nutritional_status,
                    'stunting_status' => $measurement->stunting_status,
                    'wasting_status' => $measurement->wasting_status,
                    'notes' => $measurement->clinical_notes,
                ];
            }),
            'immunization_history' => $child->immunizations->map(function($imm) {
                return [
                    'vaccine' => $imm->vaccine_name,
                    'type' => $imm->vaccine_type,
                    'due_date' => $imm->next_due_date,
                    'administered_date' => $imm->date_administered,
                    'status' => $imm->status_label,
                    'batch_number' => $imm->batch_number,
                    'site' => $imm->site,
                    'route' => $imm->route,
                    'adverse_reactions' => $imm->adverse_reactions,
                ];
            }),
            'alerts' => $this->whoGrowthService->checkAlarmingPatterns($child),
            'generated_at' => now()->toDateTimeString(),
        ];

        if ($request->expectsJson()) {
            return response()->json($report);
        }

        // For PDF export, we'll return a view that can be printed
        return view('reports.child-export', compact('child', 'report'));
    }

    /**
     * Statistics dashboard - database-agnostic (works with MySQL, PostgreSQL, SQLite)
     */
    public function statistics()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Build child query with cross-role visibility
        $childQuery = function($q) use ($user, $userId) {
            if ($user->isAdmin() || $user->isNurse() || $user->isDoctor()) {
                // See all children
                return;
            }
            $q->where('user_id', $userId);
        };

        // Children statistics
        $totalChildren = Child::active()->where($childQuery)->count();
        $maleChildren = Child::active()->male()->where($childQuery)->count();
        $femaleChildren = Child::active()->female()->where($childQuery)->count();

        // Age distribution using database-agnostic Carbon-based calculation
        // We fetch all children and group in PHP to avoid DB-specific date functions
        $children = Child::active()->where($childQuery)->get();
        $ageGroups = [];
        foreach ($children as $child) {
            $ageMonths = $child->age_in_months;
            if ($ageMonths === null) continue;

            if ($ageMonths < 6) {
                $group = '0-6 months';
            } elseif ($ageMonths < 12) {
                $group = '6-12 months';
            } elseif ($ageMonths < 24) {
                $group = '1-2 years';
            } elseif ($ageMonths < 60) {
                $group = '2-5 years';
            } else {
                $group = '5+ years';
            }

            $ageGroups[$group] = ($ageGroups[$group] ?? 0) + 1;
        }

        $ageDistribution = collect($ageGroups)->map(function($count, $group) {
            return (object) ['age_group' => $group, 'count' => $count];
        })->values();

        // Growth measurements statistics
        $recentMeasurements = GrowthMeasurement::where('measurement_date', '>=', Carbon::now()->subMonths(6))
            ->whereHas('child', $childQuery)
            ->get();

        $measurementsByMonth = GrowthMeasurement::where('measurement_date', '>=', Carbon::now()->subMonths(6))
            ->whereHas('child', $childQuery)
            ->get()
            ->groupBy(function($m) {
                return Carbon::parse($m->measurement_date)->format('Y-m');
            })
            ->map(function($items, $month) {
                return (object) ['month' => $month, 'count' => $items->count()];
            })
            ->sortBy('month')
            ->values();

        // Nutritional status distribution (from latest measurements)
        $latestMeasurements = GrowthMeasurement::select(DB::raw('MAX(id) as id'))
            ->whereHas('child', $childQuery)
            ->groupBy('child_id')
            ->pluck('id');

        $nutritionalStatusDist = GrowthMeasurement::whereIn('id', $latestMeasurements)
            ->selectRaw('nutritional_status, COUNT(*) as count')
            ->groupBy('nutritional_status')
            ->get();

        // Immunization statistics
        $totalImmunizations = Immunization::whereHas('child', $childQuery)->count();

        $immunizationStatus = Immunization::whereHas('child', $childQuery)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $overdueImmunizations = Immunization::whereHas('child', $childQuery)->overdue()->count();

        return view('reports.statistics', compact(
            'totalChildren',
            'maleChildren',
            'femaleChildren',
            'ageDistribution',
            'recentMeasurements',
            'measurementsByMonth',
            'nutritionalStatusDist',
            'totalImmunizations',
            'immunizationStatus',
            'overdueImmunizations'
        ));
    }
}