<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LogbookReviewMail;
use App\Models\LogbookUpload;
use App\Models\SyarikatPenempatan;
use App\Models\Talent;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LogbookUploadController extends Controller
{
    public function index(Request $request)
    {
        $query = LogbookUpload::with('syarikatPenempatan');

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
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_graduan', 'like', "%{$search}%")
                  ->orWhere('nama_graduan', 'like', "%{$search}%");
            });
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        if ($request->filled('id_syarikat')) {
            $query->where('id_syarikat', $request->id_syarikat);
        }
        if ($request->filled('status_logbook')) {
            $query->where('status_logbook', $request->status_logbook);
        }
        if ($request->filled('status_semakan')) {
            $query->where('status_semakan', $request->status_semakan);
        }

        $records = $query->orderByDesc('tahun')->orderByDesc('id')->paginate(20)->withQueryString();
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        $bulanList = LogbookUpload::distinct()->pluck('bulan')->sort()->values();
        $isCompanyRole = in_array($role, ['rakan_kolaborasi', 'syarikat_pelaksana']);

        return view('admin.logbook.index', compact('records', 'penempatan', 'bulanList', 'isCompanyRole'));
    }

    public function create()
    {
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        $talents = \App\Models\Talent::with(['syarikatPenempatan:id_syarikat,nama_syarikat'])
            ->orderBy('full_name')
            ->get(['id_graduan', 'full_name', 'talent_code', 'id_pelaksana', 'id_syarikat_penempatan']);
        return view('admin.logbook.create', compact('penempatan', 'talents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_graduan' => 'required|string|max:20',
            'nama_graduan' => 'required|string|max:255',
            'id_syarikat' => 'nullable|exists:syarikat_penempatan,id_syarikat',
            'nama_syarikat' => 'nullable|string|max:255',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'status_logbook' => 'required|in:Dikemukakan,Dalam Semakan,Lewat,Belum Dikemukakan',
            'tarikh_upload' => 'nullable|date',
            'link_file_logbook' => 'nullable|string|max:500',
            'status_semakan' => 'required|in:Lulus,Dalam Proses,Perlu Semakan Semula,Belum Disemak',
            'komen_mentor' => 'nullable|string',
            'tarikh_semakan' => 'nullable|date',
            'nama_mentor' => 'nullable|string|max:200',
        ]);

        $record = LogbookUpload::create($validated);
        AuditLog::log('logbook_uploads', 'create', $record->id, null, $record->toArray());

        return redirect()->route('admin.logbook.index')->with('success', __('messages.logbook_created'));
    }

    public function show(LogbookUpload $logbook)
    {
        $this->authorizeCompanyAccess($logbook);
        $logbook->load('syarikatPenempatan');
        return view('admin.logbook.show', compact('logbook'));
    }

    public function edit(LogbookUpload $logbook)
    {
        $this->authorizeCompanyAccess($logbook);
        $penempatan = SyarikatPenempatan::orderBy('nama_syarikat')->get();
        $talents = \App\Models\Talent::with(['syarikatPenempatan:id_syarikat,nama_syarikat'])
            ->orderBy('full_name')
            ->get(['id_graduan', 'full_name', 'talent_code', 'id_pelaksana', 'id_syarikat_penempatan']);
        return view('admin.logbook.edit', compact('logbook', 'penempatan', 'talents'));
    }

    public function update(Request $request, LogbookUpload $logbook)
    {
        $this->authorizeCompanyAccess($logbook);
        $validated = $request->validate([
            'id_graduan' => 'required|string|max:20',
            'nama_graduan' => 'required|string|max:255',
            'id_syarikat' => 'nullable|exists:syarikat_penempatan,id_syarikat',
            'nama_syarikat' => 'nullable|string|max:255',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'status_logbook' => 'required|in:Dikemukakan,Dalam Semakan,Lewat,Belum Dikemukakan',
            'tarikh_upload' => 'nullable|date',
            'link_file_logbook' => 'nullable|string|max:500',
            'status_semakan' => 'required|in:Lulus,Dalam Proses,Perlu Semakan Semula,Belum Disemak',
            'komen_mentor' => 'nullable|string',
            'tarikh_semakan' => 'nullable|date',
            'nama_mentor' => 'nullable|string|max:200',
        ]);

        $oldReview = $logbook->status_semakan;
        $oldData = $logbook->toArray();
        $logbook->update($validated);
        AuditLog::log('logbook_uploads', 'update', $logbook->id, $oldData, $logbook->fresh()->toArray());

        // Notify talent when review status changes to Lulus or Perlu Semakan Semula
        $newReview = $validated['status_semakan'];
        if ($newReview !== $oldReview && in_array($newReview, ['Lulus', 'Perlu Semakan Semula'])) {
            try {
                $talent = Talent::where('id_graduan', $logbook->id_graduan)->first();
                if ($talent?->email) {
                    Mail::to($talent->email)->send(new LogbookReviewMail($logbook->fresh(), $newReview));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('admin.logbook.index')->with('success', __('messages.logbook_updated'));
    }

    public function uploadFile(Request $request, LogbookUpload $logbook)
    {
        $this->authorizeCompanyAccess($logbook);

        $request->validate([
            'logbook_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('logbook_file');
        $folder = 'logbooks/' . ($logbook->id_graduan ?: 'general');
        $filename = "logbook_{$logbook->id}_" . time() . '.' . $file->getClientOriginalExtension();

        try {
            $path = $file->storeAs($folder, $filename, 'public');
            if (!$path) {
                return back()->with('error', 'Failed to store file.');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }

        $logbook->update([
            'link_file_logbook' => $path,
            'file_name' => $file->getClientOriginalName(),
            'tarikh_upload' => now()->toDateString(),
            'status_logbook' => $logbook->status_logbook === 'Belum Dikemukakan' ? 'Dikemukakan' : $logbook->status_logbook,
        ]);

        AuditLog::log('logbook_uploads', 'upload', $logbook->id, null, ['file' => $path]);

        return back()->with('success', __('protege.file_uploaded'));
    }

    private function authorizeCompanyAccess(LogbookUpload $logbook): void
    {
        $user = auth()->user();
        $role = $user->role?->role_name;
        $graduan = $logbook->id_graduan;

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
