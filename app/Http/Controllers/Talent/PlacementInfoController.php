<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PlacementInfoController extends BaseTalentController
{
    public function index()
    {
        $talent = $this->getTalent();
        $talent->load(['syarikatPelaksana', 'syarikatPenempatan']);

        $activePlacement  = $talent->activePlacement;
        $placementHistory = $talent->placements()
            ->with('company', 'batch')
            ->orderByDesc('start_date')
            ->get();

        // Check if talent has PROTEGE programme data directly on the talent record
        $hasProtegeData = $talent->id_pelaksana || $talent->id_syarikat_penempatan || $talent->jawatan;

        return view('talent.placement.index', compact('talent', 'activePlacement', 'placementHistory', 'hasProtegeData'));
    }
}
