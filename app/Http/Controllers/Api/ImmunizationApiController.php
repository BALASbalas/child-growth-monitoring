<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Immunization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImmunizationApiController extends Controller
{
    /**
     * Get immunizations list for DataTable
     */
    public function index(Request $request)
    {
        $query = Immunization::with(['child', 'immunizationSchedule']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vaccine_name', 'like', "%{$search}%")
                  ->orWhereHas('child', function($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('unique_id', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('child_id')) {
            $query->where('child_id', $request->child_id);
        }

        $immunizations = $query->latest('next_due_date')->paginate(15);

        $immunizations->getCollection()->transform(function($imm) {
            return [
                'id' => $imm->id,
                'child_id' => $imm->child_id,
                'child_name' => $imm->child ? $imm->child->full_name : 'Unknown',
                'child_unique_id' => $imm->child ? $imm->child->unique_id : 'N/A',
                'vaccine_name' => $imm->vaccine_name,
                'vaccine_type' => $imm->vaccine_type,
                'dose_number' => $imm->dose_number,
                'status' => $imm->status,
                'status_label' => $imm->status_label,
                'next_due_date' => $imm->next_due_date ? $imm->next_due_date->format('Y-m-d') : null,
                'date_administered' => $imm->date_administered ? $imm->date_administered->format('Y-m-d') : null,
                'administered_by' => $imm->administered_by,
                'batch_number' => $imm->batch_number,
                'site' => $imm->site,
                'route' => $imm->route,
                'adverse_reactions' => $imm->adverse_reactions,
                'is_overdue' => $imm->is_overdue,
                'created_at' => $imm->created_at ? $imm->created_at->format('Y-m-d H:i:s') : null,
            ];
        });

        return response()->json($immunizations);
    }

    /**
     * Store a new immunization
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'nullable|string|max:255',
            'dose_number' => 'nullable|integer|min:1',
            'next_due_date' => 'nullable|date',
            'date_administered' => 'nullable|date',
            'batch_number' => 'nullable|string|max:255',
            'site' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'adverse_reactions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = $request->filled('date_administered') ? 'administered' : 'scheduled';
        $validated['administered_by'] = $validated['status'] === 'administered' ? Auth::user()->name : null;

        $immunization = Immunization::create($validated);

        return response()->json(['success' => true, 'message' => 'Vaccination record added successfully.', 'immunization' => $immunization], 201);
    }

    /**
     * Get a single immunization
     */
    public function show($id)
    {
        $immunization = Immunization::with(['child', 'immunizationSchedule'])->findOrFail($id);
        return response()->json($immunization);
    }

    /**
     * Update an immunization
     */
    public function update(Request $request, $id)
    {
        $immunization = Immunization::findOrFail($id);

        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'nullable|string|max:255',
            'dose_number' => 'nullable|integer|min:1',
            'next_due_date' => 'nullable|date',
            'date_administered' => 'nullable|date',
            'batch_number' => 'nullable|string|max:255',
            'site' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'adverse_reactions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($request->filled('date_administered') && $immunization->status !== 'administered') {
            $validated['status'] = 'administered';
            $validated['administered_by'] = Auth::user()->name;
        } elseif (!$request->filled('date_administered') && $immunization->status === 'administered') {
            $validated['status'] = 'scheduled';
            $validated['administered_by'] = null;
        }

        $immunization->update($validated);

        return response()->json(['success' => true, 'message' => 'Vaccination record updated successfully.', 'immunization' => $immunization]);
    }

    /**
     * Delete an immunization
     */
    public function destroy($id)
    {
        $immunization = Immunization::findOrFail($id);
        $immunization->delete();
        return response()->json(['success' => true, 'message' => 'Vaccination record deleted successfully.']);
    }
}