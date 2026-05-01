<?php

namespace App\Http\Controllers;

use App\Models\ApplicantRequest;
use App\Models\Talent;
use Illuminate\Http\Request;

class PublicPortalController extends Controller
{
    private const PUBLIC_TALENT_STATUSES = [
        'approved', 'assigned', 'in_progress', 'completed', 'alumni',
        'Aktif', 'Tamat',
    ];

    public function index(Request $request)
    {
        $query = $this->publicTalentQuery();

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
        $baseQuery = $this->publicTalentQuery();
        $universities = $baseQuery->whereNotNull('university')->where('university', '!=', '')
            ->distinct()->orderBy('university')->pluck('university');

        $programmes = $this->publicTalentQuery()
            ->whereNotNull('programme')->where('programme', '!=', '')
            ->distinct()->orderBy('programme')->pluck('programme');

        $requestStatuses = $this->requestStatusesForTalents($talents->getCollection()->pluck('id')->all());

        return view('portal.index', compact('talents', 'universities', 'programmes', 'requestStatuses'));
    }

    public function show(Talent $talent)
    {
        if (!$this->isPublicTalent($talent)) {
            abort(404);
        }

        // Only load public-safe data - no IC, address, stipend, notes
        $certifications = $talent->certifications()->get(['certification_name', 'issuer', 'issue_date', 'expiry_date']);

        $requestStatus = $this->requestStatusesForTalents([$talent->id])[$talent->id] ?? null;

        return view('portal.show', compact('talent', 'certifications', 'requestStatus'));
    }

    public function suggestions(Request $request)
    {
        $field = $request->input('field');
        $term = $request->input('term', '');

        if (!in_array($field, ['university', 'programme'])) {
            return response()->json([]);
        }

        $results = Talent::whereNotNull($field)
            ->where($field, '!=', '')
            ->where($field, 'like', "%{$term}%")
            ->distinct()
            ->orderBy($field)
            ->limit(20)
            ->pluck($field);

        return response()->json($results);
    }

    private function publicTalentQuery()
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

    private function isPublicTalent(Talent $talent): bool
    {
        $resolvedStatus = $talent->status_aktif ?: $talent->status;

        return !empty($talent->id_graduan)
            && (bool) $talent->public_visibility
            && in_array($resolvedStatus, self::PUBLIC_TALENT_STATUSES, true);
    }

    private function requestStatusesForTalents(array $talentIds): array
    {
        if (empty($talentIds) || !auth()->check() || !auth()->user()->hasRole('syarikat_pelaksana')) {
            return [];
        }

        $implementingCompanyId = auth()->user()->id_pelaksana;
        if (empty($implementingCompanyId)) {
            return [];
        }

        return ApplicantRequest::query()
            ->where('implementing_company_id', $implementingCompanyId)
            ->whereIn('talent_id', $talentIds)
            ->get(['talent_id', 'status'])
            ->pluck('status', 'talent_id')
            ->map(fn ($status) => (string) $status)
            ->all();
    }
}
