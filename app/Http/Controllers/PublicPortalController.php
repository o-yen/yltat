<?php

namespace App\Http\Controllers;

use App\Models\ApplicantRequest;
use App\Models\SyarikatPelaksana;
use App\Models\Talent;
use Illuminate\Http\Request;

class PublicPortalController extends Controller
{
    public const PUBLIC_TALENT_STATUSES = [
        'approved', 'assigned', 'in_progress', 'completed', 'alumni',
        'Aktif', 'Tamat',
    ];

    public function index(Request $request)
    {
        $query = $this->portalTalentQueryForCurrentUser();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('university', 'like', "%{$search}%")
                  ->orWhere('programme', 'like', "%{$search}%")
                  ->orWhere('skills_text', 'like', "%{$search}%");
            });
        }

        if ($request->filled('university')) {
            $query->where('university', $request->university);
        }

        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }

        if ($request->filled('programme')) {
            $query->where('programme', $request->programme);
        }

        $talents = $query->orderBy('full_name')->paginate(12)->withQueryString();

        // Get distinct universities and skills for filter dropdowns
        $baseQuery = $this->portalTalentQueryForCurrentUser();
        $universities = $baseQuery->whereNotNull('university')->where('university', '!=', '')
            ->distinct()->orderBy('university')->pluck('university');

        $programmes = $this->portalTalentQueryForCurrentUser()
            ->whereNotNull('programme')->where('programme', '!=', '')
            ->distinct()->orderBy('programme')->pluck('programme');

        $requestStatuses = $this->requestStatusesForTalents($talents->getCollection()->pluck('id')->all());
        $implementingCompanies = $this->implementationCompanyOptions();
        $statusLabels = ApplicantRequest::statusLabels();

        return view('portal.index', compact('talents', 'universities', 'programmes', 'requestStatuses', 'implementingCompanies', 'statusLabels'));
    }

    public function show(Talent $talent)
    {
        if (!$this->isVisiblePortalTalent($talent)) {
            abort(404);
        }

        // Only load public-safe data - no IC, address, stipend, notes
        $certifications = $talent->certifications()->get(['certification_name', 'issuer', 'issue_date', 'expiry_date']);

        $requestStatus = $this->requestStatusesForTalents([$talent->id])[$talent->id] ?? null;
        $implementingCompanies = $this->implementationCompanyOptions();
        $statusLabels = ApplicantRequest::statusLabels();

        return view('portal.show', compact('talent', 'certifications', 'requestStatus', 'implementingCompanies', 'statusLabels'));
    }

    public function suggestions(Request $request)
    {
        $field = $request->input('field');
        $term = $request->input('term', '');

        if (!in_array($field, ['university', 'programme'])) {
            return response()->json([]);
        }

        $results = $this->portalTalentQueryForCurrentUser()
            ->where($field, '!=', '')
            ->where($field, 'like', "%{$term}%")
            ->distinct()
            ->orderBy($field)
            ->limit(20)
            ->pluck($field);

        return response()->json($results);
    }

    private function portalTalentQueryForCurrentUser()
    {
        if (auth()->check() && auth()->user()->hasRole('rakan_kolaborasi')) {
            return $this->availableTalentQuery();
        }

        if (auth()->check() && auth()->user()->hasRole('syarikat_pelaksana')) {
            return $this->basePublicTalentQuery()->whereRaw('1 = 0');
        }

        return $this->basePublicTalentQuery();
    }

    private function basePublicTalentQuery()
    {
        return Talent::query()
            ->whereNotNull('id_graduan')
            ->where('public_visibility', true)
            ->where(function ($query) {
                $query->whereIn('status_aktif', self::PUBLIC_TALENT_STATUSES)
                    ->orWhere(function ($fallbackQuery) {
                        $fallbackQuery->whereNull('status_aktif')
                            ->whereIn('status', self::PUBLIC_TALENT_STATUSES);
                    });
            });
    }

    private function availableTalentQuery()
    {
        return $this->basePublicTalentQuery()
            ->where(function ($query) {
                $query->whereNull('id_pelaksana')->orWhere('id_pelaksana', '');
            })
            ->where(function ($query) {
                $query->whereNull('id_syarikat_penempatan')->orWhere('id_syarikat_penempatan', '');
            });
    }

    private function isVisiblePortalTalent(Talent $talent): bool
    {
        if (auth()->check() && auth()->user()->hasRole('rakan_kolaborasi')) {
            return $this->isAvailableTalent($talent);
        }

        if (auth()->check() && auth()->user()->hasRole('syarikat_pelaksana')) {
            return false;
        }

        return $this->isPublicTalent($talent);
    }

    private function isPublicTalent(Talent $talent): bool
    {
        $resolvedStatus = $talent->status_aktif ?: $talent->status;

        return !empty($talent->id_graduan)
            && (bool) $talent->public_visibility
            && in_array($resolvedStatus, self::PUBLIC_TALENT_STATUSES, true);
    }

    private function isAvailableTalent(Talent $talent): bool
    {
        return $this->isPublicTalent($talent)
            && empty($talent->id_pelaksana)
            && empty($talent->id_syarikat_penempatan);
    }

    private function requestStatusesForTalents(array $talentIds): array
    {
        if (empty($talentIds) || !auth()->check() || !auth()->user()->hasRole('rakan_kolaborasi')) {
            return [];
        }

        $placementCompanyId = auth()->user()->id_syarikat_penempatan;
        if (empty($placementCompanyId)) {
            return [];
        }

        return ApplicantRequest::query()
            ->where('placement_company_id', $placementCompanyId)
            ->whereIn('talent_id', $talentIds)
            ->get(['talent_id', 'status'])
            ->pluck('status', 'talent_id')
            ->map(fn ($status) => (string) $status)
            ->all();
    }

    private function implementationCompanyOptions()
    {
        if (!auth()->check() || !auth()->user()->hasRole('rakan_kolaborasi')) {
            return collect();
        }

        return SyarikatPelaksana::query()
            ->orderBy('nama_syarikat')
            ->get(['id_pelaksana', 'nama_syarikat']);
    }
}
