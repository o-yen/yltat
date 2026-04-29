<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KehadiranPrestasi;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use App\Models\Talent;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class KehadiranPrestasiController extends Controller
{
    public function index(Request $request)
    {
        $query = KehadiranPrestasi::with(['syarikatPenempatan', 'syarikatPelaksana']);

        // Scope by company role — only see own graduates
        $user = auth()->user();
        $role = $user->role?->role_name;
        if ($role === 'rakan_kolaborasi') {
            $idSyarikat = $user->id_syarikat_penempatan;
            if ($idSyarikat) {
                $graduanIds = Talent::where('id_syarikat_penempatan', $idSyarikat)->pluck('id_graduan')->toArray();
                $query->whereIn('id_graduan', $graduanIds ?: ['__none__']);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($role === 'syarikat_pelaksana') {
            $idPelaksana = $user->id_pelaksana;
            if ($idPelaksana) {
                $graduanIds = Talent::where('id_pelaksana', $idPelaksana)->pluck('id_graduan')->toArray();
                $query->whereIn('id_graduan', $graduanIds ?: ['__none__']);
            } else {
                $query->whereRaw('1 = 0');
            }
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
        if ($request->filled('id_syarikat')) {
            $query->where('id_syarikat', $request->id_syarikat);
        }

        $records = $query->orderByDesc('tahun')->orderByDesc('id')->paginate(20)->withQueryString();
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        $bulanList = KehadiranPrestasi::distinct()->pluck('bulan')->sort()->values();
        $isCompanyRole = in_array($role, ['rakan_kolaborasi', 'syarikat_pelaksana']);

        return view('admin.kehadiran.index', compact('records', 'pelaksana', 'penempatan', 'bulanList', 'isCompanyRole'));
    }

    public function create()
    {
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        $talents = \App\Models\Talent::with(['syarikatPenempatan:id_syarikat,nama_syarikat'])
            ->orderBy('full_name')
            ->get(['id_graduan', 'full_name', 'talent_code', 'id_pelaksana', 'id_syarikat_penempatan']);
        return view('admin.kehadiran.create', compact('pelaksana', 'penempatan', 'talents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_graduan' => 'required|string|max:20',
            'id_syarikat' => 'nullable|exists:syarikat_penempatan,id_syarikat',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'hari_hadir' => 'required|integer|min:0',
            'hari_bekerja' => 'required|integer|min:1',
            'skor_prestasi' => 'required|integer|min:1|max:10',
            'komen_mentor' => 'nullable|string',
            'status_logbook' => 'required|in:Dikemukakan,Lewat,Belum Dikemukakan',
        ]);

        $validated['kehadiran_pct'] = round($validated['hari_hadir'] / $validated['hari_bekerja'], 2);

        $record = KehadiranPrestasi::create($validated);
        AuditLog::log('kehadiran_prestasi', 'create', $record->id, null, $record->toArray());

        return redirect()->route('admin.kehadiran.index')->with('success', __('messages.kehadiran_created'));
    }

    public function show(KehadiranPrestasi $kehadiran)
    {
        $this->authorizeCompanyAccess($kehadiran);
        $kehadiran->load(['syarikatPenempatan', 'syarikatPelaksana']);
        return view('admin.kehadiran.show', compact('kehadiran'));
    }

    public function edit(KehadiranPrestasi $kehadiran)
    {
        $this->authorizeCompanyAccess($kehadiran);
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        $talents = \App\Models\Talent::with(['syarikatPenempatan:id_syarikat,nama_syarikat'])
            ->orderBy('full_name')
            ->get(['id_graduan', 'full_name', 'talent_code', 'id_pelaksana', 'id_syarikat_penempatan']);
        return view('admin.kehadiran.edit', compact('kehadiran', 'pelaksana', 'penempatan', 'talents'));
    }

    public function update(Request $request, KehadiranPrestasi $kehadiran)
    {
        $this->authorizeCompanyAccess($kehadiran);
        $validated = $request->validate([
            'id_graduan' => 'required|string|max:20',
            'id_syarikat' => 'nullable|exists:syarikat_penempatan,id_syarikat',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'hari_hadir' => 'required|integer|min:0',
            'hari_bekerja' => 'required|integer|min:1',
            'skor_prestasi' => 'required|integer|min:1|max:10',
            'komen_mentor' => 'nullable|string',
            'status_logbook' => 'required|in:Dikemukakan,Lewat,Belum Dikemukakan',
        ]);

        $validated['kehadiran_pct'] = round($validated['hari_hadir'] / $validated['hari_bekerja'], 2);

        $oldData = $kehadiran->toArray();
        $kehadiran->update($validated);
        AuditLog::log('kehadiran_prestasi', 'update', $kehadiran->id, $oldData, $kehadiran->fresh()->toArray());

        return redirect()->route('admin.kehadiran.index')->with('success', __('messages.kehadiran_updated'));
    }

    private function authorizeCompanyAccess(KehadiranPrestasi $kehadiran): void
    {
        $user = auth()->user();
        $role = $user->role?->role_name;
        $graduan = $kehadiran->id_graduan;

        if ($role === 'rakan_kolaborasi' && $user->id_syarikat_penempatan) {
            $belongs = Talent::where('id_graduan', $graduan)->where('id_syarikat_penempatan', $user->id_syarikat_penempatan)->exists();
            if (!$belongs) abort(403);
        }
        if ($role === 'syarikat_pelaksana' && $user->id_pelaksana) {
            $belongs = Talent::where('id_graduan', $graduan)->where('id_pelaksana', $user->id_pelaksana)->exists();
            if (!$belongs) abort(403);
        }
    }
}
