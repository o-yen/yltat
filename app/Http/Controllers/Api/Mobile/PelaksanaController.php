<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\KewanganElaun;
use App\Models\StatusSurat;
use App\Models\IsuRisiko;
use App\Models\LogbookUpload;
use App\Models\SyarikatPelaksana;
use App\Models\Talent;
use Illuminate\Http\Request;

class PelaksanaController extends BaseMobileController
{
    private function pelaksanaId(): ?string
    {
        return auth()->user()?->id_pelaksana;
    }

    private function pelaksana(): ?SyarikatPelaksana
    {
        return SyarikatPelaksana::find($this->pelaksanaId());
    }

    public function dashboard(Request $request)
    {
        $sp = $this->pelaksana();
        $pid = $this->pelaksanaId();

        $graduanCount = Talent::where('id_pelaksana', $pid)->count();
        $bayaranSelesai = KewanganElaun::where('id_pelaksana', $pid)->where('status_bayaran', 'Selesai')->count();
        $bayaranLewat = KewanganElaun::where('id_pelaksana', $pid)->where('status_bayaran', 'Lewat')->count();
        $suratKuningDone = StatusSurat::where('id_pelaksana', $pid)->where('jenis_surat', 'Surat Kuning')->where('status_surat', 'Selesai')->count();
        $suratKuningTotal = StatusSurat::where('id_pelaksana', $pid)->where('jenis_surat', 'Surat Kuning')->count();
        $suratBiruDone = StatusSurat::where('id_pelaksana', $pid)->where('jenis_surat', 'Surat Biru')->where('status_surat', 'Selesai')->count();
        $suratBiruTotal = StatusSurat::where('id_pelaksana', $pid)->where('jenis_surat', 'Surat Biru')->count();
        $activeIssues = IsuRisiko::where('id_pelaksana', $pid)->whereIn('status', ['Baru', 'Dalam Tindakan'])->count();

        return $this->success([
            'company' => [
                'id_pelaksana' => $sp?->id_pelaksana,
                'name' => $sp?->nama_syarikat,
                'project' => $sp?->projek_kontrak,
                'quota_used' => $sp?->kuota_digunakan,
                'quota_approved' => $sp?->kuota_diluluskan,
                'budget_allocated' => $sp?->peruntukan_diluluskan,
                'budget_used' => $sp?->peruntukan_diguna,
                'budget_balance' => $sp?->baki_peruntukan,
                'fund_status' => $sp?->status_dana,
                'compliance' => $sp?->tahap_pematuhan,
            ],
            'stats' => [
                'graduates' => $graduanCount,
                'payments_done' => $bayaranSelesai,
                'payments_late' => $bayaranLewat,
                'surat_kuning' => ['done' => $suratKuningDone, 'total' => $suratKuningTotal],
                'surat_biru' => ['done' => $suratBiruDone, 'total' => $suratBiruTotal],
                'active_issues' => $activeIssues,
            ],
        ]);
    }

