<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SyarikatPenempatan;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SyarikatPenempatanController extends Controller
{
    public function index(Request $request)
    {
        $query = SyarikatPenempatan::query();

        // rakan_kolaborasi can only view/edit their own company record
        $role = auth()->user()->role?->role_name;
        if ($role === 'rakan_kolaborasi') {
            $idSyarikat = auth()->user()->syarikatPenempatan?->id_syarikat;
            $query->where('id_syarikat', $idSyarikat ?: '__none__');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_syarikat', 'like', "%{$search}%")
                  ->orWhere('id_syarikat', 'like', "%{$search}%")
                  ->orWhere('sektor_industri', 'like', "%{$search}%")
                  ->orWhere('pic', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_pematuhan')) {
            $query->where('status_pematuhan', $request->status_pematuhan);
        }

        if ($request->filled('status_training')) {
            $query->where('status_training', $request->status_training);
        }

        if ($request->filled('sektor_industri')) {
            $query->where('sektor_industri', $request->sektor_industri);
        }

        $penempatan = $query->orderBy('id_syarikat')->paginate(20)->withQueryString();
        $sektors = SyarikatPenempatan::distinct()->pluck('sektor_industri')->sort()->values();

        return view('admin.syarikat-penempatan.index', compact('penempatan', 'sektors'));
    }

    public function create()
    {
        return view('admin.syarikat-penempatan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_syarikat' => 'required|string|max:255',
            'jenis_syarikat' => 'nullable|string|max:50',
            'sektor_industri' => 'required|string|max:100',
            'kuota_dipersetujui' => 'required|integer|min:0',
            'jumlah_graduan_ditempatkan' => 'nullable|integer|min:0',
            'pic' => 'required|string|max:200',
            'no_telefon_pic' => 'required|string|max:30',
            'email_pic' => 'required|email|max:200',
            'laporan_bulanan' => 'required|in:Lengkap,Tertangguh,Tidak Lengkap',
            'status_pematuhan' => 'required|in:Cemerlang,Baik,Memuaskan,Perlu Penambahbaikan',
            'catatan' => 'nullable|string',
        ]);

        $validated['id_syarikat'] = SyarikatPenempatan::generateId();
        $validated['jenis_syarikat'] = $validated['jenis_syarikat'] ?? 'Rakan Kolaborasi';
        $validated['jumlah_graduan_ditempatkan'] = $validated['jumlah_graduan_ditempatkan'] ?? 0;

        $penempatan = SyarikatPenempatan::create($validated);

        AuditLog::log('syarikat_penempatan', 'create', $penempatan->id_syarikat, null, $penempatan->toArray());

        return redirect()->route('admin.syarikat-penempatan.show', $penempatan)
            ->with('success', __('messages.penempatan_created'));
    }

    public function show(SyarikatPenempatan $syarikatPenempatan)
    {
        $syarikatPenempatan->loadCount('graduan');

        return view('admin.syarikat-penempatan.show', compact('syarikatPenempatan'));
    }

    public function edit(SyarikatPenempatan $syarikatPenempatan)
    {
        return view('admin.syarikat-penempatan.edit', compact('syarikatPenempatan'));
    }

    public function update(Request $request, SyarikatPenempatan $syarikatPenempatan)
    {
        $validated = $request->validate([
            'nama_syarikat' => 'required|string|max:255',
            'jenis_syarikat' => 'nullable|string|max:50',
            'sektor_industri' => 'required|string|max:100',
            'kuota_dipersetujui' => 'required|integer|min:0',
            'jumlah_graduan_ditempatkan' => 'nullable|integer|min:0',
            'pic' => 'required|string|max:200',
            'no_telefon_pic' => 'required|string|max:30',
            'email_pic' => 'required|email|max:200',
            'laporan_bulanan' => 'required|in:Lengkap,Tertangguh,Tidak Lengkap',
            'status_pematuhan' => 'required|in:Cemerlang,Baik,Memuaskan,Perlu Penambahbaikan',
            'catatan' => 'nullable|string',
            'soft_skills_sesi1_status' => 'nullable|in:Belum Mula,Dalam Perancangan,Selesai',
            'soft_skills_sesi1_tarikh' => 'nullable|date',
            'soft_skills_sesi1_peserta' => 'nullable|integer|min:0',
            'soft_skills_sesi2_status' => 'nullable|in:Belum Mula,Dirancang,Selesai',
            'soft_skills_sesi2_tarikh' => 'nullable|date',
            'soft_skills_sesi2_peserta' => 'nullable|integer|min:0',
        ]);

        $oldData = $syarikatPenempatan->toArray();
        $syarikatPenempatan->update($validated);

        AuditLog::log('syarikat_penempatan', 'update', $syarikatPenempatan->id_syarikat, $oldData, $syarikatPenempatan->fresh()->toArray());

        return redirect()->route('admin.syarikat-penempatan.show', $syarikatPenempatan)
            ->with('success', __('messages.penempatan_updated'));
    }

    public function destroy(SyarikatPenempatan $syarikatPenempatan)
    {
        if ($syarikatPenempatan->graduan()->exists()) {
            return redirect()->route('admin.syarikat-penempatan.show', $syarikatPenempatan)
                ->with('error', __('messages.penempatan_delete_blocked'));
        }

        $oldData = $syarikatPenempatan->toArray();
        $syarikatPenempatan->delete();

        AuditLog::log('syarikat_penempatan', 'delete', $syarikatPenempatan->id_syarikat, $oldData, null);

        return redirect()->route('admin.syarikat-penempatan.index')
            ->with('success', __('messages.penempatan_deleted'));
    }
}
