<?php

namespace App\Http\Controllers\Talent;

use App\Models\LogbookUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogbookController extends BaseTalentController
{
    public function index()
    {
        $talent = $this->getTalent();

        $logbooks = LogbookUpload::where('id_graduan', $talent->id_graduan)
            ->orderByDesc('tahun')
            ->orderByDesc('id')
            ->get();

        return view('talent.logbook.index', compact('talent', 'logbooks'));
    }

    public function upload(Request $request)
    {
        $talent = $this->getTalent();

        $request->validate([
            'bulan' => 'required|string',
            'tahun' => 'required|integer|min:2020|max:2030',
            'logbook_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('logbook_file');
        $folder = 'logbooks/' . $talent->id_graduan;
        $filename = "logbook_{$talent->id_graduan}_{$request->bulan}_{$request->tahun}_" . time() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($folder, $filename, 'public');

        if (!$path) {
            return back()->with('error', 'Failed to upload file.');
        }

        // Check if record exists for this month
        $existing = LogbookUpload::where('id_graduan', $talent->id_graduan)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existing) {
            $existing->update([
                'link_file_logbook' => $path,
                'file_name' => $file->getClientOriginalName(),
                'tarikh_upload' => now()->toDateString(),
                'status_logbook' => 'Dikemukakan',
            ]);
        } else {
            LogbookUpload::create([
                'id_graduan' => $talent->id_graduan,
                'nama_graduan' => $talent->full_name,
                'id_syarikat' => $talent->id_syarikat_penempatan,
                'nama_syarikat' => $talent->syarikatPenempatan?->nama_syarikat,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'status_logbook' => 'Dikemukakan',
                'tarikh_upload' => now()->toDateString(),
                'link_file_logbook' => $path,
                'file_name' => $file->getClientOriginalName(),
                'status_semakan' => 'Belum Disemak',
            ]);
        }

        return back()->with('success', __('talent.logbook_submitted'));
    }
}
