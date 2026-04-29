<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetAllocation;
use App\Models\BudgetTransaction;
use App\Models\KewanganElaun;
use App\Models\SyarikatPelaksana;
use App\Models\Company;
use App\Models\IntakeBatch;
use App\Models\Placement;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index()
    {
        $currentYear = date('Y');

        $totalAllocated = BudgetAllocation::where('fiscal_year', $currentYear)->sum('allocated_amount');

        // Manual budget transactions
        $manualSpent = BudgetTransaction::where('status', 'approved')
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        // Kewangan (allowance) payments — actual salary disbursements
        $kewanganPaid = KewanganElaun::where('tahun', $currentYear)
            ->where('status_bayaran', 'Selesai')
            ->sum('elaun_prorate');
        $kewanganPending = KewanganElaun::where('tahun', $currentYear)
            ->where('status_bayaran', 'Dalam Proses')
            ->sum('elaun_prorate');

        // Total spent = manual transactions + kewangan paid
        $totalSpent = $manualSpent + $kewanganPaid;

        // Count months already covered by kewangan records this year
        $kewanganMonthsCovered = KewanganElaun::where('tahun', $currentYear)
            ->select('id_graduan', 'bulan')
            ->distinct()
            ->count();

        // Projected remaining from active placements, minus months already paid
        $activePlacements = Placement::with('talent')
            ->whereIn('placement_status', ['active', 'confirmed'])
            ->get();

        $projectedRemaining = $activePlacements->sum(function ($placement) use ($currentYear) {
            $remainingMonths = $placement->remaining_months;
            if ($remainingMonths <= 0) return 0;

            // Subtract months that already have kewangan records
            $paidMonths = 0;
            if ($placement->talent) {
                $paidMonths = KewanganElaun::where('id_graduan', $placement->talent->talent_code ?? $placement->talent->id_graduan ?? '')
                    ->where('tahun', $currentYear)
                    ->count();
            }

            $unpaidMonths = max(0, $remainingMonths - $paidMonths);
            return $placement->monthly_stipend * $unpaidMonths;
        });

        $forecastTotal = $totalSpent + $kewanganPending + $projectedRemaining;
        $actualRemaining = $totalAllocated - $totalSpent;
        $isOverrun = $forecastTotal > $totalAllocated;

        // Monthly trend — combine budget transactions + kewangan payments
        $budgetTrend = BudgetTransaction::select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'approved')
            ->whereYear('transaction_date', $currentYear)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Map Malay month names to month numbers for kewangan
        $monthMap = [
            'Januari' => 1, 'Februari' => 2, 'Mac' => 3, 'April' => 4,
            'Mei' => 5, 'Jun' => 6, 'Julai' => 7, 'Ogos' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Disember' => 12,
        ];

        $kewanganTrend = KewanganElaun::where('tahun', $currentYear)
            ->where('status_bayaran', 'Selesai')
            ->get()
            ->groupBy('bulan')
            ->mapWithKeys(fn($items, $bulan) => [
                $monthMap[$bulan] ?? 0 => $items->sum('elaun_prorate'),
            ]);

        $trendLabels = [];
        $trendData = [];
        for ($m = 1; $m <= 12; $m++) {
            $trendLabels[] = Carbon::createFromDate($currentYear, $m, 1)->format('M');
            $budget = isset($budgetTrend[$m]) ? (float)$budgetTrend[$m]->total : 0;
            $kewangan = isset($kewanganTrend[$m]) ? (float)$kewanganTrend[$m] : 0;
            $trendData[] = $budget + $kewangan;
        }

        // Company breakdown — combine both sources
        $budgetByCompany = BudgetTransaction::select('company_id', DB::raw('SUM(amount) as total'))
            ->where('status', 'approved')
            ->whereYear('transaction_date', $currentYear)
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->get()
            ->keyBy('company_id');

        // Kewangan by pelaksana → map to company via company_code
        $kewanganByPelaksana = KewanganElaun::where('tahun', $currentYear)
            ->where('status_bayaran', 'Selesai')
            ->whereNotNull('id_pelaksana')
            ->select('id_pelaksana', DB::raw('SUM(elaun_prorate) as total'))
            ->groupBy('id_pelaksana')
            ->get();

        $companyTotals = collect();
        foreach ($budgetByCompany as $companyId => $row) {
            $company = Company::find($companyId);
            $name = $company?->company_name ?? 'Unknown';
            $companyTotals[$name] = ($companyTotals[$name] ?? 0) + (float)$row->total;
        }
        foreach ($kewanganByPelaksana as $row) {
            $pelaksana = SyarikatPelaksana::find($row->id_pelaksana);
            $name = $pelaksana?->nama_syarikat ?? $row->id_pelaksana;
            $companyTotals[$name] = ($companyTotals[$name] ?? 0) + (float)$row->total;
        }

        $companyTotals = $companyTotals->sortByDesc(fn($v) => $v);
        $companyLabels = $companyTotals->keys()->toArray();
        $companyData = $companyTotals->values()->toArray();

        // Recent transactions — show both budget txns and recent kewangan payments
        $recentBudgetTxns = BudgetTransaction::with(['talent:id,full_name', 'company:id,company_name'])
            ->orderByDesc('transaction_date')
            ->limit(5)
            ->get()
            ->map(fn($t) => [
                'label' => $t->company?->company_name ?? $t->talent?->full_name ?? __('common.general'),
                'sub' => ($t->transaction_date?->format('d/m/Y') ?? '') . ' · ' . ucfirst($t->category),
                'amount' => $t->amount,
                'type' => 'budget',
            ]);

        $recentKewanganTxns = KewanganElaun::with('graduan')
            ->where('status_bayaran', 'Selesai')
            ->orderByDesc('tarikh_bayar')
            ->limit(5)
            ->get()
            ->map(fn($k) => [
                'label' => $k->graduan?->full_name ?? $k->id_graduan,
                'sub' => ($k->tarikh_bayar?->format('d/m/Y') ?? $k->bulan . ' ' . $k->tahun) . ' · ' . __('common.budget_categories.allowance'),
                'amount' => $k->elaun_prorate,
                'type' => 'kewangan',
            ]);

        $recentTransactions = $recentBudgetTxns->merge($recentKewanganTxns)
            ->sortByDesc('amount')
            ->take(10)
            ->values();

        // Allocations — with batch name for clarity
        $allocations = BudgetAllocation::with(['batch', 'company'])
            ->where('fiscal_year', $currentYear)
            ->get();

        return view('admin.budget.index', compact(
            'currentYear',
            'totalAllocated',
            'totalSpent',
            'manualSpent',
            'kewanganPaid',
            'kewanganPending',
            'projectedRemaining',
            'forecastTotal',
            'actualRemaining',
            'isOverrun',
            'trendLabels',
            'trendData',
            'companyLabels',
            'companyData',
            'recentTransactions',
            'allocations'
        ));
    }

    public function allocations(Request $request)
    {
        $query = BudgetAllocation::with(['batch', 'company']);

        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        $allocations = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $batches = IntakeBatch::orderByDesc('year')->get();
        $fiscalYears = range(date('Y') + 1, date('Y') - 2);

        return view('admin.budget.allocations', compact('allocations', 'pelaksana', 'batches', 'fiscalYears'));
    }

    public function storeAllocation(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year' => 'required|string|max:10',
            'batch_id' => 'nullable|exists:intake_batches,id',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'allocated_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        // Map pelaksana to company record for FK compatibility
        $companyId = null;
        if (!empty($validated['id_pelaksana'])) {
            $company = Company::where('company_code', $validated['id_pelaksana'])->first();
            $companyId = $company?->id;
        }

        $allocation = BudgetAllocation::create([
            'fiscal_year' => $validated['fiscal_year'],
            'batch_id' => $validated['batch_id'],
            'company_id' => $companyId,
            'allocated_amount' => $validated['allocated_amount'],
            'remarks' => $validated['remarks'],
        ]);

        AuditLog::log('budget', 'create_allocation', $allocation->id, null, $allocation->toArray());

        return redirect()->route('admin.budget.allocations')
            ->with('success', __('messages.allocation_created'));
    }

    public function destroyAllocation(BudgetAllocation $allocation)
    {
        $allocation->delete();
        return back()->with('success', __('messages.allocation_deleted'));
    }

    public function transactions(Request $request)
    {
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $placements = Placement::with(['talent', 'company'])->whereIn('placement_status', ['active', 'confirmed'])->get();
        $categories = ['allowance', 'equipment', 'training', 'admin', 'travel', 'other'];

        $filterCategory = $request->input('category');
        $filterSearch = $request->input('search');
        $editTransaction = null;

        if ($request->filled('edit_transaction')) {
            $editTransaction = BudgetTransaction::find($request->integer('edit_transaction'));
        }

        // Budget transactions (non-allowance manual entries)
        $budgetRows = collect();
        if (!$filterCategory || $filterCategory !== 'allowance') {
            $bQuery = BudgetTransaction::with(['talent', 'company', 'placement']);
            if ($filterSearch) {
                $bQuery->where(function ($q) use ($filterSearch) {
                    $q->where('description', 'like', "%{$filterSearch}%")
                      ->orWhere('reference_no', 'like', "%{$filterSearch}%");
                });
            }
            if ($filterCategory) {
                $bQuery->where('category', $filterCategory);
            }
            $budgetRows = $bQuery->orderByDesc('transaction_date')->get()->map(fn($t) => [
                'date' => $t->transaction_date,
                'category' => $t->category,
                'description' => $t->description,
                'company' => $t->company?->company_name ?? $t->talent?->full_name ?? '-',
                'amount' => (float) $t->amount,
                'status' => $t->status,
                'type' => 'budget',
                'id' => $t->id,
                'placement_id' => $t->placement_id,
                'company_id' => $t->company_id,
                'reference_no' => $t->reference_no,
            ])->toBase();
        }

        // Kewangan (allowance) payments
        $kewanganRows = collect();
        if (!$filterCategory || $filterCategory === 'allowance') {
            $kQuery = KewanganElaun::with(['graduan', 'syarikatPelaksana']);
            if ($filterSearch) {
                $kQuery->where(function ($q) use ($filterSearch) {
                    $q->where('id_graduan', 'like', "%{$filterSearch}%")
                      ->orWhereHas('graduan', fn($q2) => $q2->where('full_name', 'like', "%{$filterSearch}%"));
                });
            }
            $kewanganRows = $kQuery->orderByDesc('tarikh_bayar')->get()->map(fn($k) => [
                'date' => $k->tarikh_bayar ?? $k->created_at,
                'category' => 'allowance',
                'description' => $k->bulan . ' ' . $k->tahun,
                'company' => $k->graduan?->full_name ?? $k->id_graduan,
                'amount' => (float) $k->elaun_prorate,
                'status' => match($k->status_bayaran) {
                    'Selesai' => 'approved',
                    'Dalam Proses' => 'pending',
                    default => 'pending',
                },
                'type' => 'kewangan',
                'id' => $k->id,
            ])->toBase();
        }

        // Merge and sort
        $allRows = $budgetRows->merge($kewanganRows)->sortByDesc('date')->values();

        // Manual pagination
        $page = $request->input('page', 1);
        $perPage = 20;
        $paginatedRows = new \Illuminate\Pagination\LengthAwarePaginator(
            $allRows->forPage($page, $perPage)->values(),
            $allRows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.budget.transactions', compact('paginatedRows', 'pelaksana', 'placements', 'categories', 'editTransaction'));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $this->validateTransaction($request);

        // Map pelaksana to company_id for FK compatibility
        $companyId = null;
        if (!empty($validated['id_pelaksana'])) {
            $company = Company::where('company_code', $validated['id_pelaksana'])->first();
            $companyId = $company?->id;
        } elseif (!empty($validated['placement_id'])) {
            $placement = Placement::find($validated['placement_id']);
            $companyId = $placement?->company_id;
        }

        unset($validated['id_pelaksana']);
        $validated['company_id'] = $companyId;

        $transaction = BudgetTransaction::create($validated);

        AuditLog::log('budget', 'create_transaction', $transaction->id, null, $transaction->toArray());

        return redirect()->route('admin.budget.transactions')
            ->with('success', __('messages.transaction_created'));
    }

    public function updateTransaction(Request $request, BudgetTransaction $transaction)
    {
        $validated = $this->validateTransaction($request);

        $companyId = null;
        if (!empty($validated['id_pelaksana'])) {
            $company = Company::where('company_code', $validated['id_pelaksana'])->first();
            $companyId = $company?->id;
        } elseif (!empty($validated['placement_id'])) {
            $placement = Placement::find($validated['placement_id']);
            $companyId = $placement?->company_id;
        }

        unset($validated['id_pelaksana']);
        $validated['company_id'] = $companyId;

        $old = $transaction->toArray();
        $transaction->update($validated);

        AuditLog::log('budget', 'update_transaction', $transaction->id, $old, $transaction->fresh()->toArray());

        return redirect()->route('admin.budget.transactions')
            ->with('success', __('messages.transaction_updated'));
    }

    public function destroyTransaction(BudgetTransaction $transaction)
    {
        $transaction->delete();
        return back()->with('success', __('messages.transaction_deleted'));
    }

    protected function validateTransaction(Request $request): array
    {
        return $request->validate([
            'placement_id' => 'nullable|exists:placements,id',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'transaction_date' => 'required|date',
            'category' => 'required|string|max:100|in:equipment,training,admin,travel,other',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:approved,pending,rejected',
            'reference_no' => 'nullable|string|max:100',
        ]);
    }
}
