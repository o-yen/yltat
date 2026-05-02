<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Talent;
use Illuminate\Http\Request;

class PortalController extends BaseMobileController
{
    private const PUBLIC_TALENT_STATUSES = [
        'approved', 'assigned', 'in_progress', 'completed', 'alumni',
        'Aktif', 'Tamat',
    ];

    public function index(Request $request)
    {
        $query = $this->basePublicTalentQuery();
        $talents = $this->filteredTalents($request, $query);

        return $this->success($this->talentListPayload($talents));
    }

    public function availableForCompany(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('rakan_kolaborasi')) {
            return $this->error('Forbidden.', 403);
        }

        $talents = $this->filteredTalents($request, $this->availableTalentQuery());

        return $this->success($this->talentListPayload($talents));
    }

    public function show(Talent $talent)
    {
        if (!$this->isPublicTalent($talent)) {
            return $this->error('Talent not found.', 404);
        }

        $talent->load('certifications');

        return $this->success([
            'talent' => [
                'id' => $talent->id,
                'full_name' => $talent->full_name,
                'programme' => $talent->programme,
                'university' => $talent->university,
                'graduation_year' => $talent->graduation_year,
                'skills' => array_values(array_filter(array_map('trim', explode(',', (string) $talent->skills_text)))),
                'profile_summary' => $talent->profile_summary,
                'preferred_sectors' => $talent->preferred_sectors ?? [],
                'preferred_locations' => $talent->preferred_locations ?? [],
                'certifications' => $talent->certifications->map(fn($cert) => [
                    'name' => $cert->certification_name,
                    'issuer' => $cert->issuer,
                    'issue_date' => optional($cert->issue_date)->toDateString(),
                    'expiry_date' => optional($cert->expiry_date)->toDateString(),
                ])->values(),
            ],
        ]);
    }

    private function filteredTalents(Request $request, $query)
    {
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
            $query->where('university', 'like', "%{$request->university}%");
        }

        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }

        if ($request->filled('skills')) {
            $query->where('skills_text', 'like', "%{$request->skills}%");
        }

        return $query->orderBy('full_name')->paginate((int) $request->get('per_page', 12));
    }

    private function talentListPayload($talents): array
    {
        return [
            'items' => $talents->getCollection()->map(function (Talent $talent) {
                return [
                    'id' => $talent->id,
                    'full_name' => $talent->full_name,
                    'programme' => $talent->programme,
                    'university' => $talent->university,
                    'graduation_year' => $talent->graduation_year,
                    'skills' => array_values(array_filter(array_map('trim', explode(',', (string) $talent->skills_text)))),
                    'profile_summary' => $talent->profile_summary,
                ];
            })->values(),
            'pagination' => [
                'current_page' => $talents->currentPage(),
                'last_page' => $talents->lastPage(),
                'per_page' => $talents->perPage(),
                'total' => $talents->total(),
            ],
        ];
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

    private function isPublicTalent(Talent $talent): bool
    {
        $resolvedStatus = $talent->status_aktif ?: $talent->status;

        return !empty($talent->id_graduan)
            && (bool) $talent->public_visibility
            && in_array($resolvedStatus, self::PUBLIC_TALENT_STATUSES, true);
    }
}
