<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AllowancePaymentMail;
use App\Models\KewanganElaun;
use App\Models\SyarikatPelaksana;
use App\Models\Talent;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class KewanganElaunController extends Controller
{
    public function index(Request $request)
    {
        $query = KewanganElaun::with('syarikatPelaksana');

        // syarikat_pelaksana can only view/edit records for their own graduates
        $role = auth()->user()->role?->role_name;
        if ($role === 'syarikat_pelaksana') {
            $idPelaksana = auth()->user()->syarikatPelaksana?->id_pelaksana;
            $query->where('id_pelaksana', $idPelaksana ?: '__none__');
        }

        if ($request->filled('search')) {
            $query->where('id_graduan', 'like', "%{$request->search}%");
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('id_pelaksana')) {
            $query->where('id_pelaksana', $request->id_pelaksana);
        }
        if ($request->filled('status_bayaran')) {
            $query->where('status_bayaran', $request->status_bayaran);
        }

        $records = $query->orderByDesc('tahun')->orderByDesc('id')->paginate(20)->withQueryString();
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $bulanList = KewanganElaun::distinct()->pluck('bulan')->sort()->values();

        // Summary stats
        $totalPaid = KewanganElaun::where('status_bayaran', 'Selesai')->sum('elaun_prorate');
        $totalPending = KewanganElaun::where('status_bayaran', 'Dalam Proses')->sum('elaun_prorate');
        $totalLate = KewanganElaun::where('status_bayaran', 'Lewat')->count();

        return view('admin.kewangan.index', compact('records', 'pelaksana', 'bulanList', 'totalPaid', 'totalPending', 'totalLate'));
    }

    public function create()
    {
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $talents = \App\Models\Talent::with(['syarikatPenempatan:id_syarikat,nama_syarikat'])
            ->orderBy('full_name')
            ->get(['id_graduan', 'full_name', 'talent_code', 'id_pelaksana', 'id_syarikat_penempatan']);
        return view('admin.kewangan.create', compact('pelaksana', 'talents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_graduan' => 'required|string|max:20',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'tarikh_mula_kerja' => 'nullable|date',
            'tarikh_akhir_kerja' => 'nullable|date',
            'hari_bekerja_sebenar' => 'required|integer|min:0',
            'hari_dalam_bulan' => 'required|integer|min:1',
            'elaun_penuh' => 'required|numeric|min:0',
            'status_bayaran' => 'required|in:Selesai,Dalam Proses,Lewat',
            'tarikh_bayar' => 'nullable|date',
            'tarikh_jangka_bayar' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $validated['elaun_prorate'] = round($validated['elaun_penuh'] * ($validated['hari_bekerja_sebenar'] / $validated['hari_dalam_bulan']), 2);
        $validated['hari_lewat'] = 0;
        if ($validated['tarikh_bayar'] && $validated['tarikh_jangka_bayar']) {
            $diff = strtotime($validated['tarikh_bayar']) - strtotime($validated['tarikh_jangka_bayar']);
            $validated['hari_lewat'] = max(0, (int) ($diff / 86400));
        }

        $record = KewanganElaun::create($validated);
        AuditLog::log('kewangan_elaun', 'create', $record->id, null, $record->toArray());

        return redirect()->route('admin.kewangan.index')->with('success', __('messages.kewangan_created'));
    }

    public function show(KewanganElaun $kewangan)
    {
        $kewangan->load('syarikatPelaksana');
        return view('admin.kewangan.show', compact('kewangan'));
    }

    public function edit(KewanganElaun $kewangan)
    {
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $talents = \App\Models\Talent::with(['syarikatPenempatan:id_syarikat,nama_syarikat'])
            ->orderBy('full_name')
            ->get(['id_graduan', 'full_name', 'talent_code', 'id_pelaksana', 'id_syarikat_penempatan']);
        return view('admin.kewangan.edit', compact('kewangan', 'pelaksana', 'talents'));
    }

    public function update(Request $request, KewanganElaun $kewangan)
    {
        $validated = $request->validate([
            'id_graduan' => 'required|string|max:20',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'tarikh_mula_kerja' => 'nullable|date',
            'tarikh_akhir_kerja' => 'nullable|date',
            'hari_bekerja_sebenar' => 'required|integer|min:0',
            'hari_dalam_bulan' => 'required|integer|min:1',
            'elaun_penuh' => 'required|numeric|min:0',
            'status_bayaran' => 'required|in:Selesai,Dalam Proses,Lewat',
            'tarikh_bayar' => 'nullable|date',
            'tarikh_jangka_bayar' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $validated['elaun_prorate'] = round($validated['elaun_penuh'] * ($validated['hari_bekerja_sebenar'] / $validated['hari_dalam_bulan']), 2);
        $validated['hari_lewat'] = 0;
        if ($validated['tarikh_bayar'] && $validated['tarikh_jangka_bayar']) {
            $diff = strtotime($validated['tarikh_bayar']) - strtotime($validated['tarikh_jangka_bayar']);
            $validated['hari_lewat'] = max(0, (int) ($diff / 86400));
        }

        $oldStatus = $kewangan->status_bayaran;
        $oldData = $kewangan->toArray();
        $kewangan->update($validated);
        AuditLog::log('kewangan_elaun', 'update', $kewangan->id, $oldData, $kewangan->fresh()->toArray());

        // Notify talent when payment status changes to Selesai
        if ($validated['status_bayaran'] === 'Selesai' && $oldStatus !== 'Selesai') {
            try {
                $talent = Talent::where('id_graduan', $kewangan->id_graduan)->first();
                if ($talent?->email) {
                    Mail::to($talent->email)->send(new AllowancePaymentMail($kewangan->fresh(), $talent->full_name, 'Selesai'));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('admin.kewangan.index')->with('success', __('messages.kewangan_updated'));
    }
}
