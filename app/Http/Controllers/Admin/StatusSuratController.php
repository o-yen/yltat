<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SuratIssuedMail;
use App\Models\StatusSurat;
use App\Models\SyarikatPelaksana;
use App\Models\Talent;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StatusSuratController extends Controller
{
    public function index(Request $request)
    {
        $query = StatusSurat::with('syarikatPelaksana');

        // syarikat_pelaksana can only view/edit records for their own graduates
        $role = auth()->user()->role?->role_name;
        if ($role === 'syarikat_pelaksana') {
            $idPelaksana = auth()->user()->syarikatPelaksana?->id_pelaksana;
            $query->where('id_pelaksana', $idPelaksana ?: '__none__');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_graduan', 'like', "%{$search}%")
                  ->orWhere('nama_graduan', 'like', "%{$search}%")
                  ->orWhere('pic_responsible', 'like', "%{$search}%");
            });
        }
        if ($request->filled('jenis_surat')) {
            $query->where('jenis_surat', $request->jenis_surat);
        }
        if ($request->filled('status_surat')) {
            $query->where('status_surat', $request->status_surat);
        }
        if ($request->filled('id_pelaksana')) {
            $query->where('id_pelaksana', $request->id_pelaksana);
        }

        $records = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();

        // Summary counts
        $totalKuning = StatusSurat::where('jenis_surat', 'Surat Kuning')->count();
        $kuningSelesai = StatusSurat::where('jenis_surat', 'Surat Kuning')->where('status_surat', 'Selesai')->count();
        $totalBiru = StatusSurat::where('jenis_surat', 'Surat Biru')->count();
        $biruSelesai = StatusSurat::where('jenis_surat', 'Surat Biru')->where('status_surat', 'Selesai')->count();

        return view('admin.status-surat.index', compact('records', 'pelaksana', 'totalKuning', 'kuningSelesai', 'totalBiru', 'biruSelesai'));
    }

    public function create()
    {
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $talentsJson = Talent::select('id_graduan', 'talent_code', 'full_name', 'id_pelaksana')
            ->orderBy('full_name')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id_graduan ?? $t->talent_code,
                    'name' => $t->full_name,
                    'pelaksana' => $t->id_pelaksana ?? '',
                ];
            })
            ->values();
        return view('admin.status-surat.create', compact('pelaksana', 'talentsJson'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pelaksana' => 'required|exists:syarikat_pelaksana,id_pelaksana',
            'jenis_surat' => 'required|in:Surat Kuning,Surat Biru',
            'id_graduan' => 'nullable|string|max:20',
            'nama_graduan' => 'nullable|string|max:255',
            'status_surat' => 'required|in:Belum Mula,Draft,Semakan,Tandatangan,Hantar,Selesai',
            'tarikh_mula_proses' => 'nullable|date',
            'tarikh_draft' => 'nullable|date',
            'tarikh_semakan' => 'nullable|date',
            'tarikh_tandatangan' => 'nullable|date',
            'tarikh_hantar' => 'nullable|date',
            'tarikh_siap' => 'nullable|date',
            'pic_responsible' => 'required|string|max:200',
            'isu_halangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // Auto-fill nama_graduan from talent if not provided
        if (!empty($validated['id_graduan']) && empty($validated['nama_graduan'])) {
            $talent = Talent::where('id_graduan', $validated['id_graduan'])
                ->orWhere('talent_code', $validated['id_graduan'])
                ->first();
            $validated['nama_graduan'] = $talent?->full_name ?? $validated['nama_graduan'];
        }

        $record = StatusSurat::create($validated);
        AuditLog::log('status_surat', 'create', $record->id, null, $record->toArray());

        return redirect()->route('admin.status-surat.index')->with('success', __('messages.surat_created'));
    }

    public function show(StatusSurat $statusSurat)
    {
        $statusSurat->load('syarikatPelaksana');
        return view('admin.status-surat.show', compact('statusSurat'));
    }

    public function edit(StatusSurat $statusSurat)
    {
        $pelaksana = SyarikatPelaksana::orderBy('nama_syarikat')->get();
        $talentsJson = Talent::select('id_graduan', 'talent_code', 'full_name', 'id_pelaksana')
            ->orderBy('full_name')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id_graduan ?? $t->talent_code,
                    'name' => $t->full_name,
                    'pelaksana' => $t->id_pelaksana ?? '',
                ];
            })
            ->values();
        return view('admin.status-surat.edit', compact('statusSurat', 'pelaksana', 'talentsJson'));
    }

    public function update(Request $request, StatusSurat $statusSurat)
    {
        $validated = $request->validate([
            'id_pelaksana' => 'required|exists:syarikat_pelaksana,id_pelaksana',
            'jenis_surat' => 'required|in:Surat Kuning,Surat Biru',
            'id_graduan' => 'nullable|string|max:20',
            'nama_graduan' => 'nullable|string|max:255',
            'status_surat' => 'required|in:Belum Mula,Draft,Semakan,Tandatangan,Hantar,Selesai',
            'tarikh_mula_proses' => 'nullable|date',
            'tarikh_draft' => 'nullable|date',
            'tarikh_semakan' => 'nullable|date',
            'tarikh_tandatangan' => 'nullable|date',
            'tarikh_hantar' => 'nullable|date',
            'tarikh_siap' => 'nullable|date',
            'pic_responsible' => 'required|string|max:200',
            'isu_halangan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $oldData = $statusSurat->toArray();
        $statusSurat->update($validated);
        AuditLog::log('status_surat', 'update', $statusSurat->id, $oldData, $statusSurat->fresh()->toArray());

        return redirect()->route('admin.status-surat.index')->with('success', __('messages.surat_updated'));
    }

    /**
     * Advance surat workflow to next step (called via AJAX or form from Pelaksana page).
     */
    public function advanceStatus(Request $request, StatusSurat $statusSurat)
    {
        $workflow = StatusSurat::WORKFLOW;
        $currentIdx = array_search($statusSurat->status_surat, $workflow);

        if ($currentIdx === false || $currentIdx >= count($workflow) - 1) {
            return back()->with('error', 'Surat sudah selesai atau status tidak sah.');
        }

        $nextStatus = $workflow[$currentIdx + 1];
        $dateField = match ($nextStatus) {
            'Draft' => 'tarikh_draft',
            'Semakan' => 'tarikh_semakan',
            'Tandatangan' => 'tarikh_tandatangan',
            'Hantar' => 'tarikh_hantar',
            'Selesai' => 'tarikh_siap',
            default => null,
        };

        $oldData = $statusSurat->toArray();

        $updateData = ['status_surat' => $nextStatus];
        if ($dateField) {
            $updateData[$dateField] = now()->toDateString();
        }

        // Handle file attachment
        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $filename = "surat_{$statusSurat->id}_{$nextStatus}_" . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('surat/' . $statusSurat->id_pelaksana, $filename, 'public');
            $updateData['file_attachment'] = $path;
            $updateData['file_name'] = $file->getClientOriginalName();
        }

        $statusSurat->update($updateData);

        AuditLog::log('status_surat', 'advance', $statusSurat->id, $oldData, $statusSurat->fresh()->toArray());

        // Send email when letter is sent (Hantar stage) — notify talent + pelaksana
        if ($nextStatus === 'Hantar') {
            try {
                // Notify talent
                $talent = Talent::where('id_graduan', $statusSurat->id_graduan)->first();
                if ($talent?->email) {
                    Mail::to($talent->email)->send(new SuratIssuedMail($statusSurat, $talent->full_name));
                }
                // Notify pelaksana
                $pelaksana = SyarikatPelaksana::where('id_pelaksana', $statusSurat->id_pelaksana)->first();
                if ($pelaksana?->email_pic) {
                    Mail::to($pelaksana->email_pic)->send(new SuratIssuedMail($statusSurat, $pelaksana->pic_syarikat ?? $pelaksana->nama_syarikat));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', "Status surat dikemaskini ke: {$nextStatus}");
    }

    /**
     * Upload/replace attachment for a surat.
     */
    public function uploadAttachment(Request $request, StatusSurat $statusSurat)
    {
        $request->validate([
            'file_attachment' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file_attachment');
        $folder = 'surat/' . ($statusSurat->id_pelaksana ?: 'general');
        $filename = "surat_{$statusSurat->id}_" . time() . '.' . $file->getClientOriginalExtension();

        try {
            $path = $file->storeAs($folder, $filename, 'public');
            if (!$path) {
                return back()->with('error', 'Failed to store file.');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }

        $statusSurat->update([
            'file_attachment' => $path,
            'file_name' => $file->getClientOriginalName(),
        ]);

        AuditLog::log('status_surat', 'upload', $statusSurat->id, null, ['file' => $path]);

        return back()->with('success', __('protege.file_uploaded'));
    }
}
