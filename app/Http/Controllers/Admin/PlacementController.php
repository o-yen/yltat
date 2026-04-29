<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Placement;
use App\Models\Talent;
use App\Models\Company;
use App\Models\IntakeBatch;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    public function index(Request $request)
    {
        $query = Placement::with(['talent', 'company', 'batch'])
            ->whereHas('talent', fn ($talentQuery) => $talentQuery->whereNotNull('id_graduan'))
            ->whereHas('company', fn ($companyQuery) => $companyQuery->where('company_code', 'like', 'SPTAN_%'));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('talent', fn($talentQuery) => $talentQuery->where('full_name', 'like', "%{$search}%"))
                    ->orWhereHas('company', fn($companyQuery) => $companyQuery->where('company_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('placement_status', $request->status);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        $placements = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $companies = Company::where('status', 'active')->where('company_code', 'like', 'SPTAN_%')->orderBy('company_name')->get();
        $batches = IntakeBatch::orderByDesc('year')->get();
        $statuses = ['planned', 'confirmed', 'active', 'extended', 'completed', 'terminated', 'cancelled'];

        return view('admin.placements.index', compact('placements', 'companies', 'batches', 'statuses'));
    }

    public function create()
    {
        $talents = Talent::whereNotNull('id_graduan')->orderBy('full_name')->get();
        $companies = Company::where('status', 'active')->where('company_code', 'like', 'SPTAN_%')->orderBy('company_name')->get();
        $batches = IntakeBatch::whereIn('status', ['active', 'planned'])->orderByDesc('year')->get();

        return view('admin.placements.create', compact('talents', 'companies', 'batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'talent_id' => 'required|exists:talents,id',
            'company_id' => 'required|exists:companies,id',
            'batch_id' => 'nullable|exists:intake_batches,id',
            'department' => 'nullable|string|max:200',
            'supervisor_name' => 'nullable|string|max:200',
            'supervisor_email' => 'nullable|email|max:200',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_months' => 'nullable|integer|min:1|max:24',
            'monthly_stipend' => 'required|numeric|min:0',
            'additional_cost' => 'nullable|numeric|min:0',
            'placement_status' => 'required|in:planned,confirmed,active,extended,completed,terminated,cancelled',
            'programme_type' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $placement = Placement::create($validated);

        // Update talent status to assigned
        $talent = Talent::find($validated['talent_id']);
        if ($talent && in_array($talent->status, ['approved', 'shortlisted'])) {
            $talent->update(['status' => 'assigned']);
        }

        AuditLog::log('placements', 'create', $placement->id, null, $placement->toArray());

        return redirect()->route('admin.placements.show', $placement)
            ->with('success', __('messages.placement_created'));
    }

    public function show(Placement $placement)
    {
        abort_unless($placement->talent?->id_graduan && str_starts_with($placement->company?->company_code ?? '', 'SPTAN_'), 404);

        $placement->load(['talent', 'company', 'batch', 'feedback', 'budgetTransactions']);

        return view('admin.placements.show', compact('placement'));
    }

    public function edit(Placement $placement)
    {
        abort_unless($placement->talent?->id_graduan && str_starts_with($placement->company?->company_code ?? '', 'SPTAN_'), 404);

        $talents = Talent::whereNotNull('id_graduan')->orderBy('full_name')->get();
        $companies = Company::where('status', 'active')->where('company_code', 'like', 'SPTAN_%')->orderBy('company_name')->get();
        $batches = IntakeBatch::orderByDesc('year')->get();

        return view('admin.placements.edit', compact('placement', 'talents', 'companies', 'batches'));
    }

    public function update(Request $request, Placement $placement)
    {
        $validated = $request->validate([
            'talent_id' => 'required|exists:talents,id',
            'company_id' => 'required|exists:companies,id',
            'batch_id' => 'nullable|exists:intake_batches,id',
            'department' => 'nullable|string|max:200',
            'supervisor_name' => 'nullable|string|max:200',
            'supervisor_email' => 'nullable|email|max:200',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_months' => 'nullable|integer|min:1|max:24',
            'monthly_stipend' => 'required|numeric|min:0',
            'additional_cost' => 'nullable|numeric|min:0',
            'placement_status' => 'required|in:planned,confirmed,active,extended,completed,terminated,cancelled',
            'programme_type' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $oldData = $placement->toArray();
        $placement->update($validated);

        AuditLog::log('placements', 'update', $placement->id, $oldData, $placement->fresh()->toArray());

        return redirect()->route('admin.placements.show', $placement)
            ->with('success', __('messages.placement_updated'));
    }
}
