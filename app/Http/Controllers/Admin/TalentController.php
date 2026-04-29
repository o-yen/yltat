<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Talent;
use App\Models\TalentDocument;
use App\Models\TalentCertification;
use App\Models\AuditLog;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TalentController extends Controller
{
    private const TALENT_STATUSES = [
        'applied',
        'shortlisted',
        'approved',
        'assigned',
        'in_progress',
        'completed',
        'alumni',
        'inactive',
        'Aktif',
        'Tamat',
        'Berhenti Awal',
    ];

    public function index(Request $request)
    {
        $query = Talent::query();

        // syarikat_pelaksana can only view their own graduates
        $role = auth()->user()->role?->role_name;
        if ($role === 'syarikat_pelaksana') {
            $idPelaksana = auth()->user()->syarikatPelaksana?->id_pelaksana;
            $query->where('id_pelaksana', $idPelaksana ?: '__none__');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('id_graduan', 'like', "%{$search}%")
                  ->orWhere('talent_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ic_passport_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where(function ($statusQuery) use ($request) {
                $statusQuery->where('status', $request->status)
                    ->orWhere('status_aktif', $request->status);
            });
        }

        if ($request->filled('university')) {
            $query->where('university', 'like', "%{$request->university}%");
        }

        if ($request->filled('id_pelaksana')) {
            $query->where('id_pelaksana', $request->id_pelaksana);
        }

        if ($request->filled('id_syarikat_penempatan')) {
            $query->where('id_syarikat_penempatan', $request->id_syarikat_penempatan);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $talents = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $statuses = Talent::query()
            ->selectRaw('COALESCE(status_aktif, status) as display_status')
            ->where(function ($query) {
                $query->whereNotNull('status_aktif')
                    ->orWhereNotNull('status');
            })
            ->distinct()
            ->orderBy('display_status')
            ->pluck('display_status')
            ->values();

        // Summary stats for overview cards
        $stats = [
            'total'       => Talent::count(),
            'aktif'       => Talent::where('status_aktif', 'Aktif')->orWhere(fn($q) => $q->whereNull('status_aktif')->where('status', 'in_progress'))->count(),
            'tamat'       => Talent::where('status_aktif', 'Tamat')->orWhere(fn($q) => $q->whereNull('status_aktif')->where('status', 'completed'))->count(),
            'applied'     => Talent::where('status', 'applied')->count(),
            'berhenti'    => Talent::where('status_aktif', 'Berhenti Awal')->orWhere(fn($q) => $q->whereNull('status_aktif')->where('status', 'inactive'))->count(),
        ];

        // Category breakdown
        $categories = Talent::whereNotNull('kategori')
            ->selectRaw('kategori, COUNT(*) as count')
            ->groupBy('kategori')
            ->pluck('count', 'kategori');

        return view('admin.talents.index', compact('talents', 'statuses', 'stats', 'categories'));
    }

    public function create()
    {
        return view('admin.talents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:200',
            'ic_passport_no' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'kelayakan' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'negeri' => 'nullable|string|max:100',
            'university' => 'nullable|string|max:200',
            'programme' => 'nullable|string|max:200',
            'cgpa' => 'nullable|numeric|min:0|max:4',
            'graduation_year' => 'nullable|integer|digits:4|min:1900|max:2100',
            'skills_text' => 'nullable|string',
            'profile_summary' => 'nullable|string',
            'public_visibility' => 'boolean',
            'status' => ['required', Rule::in(self::TALENT_STATUSES)],
            'notes' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // PROTEGE fields
            'id_graduan' => 'nullable|string|max:20|unique:talents,id_graduan',
            'kategori' => 'nullable|string|max:50',
            'status_penyerapan_6bulan' => 'nullable|in:Diserap,Tidak Diserap,Belum Layak',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'id_syarikat_penempatan' => 'nullable|exists:syarikat_penempatan,id_syarikat',
            'jawatan' => 'nullable|string|max:200',
            'tarikh_mula' => 'nullable|date',
            'tarikh_tamat' => 'nullable|date|after_or_equal:tarikh_mula',
            'status_aktif' => 'nullable|string|max:30',
            // Placement fields (merged)
            'department' => 'nullable|string|max:200',
            'supervisor_name' => 'nullable|string|max:200',
            'supervisor_email' => 'nullable|email|max:200',
            'duration_months' => 'nullable|integer|min:1|max:36',
            'monthly_stipend' => 'nullable|numeric|min:0',
            'additional_cost' => 'nullable|numeric|min:0',
            'programme_type' => 'nullable|string|max:100',
        ]);

        $validated['talent_code'] = Talent::generateCode();
        $validated['public_visibility'] = $request->boolean('public_visibility', true);

        $talent = Talent::create($validated);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store("photos/{$talent->id}", 'public');
            $talent->update(['profile_photo' => $path]);
        }

        // Handle document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                $docType = $request->input("document_types.{$index}", 'other');
                $path = $file->store("documents/{$talent->id}", 'public');

                TalentDocument::create([
                    'talent_id' => $talent->id,
                    'document_type' => $docType,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }
        }

        AuditLog::log('talents', 'create', $talent->id, null, $talent->toArray());

        return redirect()->route('admin.talents.show', $talent)
            ->with('success', __('messages.talent_created'));
    }

    public function show(Talent $talent)
    {
        $talent->load(['documents', 'certifications', 'placements.company', 'placements.feedback', 'syarikatPelaksana', 'syarikatPenempatan']);

        $feedbackData = $talent->placements->flatMap(fn($p) => $p->feedback);
        $transactions = $talent->budgetTransactions()->orderByDesc('transaction_date')->get();

        // PROTEGE monitoring data for this graduate
        $gradId = $talent->id_graduan ?? $talent->talent_code;

        $kehadiranRecords = \App\Models\KehadiranPrestasi::where('id_graduan', $gradId)
            ->orderBy('tahun')->orderBy('bulan')->get();

        $logbookRecords = \App\Models\LogbookUpload::where('id_graduan', $gradId)
            ->orderBy('tahun')->orderBy('bulan')->get();

        $suratRecords = \App\Models\StatusSurat::where('id_graduan', $gradId)
            ->orderByDesc('created_at')->get();

        $kewanganRecords = \App\Models\KewanganElaun::where('id_graduan', $gradId)
            ->orderBy('tahun')->orderBy('bulan')->get();

        $trainingRecords = \App\Models\TrainingParticipant::where('id_graduan', $gradId)
            ->with('trainingRecord')
            ->get();

        return view('admin.talents.show', compact(
            'talent', 'feedbackData', 'transactions',
            'kehadiranRecords', 'logbookRecords', 'suratRecords', 'kewanganRecords', 'trainingRecords'
        ));
    }

    public function edit(Talent $talent)
    {
        $talent->load(['documents', 'certifications']);

        $pelaksanaOptions = SyarikatPelaksana::query()
            ->orderBy('nama_syarikat')
            ->get(['id_pelaksana', 'nama_syarikat']);

        $penempatanOptions = SyarikatPenempatan::query()
            ->orderBy('nama_syarikat')
            ->get(['id_syarikat', 'nama_syarikat']);

        return view('admin.talents.edit', compact('talent', 'pelaksanaOptions', 'penempatanOptions'));
    }

    public function update(Request $request, Talent $talent)
    {
        $validated = $request->validate([
            'id_graduan' => ['nullable', 'string', 'max:20', Rule::unique('talents', 'id_graduan')->ignore($talent->id)],
            'full_name' => 'required|string|max:200',
            'ic_passport_no' => 'required|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'kelayakan' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'negeri' => 'nullable|string|max:100',
            'university' => 'nullable|string|max:200',
            'programme' => 'nullable|string|max:200',
            'cgpa' => 'nullable|numeric|min:0|max:4',
            'graduation_year' => 'nullable|integer|digits:4|min:1900|max:2100',
            'skills_text' => 'nullable|string',
            'profile_summary' => 'nullable|string',
            'background_type' => 'nullable|in:anak_atm,anak_veteran_atm,anak_awam_mindef',
            'guardian_name' => 'nullable|string|max:200',
            'guardian_ic' => 'nullable|string|max:50',
            'guardian_military_no' => 'nullable|string|max:50',
            'guardian_relationship' => 'nullable|string|max:100',
            'highest_qualification' => 'nullable|in:diploma,ijazah,sarjana,phd,lain',
            'preferred_sectors_text' => 'nullable|string',
            'preferred_locations_text' => 'nullable|string',
            'currently_employed' => 'boolean',
            'available_start_date' => 'nullable|date',
            'pdpa_consent' => 'boolean',
            'declaration_signature' => 'nullable|string|max:255',
            'rejection_reason' => 'nullable|string',
            'public_visibility' => 'boolean',
            'status' => ['required', Rule::in(self::TALENT_STATUSES)],
            'kategori' => 'nullable|string|max:50',
            'status_penyerapan_6bulan' => 'nullable|in:Diserap,Tidak Diserap,Belum Layak,Dalam Proses',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana',
            'id_syarikat_penempatan' => 'nullable|exists:syarikat_penempatan,id_syarikat',
            'jawatan' => 'nullable|string|max:200',
            'tarikh_mula' => 'nullable|date',
            'tarikh_tamat' => 'nullable|date|after_or_equal:tarikh_mula',
            'status_aktif' => 'nullable|string|max:30',
            // Placement fields (merged)
            'department' => 'nullable|string|max:200',
            'supervisor_name' => 'nullable|string|max:200',
            'supervisor_email' => 'nullable|email|max:200',
            'duration_months' => 'nullable|integer|min:0|max:36',
            'monthly_stipend' => 'nullable|numeric|min:0',
            'additional_cost' => 'nullable|numeric|min:0',
            'programme_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['public_visibility'] = $request->boolean('public_visibility', true);
        $validated['currently_employed'] = $request->boolean('currently_employed');
        $validated['pdpa_consent'] = $request->boolean('pdpa_consent');
        $validated['preferred_sectors'] = $this->parseTagList($request->input('preferred_sectors_text'));
        $validated['preferred_locations'] = $this->parseTagList($request->input('preferred_locations_text'));
        // Sync background_type ↔ kategori (keep both in sync)
        $bgToKat = ['anak_atm' => 'Anak ATM', 'anak_veteran_atm' => 'Anak Veteran', 'anak_awam_mindef' => 'Anak Awam MINDEF'];
        $katToBg = array_flip($bgToKat);

        if (!empty($validated['background_type']) && isset($bgToKat[$validated['background_type']])) {
            $validated['kategori'] = $bgToKat[$validated['background_type']];
        } elseif (!empty($validated['kategori']) && isset($katToBg[$validated['kategori']])) {
            $validated['background_type'] = $katToBg[$validated['kategori']];
        }

        // Sync kelayakan ↔ highest_qualification
        $qualMap = ['diploma' => 'Diploma', 'ijazah' => 'Ijazah Sarjana Muda', 'sarjana' => 'Ijazah Sarjana', 'phd' => 'PhD', 'lain' => 'Lain-lain'];
        if (!empty($validated['highest_qualification']) && isset($qualMap[$validated['highest_qualification']])) {
            $validated['kelayakan'] = $qualMap[$validated['highest_qualification']];
        }
        unset($validated['preferred_sectors_text'], $validated['preferred_locations_text']);

        $oldData = $talent->toArray();
        $talent->update($validated);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($talent->profile_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($talent->profile_photo);
            }
            $path = $request->file('profile_photo')->store("photos/{$talent->id}", 'public');
            $talent->update(['profile_photo' => $path]);
        }

        // Handle new document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                $docType = $request->input("document_types.{$index}", 'other');
                $path = $file->store("documents/{$talent->id}", 'public');

                TalentDocument::create([
                    'talent_id' => $talent->id,
                    'document_type' => $docType,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'uploaded_at' => now(),
                ]);
            }
        }

        // Handle certifications
        if ($request->filled('cert_name')) {
            foreach ($request->cert_name as $i => $certName) {
                if (!empty($certName)) {
                    TalentCertification::create([
                        'talent_id' => $talent->id,
                        'certification_name' => $certName,
                        'issuer' => $request->cert_issuer[$i] ?? null,
                        'issue_date' => $request->cert_issue_date[$i] ?? null,
                        'expiry_date' => $request->cert_expiry_date[$i] ?? null,
                    ]);
                }
            }
        }

        AuditLog::log('talents', 'update', $talent->id, $oldData, $talent->fresh()->toArray());

        return redirect()->route('admin.talents.show', $talent)
            ->with('success', __('messages.talent_updated'));
    }

    public function destroy(Talent $talent)
    {
        if ($talent->placements()->exists()) {
            return redirect()->route('admin.talents.show', $talent)
                ->with('error', __('messages.talent_delete_blocked_by_placements'));
        }

        $oldData = $talent->toArray();
        $talent->delete();

        AuditLog::log('talents', 'delete', $talent->id, $oldData, null);

        return redirect()->route('admin.talents.index')
            ->with('success', __('messages.talent_deleted'));
    }

    public function deleteDocument(TalentDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', __('messages.document_deleted'));
    }

    private function parseTagList(?string $value): array
    {
        if (blank($value)) {
            return [];
        }

        return collect(preg_split('/[\n,]+/', $value))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    private function mapKategoriToBackgroundType(?string $kategori): ?string
    {
        return match ($kategori) {
            'Anak ATM' => 'anak_atm',
            'Anak Veteran', 'Anak Veteran ATM' => 'anak_veteran_atm',
            'Anak Awam MINDEF', 'Anak Kakitangan Awam MINDEF' => 'anak_awam_mindef',
            default => null,
        };
    }

    private function mapBackgroundTypeToKategori(?string $backgroundType): ?string
    {
        return match ($backgroundType) {
            'anak_atm' => 'Anak ATM',
            'anak_veteran_atm' => 'Anak Veteran',
            'anak_awam_mindef' => 'Anak Awam MINDEF',
            default => null,
        };
    }

    private function mapQualificationTextToCode(?string $qualification): ?string
    {
        if (blank($qualification)) {
            return null;
        }

        $normalized = strtolower(trim($qualification));

        return match (true) {
            str_contains($normalized, 'diploma') => 'diploma',
            str_contains($normalized, 'sarjana') => 'sarjana',
            str_contains($normalized, 'phd') => 'phd',
            str_contains($normalized, 'ijazah') => 'ijazah',
            default => 'lain',
        };
    }
}
