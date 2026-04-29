<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SyarikatPelaksana;
use App\Models\AuditLog;
use App\Models\BudgetAllocation;
use App\Models\BudgetTransaction;
use App\Models\Company;
use App\Models\KewanganElaun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyarikatPelaksanaController extends Controller
{
    public function index(Request $request)
    {
        $query = SyarikatPelaksana::query();

        // syarikat_pelaksana can only view/edit their own company record
        $role = auth()->user()->role?->role_name;
        if ($role === 'syarikat_pelaksana') {
            $idPelaksana = auth()->user()->syarikatPelaksana?->id_pelaksana;
            $query->where('id_pelaksana', $idPelaksana ?: '__none__');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_syarikat', 'like', "%{$search}%")
                  ->orWhere('id_pelaksana', 'like', "%{$search}%")
                  ->orWhere('pic_syarikat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_dana')) {
            $query->where('status_dana', $request->status_dana);
        }

        if ($request->filled('tahap_pematuhan')) {
            $query->where('tahap_pematuhan', $request->tahap_pematuhan);
        }

        $pelaksana = $query->orderBy('id_pelaksana')->paginate(20)->withQueryString();

        $currentYear = (string) date('Y');
        $pelaksanaIds = $pelaksana->getCollection()->pluck('id_pelaksana')->filter()->values();

        $companyIdsByCode = Company::whereIn('company_code', $pelaksanaIds)
            ->pluck('id', 'company_code');

        $companyIds = $companyIdsByCode->values()->filter()->unique()->values();

        $allocatedByCompany = BudgetAllocation::where('fiscal_year', $currentYear)
            ->whereIn('company_id', $companyIds)
            ->select('company_id', DB::raw('SUM(allocated_amount) as total_allocated'))
            ->groupBy('company_id')
            ->pluck('total_allocated', 'company_id');

        $budgetSpentByCompany = BudgetTransaction::where('status', 'approved')
            ->whereYear('transaction_date', (int) $currentYear)
            ->whereIn('company_id', $companyIds)
            ->select('company_id', DB::raw('SUM(amount) as total_spent'))
            ->groupBy('company_id')
            ->pluck('total_spent', 'company_id');

        $allowanceSpentByPelaksana = KewanganElaun::where('tahun', $currentYear)
            ->where('status_bayaran', 'Selesai')
            ->whereIn('id_pelaksana', $pelaksanaIds)
            ->select('id_pelaksana', DB::raw('SUM(elaun_prorate) as total_spent'))
            ->groupBy('id_pelaksana')
            ->pluck('total_spent', 'id_pelaksana');

        $pelaksana->setCollection(
            $pelaksana->getCollection()->map(function (SyarikatPelaksana $item) use ($companyIdsByCode, $allocatedByCompany, $budgetSpentByCompany, $allowanceSpentByPelaksana) {
                $companyId = $companyIdsByCode->get($item->id_pelaksana);
                $allocated = (float) ($allocatedByCompany->get($companyId) ?? $item->peruntukan_diluluskan ?? 0);
                $manualSpent = (float) ($budgetSpentByCompany->get($companyId) ?? 0);
                $allowanceSpent = (float) ($allowanceSpentByPelaksana->get($item->id_pelaksana) ?? 0);
                $used = $manualSpent + $allowanceSpent;
                $balance = $allocated - $used;
                $balancePct = $allocated > 0 ? ($balance / $allocated) * 100 : 0;

                $item->peruntukan_diluluskan = $allocated;
                $item->peruntukan_diguna = $used;
                $item->baki_peruntukan = $balance;
                $item->usage_pct = $allocated > 0 ? round(($used / $allocated) * 100, 1) : 0;
                $item->status_dana = $allocated <= 0
                    ? 'Kritikal'
                    : ($balancePct > 20 ? 'Mencukupi' : ($balancePct >= 10 ? 'Perlu Perhatian' : 'Kritikal'));

                return $item;
            })
        );

        return view('admin.syarikat-pelaksana.index', compact('pelaksana'));
    }

    public function create()
    {
        return view('admin.syarikat-pelaksana.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_syarikat' => 'required|string|max:255',
            'projek_kontrak' => 'nullable|string|max:255',
            'jumlah_kuota_obligasi' => 'required|integer|min:0',
            'kuota_diluluskan' => 'required|integer|min:0',
            'kuota_digunakan' => 'nullable|integer|min:0',
            'peruntukan_diluluskan' => 'required|numeric|min:0',
            'peruntukan_diguna' => 'nullable|numeric|min:0',
            'pic_syarikat' => 'required|string|max:200',
            'email_pic' => 'required|email|max:200',
            'tahap_pematuhan' => 'required|in:Cemerlang,Baik,Memuaskan,Sederhana,Perlu Penambahbaikan',
        ]);

        $validated['id_pelaksana'] = SyarikatPelaksana::generateId();
        $validated['kuota_digunakan'] = $validated['kuota_digunakan'] ?? 0;
        $validated['peruntukan_diguna'] = $validated['peruntukan_diguna'] ?? 0;
        $validated['baki_peruntukan'] = $validated['peruntukan_diluluskan'] - $validated['peruntukan_diguna'];
        $validated['status_surat_kuning'] = 'Belum Mula';
        $validated['status_surat_biru'] = 'Belum Mula';

        // Auto-calculate dana status
        $bakiPct = $validated['peruntukan_diluluskan'] > 0
            ? ($validated['baki_peruntukan'] / $validated['peruntukan_diluluskan']) * 100
            : 0;
        $validated['status_dana'] = $bakiPct > 20 ? 'Mencukupi' : ($bakiPct >= 10 ? 'Perlu Perhatian' : 'Kritikal');

        $pelaksana = SyarikatPelaksana::create($validated);

        AuditLog::log('syarikat_pelaksana', 'create', $pelaksana->id_pelaksana, null, $pelaksana->toArray());

        return redirect()->route('admin.syarikat-pelaksana.show', $pelaksana)
            ->with('success', __('messages.pelaksana_created'));
    }

    public function show(SyarikatPelaksana $syarikatPelaksana)
    {
        $syarikatPelaksana->loadCount('graduan');

        $suratRecords = \App\Models\StatusSurat::where('id_pelaksana', $syarikatPelaksana->id_pelaksana)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('admin.syarikat-pelaksana.show', compact('syarikatPelaksana', 'suratRecords'));
    }

    public function edit(SyarikatPelaksana $syarikatPelaksana)
    {
        return view('admin.syarikat-pelaksana.edit', compact('syarikatPelaksana'));
    }

    public function update(Request $request, SyarikatPelaksana $syarikatPelaksana)
    {
        $validated = $request->validate([
            'nama_syarikat' => 'required|string|max:255',
            'projek_kontrak' => 'nullable|string|max:255',
            'jumlah_kuota_obligasi' => 'required|integer|min:0',
            'kuota_diluluskan' => 'required|integer|min:0',
            'kuota_digunakan' => 'nullable|integer|min:0',
            'peruntukan_diluluskan' => 'required|numeric|min:0',
            'peruntukan_diguna' => 'nullable|numeric|min:0',
            'pic_syarikat' => 'required|string|max:200',
            'email_pic' => 'required|email|max:200',
            'tahap_pematuhan' => 'required|in:Cemerlang,Baik,Memuaskan,Sederhana,Perlu Penambahbaikan',
        ]);

        $validated['kuota_digunakan'] = $validated['kuota_digunakan'] ?? 0;
        $validated['peruntukan_diguna'] = $validated['peruntukan_diguna'] ?? 0;
        $validated['baki_peruntukan'] = $validated['peruntukan_diluluskan'] - $validated['peruntukan_diguna'];

        $bakiPct = $validated['peruntukan_diluluskan'] > 0
            ? ($validated['baki_peruntukan'] / $validated['peruntukan_diluluskan']) * 100
            : 0;
        $validated['status_dana'] = $bakiPct > 20 ? 'Mencukupi' : ($bakiPct >= 10 ? 'Perlu Perhatian' : 'Kritikal');

        $oldData = $syarikatPelaksana->toArray();
        $syarikatPelaksana->update($validated);

        AuditLog::log('syarikat_pelaksana', 'update', $syarikatPelaksana->id_pelaksana, $oldData, $syarikatPelaksana->fresh()->toArray());

        return redirect()->route('admin.syarikat-pelaksana.show', $syarikatPelaksana)
            ->with('success', __('messages.pelaksana_updated'));
    }

    public function destroy(SyarikatPelaksana $syarikatPelaksana)
    {
        if ($syarikatPelaksana->graduan()->exists()) {
            return redirect()->route('admin.syarikat-pelaksana.show', $syarikatPelaksana)
                ->with('error', __('messages.pelaksana_delete_blocked'));
        }

        $oldData = $syarikatPelaksana->toArray();
        $syarikatPelaksana->delete();

        AuditLog::log('syarikat_pelaksana', 'delete', $syarikatPelaksana->id_pelaksana, $oldData, null);

        return redirect()->route('admin.syarikat-pelaksana.index')
            ->with('success', __('messages.pelaksana_deleted'));
    }
}
