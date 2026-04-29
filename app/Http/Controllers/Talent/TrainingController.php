<?php

namespace App\Http\Controllers\Talent;

use App\Models\TrainingParticipant;
use App\Models\TrainingRecord;
use Illuminate\Http\Request;

class TrainingController extends BaseTalentController
{
    public function index()
    {
        $talent = $this->getTalent();

        // Trainings the graduate is already enrolled in
        $myTrainings = TrainingParticipant::with('trainingRecord')
            ->where('id_graduan', $talent->id_graduan)
            ->orderByDesc('created_at')
            ->get();

        $joinedTrainingIds = $myTrainings->pluck('id_training')->filter()->toArray();

        // Available trainings from the graduate's placement company (not yet joined, not cancelled)
        $availableTrainings = collect();
        $companyId = $talent->id_syarikat_penempatan;
        if ($companyId) {
            $availableTrainings = TrainingRecord::where('id_syarikat', $companyId)
                ->whereNotIn('id_training', $joinedTrainingIds)
                ->whereIn('status', ['Dirancang', 'Dalam Proses'])
                ->orderByDesc('tarikh_training')
                ->get();
        }

        return view('talent.training.index', compact('talent', 'myTrainings', 'availableTrainings'));
    }

    public function join(Request $request, TrainingRecord $training)
    {
        $talent = $this->getTalent();

        // Verify training belongs to the graduate's company
        if ($training->id_syarikat !== $talent->id_syarikat_penempatan) {
            return back()->with('error', __('messages.training_not_available'));
        }

        // Check not already joined
        $exists = TrainingParticipant::where('id_training', $training->id_training)
            ->where('id_graduan', $talent->id_graduan)
            ->exists();

        if ($exists) {
            return back()->with('error', __('messages.participant_already_exists'));
        }

        TrainingParticipant::create([
            'id_record' => TrainingParticipant::generateId(),
            'id_training' => $training->id_training,
            'id_graduan' => $talent->id_graduan,
            'nama_graduan' => $talent->full_name,
            'status_kehadiran' => 'Hadir',
        ]);

        // Update training attendance counts
        $count = TrainingParticipant::where('id_training', $training->id_training)->count();
        $hadir = TrainingParticipant::where('id_training', $training->id_training)
            ->where('status_kehadiran', 'Hadir')->count();
        $training->update([
            'jumlah_dijemput' => $count,
            'jumlah_hadir' => $hadir,
            'kadar_kehadiran_pct' => $count > 0 ? round(($hadir / $count) * 100, 2) : 0,
        ]);

        return back()->with('success', __('messages.training_joined'));
    }
}
