<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CriticalIssueMail;
use App\Models\IsuRisiko;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class IsuRisikoController extends Controller
{
    public function index(Request $request)
    {
        $query = IsuRisiko::with(['syarikatPelaksana', 'syarikatPenempatan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_isu', 'like', "%{$search}%")
                  ->orWhere('butiran_isu', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%");
            });
        }
        if ($request->filled('kategori_isu')) {
            $query->where('kategori_isu', $request->kategori_isu);
        }
        if ($request->filled('tahap_risiko')) {
            $query->where('tahap_risiko', $request->tahap_risiko);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('id_pelaksana')) {
            $query->where('id_pelaksana', $request->id_pelaksana);
        }
        if ($request->filled('id_syarikat')) {
            $query->where('id_syarikat', $request->id_syarikat);
        }

        $records = $query->orderByDesc('tarikh_isu')->paginate(20)->withQueryString();
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();

        // Summary
        $totalBaru = IsuRisiko::where('status', 'Baru')->count();
        $totalDalamTindakan = IsuRisiko::where('status', 'Dalam Tindakan')->count();
        $totalKritikal = IsuRisiko::where('tahap_risiko', 'Kritikal')->whereIn('status', ['Baru', 'Dalam Tindakan'])->count();

        return view('admin.isu-risiko.index', compact('records', 'pelaksana', 'penempatan', 'totalBaru', 'totalDalamTindakan', 'totalKritikal'));
    }

    public function create()
    {
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        return view('admin.isu-risiko.create', compact('pelaksana', 'penempatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tarikh_isu' => 'required|date',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'id_syarikat' => 'nullable|exists:syarikat_penempatan,id_syarikat',
            'kategori_isu' => 'required|in:Bayaran Lewat,Kehadiran Rendah,Prestasi Lemah,Logbook Lewat,Isu Pematuhan,Masalah Komunikasi,Lain-lain',
            'butiran_isu' => 'required|string',
            'tahap_risiko' => 'required|in:Kritikal,Tinggi,Sederhana,Rendah',
            'status' => 'required|in:Baru,Dalam Tindakan,Selesai,Ditutup',
            'pic' => 'required|string|max:200',
            'tindakan_diambil' => 'nullable|string',
            'tarikh_tindakan' => 'nullable|date',
            'tarikh_tutup' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $validated['id_isu'] = IsuRisiko::generateId();

        $record = IsuRisiko::create($validated);
        AuditLog::log('isu_risiko', 'create', $record->id_isu, null, $record->toArray());

        // Notify PMO admins when a critical issue is logged
        if ($record->tahap_risiko === 'Kritikal') {
            try {
                $pmoRoles = Role::whereIn('role_name', ['super_admin', 'pmo_admin'])->pluck('id');
                $pmoUsers = User::whereIn('role_id', $pmoRoles)->where('status', 'active')->get();
                foreach ($pmoUsers as $pmoUser) {
                    Mail::to($pmoUser->email)->send(new CriticalIssueMail($record));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('admin.isu-risiko.show', $record)->with('success', __('messages.isu_created'));
    }

    public function show(IsuRisiko $isuRisiko)
    {
        $isuRisiko->load(['syarikatPelaksana', 'syarikatPenempatan']);
        return view('admin.isu-risiko.show', compact('isuRisiko'));
    }

    public function edit(IsuRisiko $isuRisiko)
    {
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        return view('admin.isu-risiko.edit', compact('isuRisiko', 'pelaksana', 'penempatan'));
    }

    public function update(Request $request, IsuRisiko $isuRisiko)
    {
        $validated = $request->validate([
            'tarikh_isu' => 'required|date',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'id_syarikat' => 'nullable|exists:syarikat_penempatan,id_syarikat',
            'kategori_isu' => 'required|in:Bayaran Lewat,Kehadiran Rendah,Prestasi Lemah,Logbook Lewat,Isu Pematuhan,Masalah Komunikasi,Lain-lain',
            'butiran_isu' => 'required|string',
            'tahap_risiko' => 'required|in:Kritikal,Tinggi,Sederhana,Rendah',
            'status' => 'required|in:Baru,Dalam Tindakan,Selesai,Ditutup',
            'pic' => 'required|string|max:200',
            'tindakan_diambil' => 'nullable|string',
            'tarikh_tindakan' => 'nullable|date',
            'tarikh_tutup' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $oldData = $isuRisiko->toArray();
        $isuRisiko->update($validated);
        AuditLog::log('isu_risiko', 'update', $isuRisiko->id_isu, $oldData, $isuRisiko->fresh()->toArray());

        return redirect()->route('admin.isu-risiko.show', $isuRisiko)->with('success', __('messages.isu_updated'));
    }
}