    public function graduates(Request $request)
    {
        $pid = $this->pelaksanaId();
        $query = Talent::where('id_pelaksana', $pid)
            ->with('syarikatPenempatan');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('full_name', 'like', "%$s%")->orWhere('id_graduan', 'like', "%$s%"));
        }

        $talents = $query->orderByDesc('updated_at')->paginate(20);

        return $this->success([
            'items' => $talents->map(fn($t) => [
                'id' => $t->id,
                'id_graduan' => $t->id_graduan,
                'full_name' => $t->full_name,
                'kategori' => $t->kategori,
                'status' => $t->status_aktif ?? $t->status,
                'placement_company' => $t->syarikatPenempatan?->nama_syarikat,
                'jawatan' => $t->jawatan,
                'start_date' => $t->tarikh_mula?->toDateString(),
                'end_date' => $t->tarikh_tamat?->toDateString(),
            ])->values(),
            'pagination' => [
                'current_page' => $talents->currentPage(),
                'last_page' => $talents->lastPage(),
                'total' => $talents->total(),
            ],
        ]);
    }

    public function kewangan(Request $request)
    {
        $pid = $this->pelaksanaId();
        $query = KewanganElaun::where('id_pelaksana', $pid);

        if ($request->filled('status_bayaran')) {
            $query->where('status_bayaran', $request->status_bayaran);
        }
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        $records = $query->orderByDesc('id')->paginate(20);

        return $this->success([
            'items' => $records->map(fn($r) => [
                'id' => $r->id,
                'id_graduan' => $r->id_graduan,
                'month' => $r->bulan,
                'year' => $r->tahun,
                'full_allowance' => $r->elaun_penuh,
                'prorate_allowance' => $r->elaun_prorate,
                'working_days' => $r->hari_bekerja_sebenar,
                'days_in_month' => $r->hari_dalam_bulan,
                'status' => $r->status_bayaran,
                'payment_date' => $r->tarikh_bayar?->toDateString(),
                'expected_date' => $r->tarikh_jangka_bayar?->toDateString(),
                'days_late' => $r->hari_lewat,
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function managePlacement(Request $request)
    {
        $pid = $this->pelaksanaId();
        $query = Talent::where('id_pelaksana', $pid)
            ->with('syarikatPenempatan');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                    ->orWhere('id_graduan', 'like', "%{$s}%")
                    ->orWhere('jawatan', 'like', "%{$s}%")
                    ->orWhere('department', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }

        $records = $query->orderByDesc('updated_at')->paginate(20);

        return $this->success([
            'items' => $records->map(fn($t) => [
                'id' => $t->id,
                'id_graduan' => $t->id_graduan,
                'full_name' => $t->full_name,
                'status' => $t->status_aktif ?? $t->status,
                'placement_company' => $t->syarikatPenempatan?->nama_syarikat,
                'job_title' => $t->jawatan,
                'department' => $t->department,
                'supervisor_name' => $t->supervisor_name,
                'supervisor_email' => $t->supervisor_email,
                'programme_type' => $t->programme_type,
                'start_date' => $t->tarikh_mula?->toDateString(),
                'end_date' => $t->tarikh_tamat?->toDateString(),
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function logbook(Request $request)
    {
        $pid = $this->pelaksanaId();
        $graduateIds = Talent::where('id_pelaksana', $pid)->pluck('id_graduan');

        $query = LogbookUpload::whereIn('id_graduan', $graduateIds)
            ->with('syarikatPenempatan');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_graduan', 'like', "%{$s}%")
                    ->orWhere('id_graduan', 'like', "%{$s}%")
                    ->orWhere('nama_syarikat', 'like', "%{$s}%");
            });
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('status_logbook')) {
            $query->where('status_logbook', $request->status_logbook);
        }

        if ($request->filled('status_semakan')) {
            $query->where('status_semakan', $request->status_semakan);
        }

        $records = $query->orderByDesc('tahun')->orderByDesc('id')->paginate(20);

        return $this->success([
            'items' => $records->map(fn($r) => [
                'id' => $r->id,
                'graduate_name' => $r->nama_graduan,
                'graduate_id' => $r->id_graduan,
                'company_name' => $r->syarikatPenempatan?->nama_syarikat ?? $r->nama_syarikat,
                'month' => $r->bulan,
                'year' => $r->tahun,
                'status_logbook' => $r->status_logbook,
                'status_review' => $r->status_semakan,
                'mentor_name' => $r->nama_mentor,
                'review_date' => $r->tarikh_semakan?->toDateString(),
                'file_url' => $r->link_file_logbook ? asset('storage/' . $r->link_file_logbook) : null,
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function statusSurat(Request $request)
    {
        $pid = $this->pelaksanaId();
        $query = StatusSurat::where('id_pelaksana', $pid);

        if ($request->filled('jenis_surat')) {
            $query->where('jenis_surat', $request->jenis_surat);
        }
        if ($request->filled('status_surat')) {
            $query->where('status_surat', $request->status_surat);
        }

        $records = $query->orderByDesc('created_at')->paginate(20);

        return $this->success([
            'items' => $records->map(fn($s) => [
                'id' => $s->id,
                'type' => $s->jenis_surat,
                'graduate_name' => $s->nama_graduan,
                'graduate_id' => $s->id_graduan,
                'status' => $s->status_surat,
                'pic' => $s->pic_responsible,
                'start_date' => $s->tarikh_mula_proses?->toDateString(),
                'completed_date' => $s->tarikh_siap?->toDateString(),
                'has_attachment' => !empty($s->file_attachment),
                'issue' => $s->isu_halangan,
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function issues(Request $request)
    {
        $pid = $this->pelaksanaId();
        $records = IsuRisiko::where('id_pelaksana', $pid)
            ->orderByDesc('tarikh_isu')
            ->paginate(20);

        return $this->success([
            'items' => $records->map(fn($i) => [
                'id' => $i->id_isu,
                'date' => $i->tarikh_isu?->toDateString(),
                'category' => $i->kategori_isu,
                'severity' => $i->tahap_risiko,
                'status' => $i->status,
                'description' => \Illuminate\Support\Str::limit($i->butiran_isu, 100),
                'pic' => $i->pic,
                'action_taken' => $i->tindakan_diambil,
            ])->values(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function profile()
    {
        $sp = $this->pelaksana();

        return $this->success([
            'id_pelaksana' => $sp?->id_pelaksana,
            'name' => $sp?->nama_syarikat,
            'project' => $sp?->projek_kontrak,
            'pic_name' => $sp?->pic_syarikat,
            'pic_email' => $sp?->email_pic,
            'quota_obligation' => $sp?->jumlah_kuota_obligasi,
            'quota_approved' => $sp?->kuota_diluluskan,
            'quota_used' => $sp?->kuota_digunakan,
            'budget_allocated' => $sp?->peruntukan_diluluskan,
            'budget_used' => $sp?->peruntukan_diguna,
            'budget_balance' => $sp?->baki_peruntukan,
            'fund_status' => $sp?->status_dana,
            'compliance' => $sp?->tahap_pematuhan,
            'surat_kuning_status' => $sp?->status_surat_kuning,
            'surat_biru_status' => $sp?->status_surat_biru,
        ]);
    }
}
