<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Talent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseMobileController extends Controller
{
    protected function success(array $data = [], string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error(string $message, int $status = 422, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    protected function currentToken(Request $request)
    {
        return $request->attributes->get('mobile_access_token');
    }

    protected function resolveTalentForUser(): ?Talent
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($user->relationLoaded('talent') && $user->talent) {
            return $user->talent;
        }

        if ($user->talent_id) {
            return $user->talent()->first();
        }

        return Talent::where('email', $user->email)->first();
    }

    protected function resolveCompanyForUser(): ?Company
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($user->relationLoaded('company') && $user->company) {
            return $user->company;
        }

        if ($user->company_id) {
            return $user->company()->first();
        }

        if ($user->id_syarikat_penempatan) {
            return Company::where('company_code', $user->id_syarikat_penempatan)->first();
        }

        return Company::where('contact_email', $user->email)->first();
    }

    protected function userPayload($user): array
    {
        return [
            'id' => $user->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'status' => $user->status,
            'language' => $user->language,
            'role' => $user->role?->role_name,
        ];
    }

    protected function talentPayload(Talent $talent): array
    {
        return [
            'id' => $talent->id,
            'talent_code' => $talent->talent_code,
            'full_name' => $talent->full_name,
            'email' => $talent->email,
            'phone' => $talent->phone,
            'ic_passport_no' => $talent->ic_passport_no,
            'gender' => $talent->gender,
            'date_of_birth' => optional($talent->date_of_birth)->toDateString(),
            'address' => $talent->address,
            'university' => $talent->university,
            'programme' => $talent->programme,
            'cgpa' => $talent->cgpa,
            'graduation_year' => $talent->graduation_year,
            'skills_text' => $talent->skills_text,
            'profile_summary' => $talent->profile_summary,
            'public_visibility' => (bool) $talent->public_visibility,
            'status' => $talent->status,
            'background_type' => $talent->background_type,
            'guardian_name' => $talent->guardian_name,
            'guardian_ic' => $talent->guardian_ic,
            'guardian_military_no' => $talent->guardian_military_no,
            'guardian_relationship' => $talent->guardian_relationship,
            'highest_qualification' => $talent->highest_qualification,
            'preferred_sectors' => $talent->preferred_sectors ?? [],
            'preferred_locations' => $talent->preferred_locations ?? [],
            'currently_employed' => (bool) $talent->currently_employed,
            'available_start_date' => optional($talent->available_start_date)->toDateString(),
            'reviewed_at' => optional($talent->reviewed_at)->toIso8601String(),
            'rejection_reason' => $talent->rejection_reason,
        ];
    }

    protected function placementPayload($placement): array
    {
        return [
            'id' => $placement->id,
            'department' => $placement->department,
            'supervisor_name' => $placement->supervisor_name,
            'supervisor_email' => $placement->supervisor_email,
            'start_date' => optional($placement->start_date)->toDateString(),
            'end_date' => optional($placement->end_date)->toDateString(),
            'duration_months' => $placement->duration_months,
            'monthly_stipend' => (float) $placement->monthly_stipend,
            'additional_cost' => (float) ($placement->additional_cost ?? 0),
            'placement_status' => $placement->placement_status,
            'programme_type' => $placement->programme_type,
            'remarks' => $placement->remarks,
            'talent' => $placement->relationLoaded('talent') && $placement->talent ? [
                'id' => $placement->talent->id,
                'full_name' => $placement->talent->full_name,
                'university' => $placement->talent->university,
                'talent_code' => $placement->talent->talent_code,
                'email' => $placement->talent->email,
                'phone' => $placement->talent->phone,
                'programme' => $placement->talent->programme,
                'cgpa' => $placement->talent->cgpa,
                'status_aktif' => $placement->talent->status_aktif,
                'status_penyerapan_6bulan' => $placement->talent->status_penyerapan_6bulan,
                'id_graduan' => $placement->talent->id_graduan,
                'jawatan' => $placement->talent->jawatan,
                'tarikh_mula' => optional($placement->talent->tarikh_mula)->toDateString(),
                'tarikh_tamat' => optional($placement->talent->tarikh_tamat)->toDateString(),
                'department' => $placement->talent->department,
                'supervisor_name' => $placement->talent->supervisor_name,
                'supervisor_email' => $placement->talent->supervisor_email,
                'duration_months' => $placement->talent->duration_months,
                'monthly_stipend' => (float) ($placement->talent->monthly_stipend ?? 0),
                'programme_type' => $placement->talent->programme_type,
                'syarikat_pelaksana' => $placement->talent->relationLoaded('syarikatPelaksana') && $placement->talent->syarikatPelaksana ? [
                    'id' => $placement->talent->syarikatPelaksana->id_pelaksana,
                    'company_name' => $placement->talent->syarikatPelaksana->nama_syarikat,
                ] : null,
                'syarikat_penempatan' => $placement->talent->relationLoaded('syarikatPenempatan') && $placement->talent->syarikatPenempatan ? [
                    'id' => $placement->talent->syarikatPenempatan->id_syarikat,
                    'company_name' => $placement->talent->syarikatPenempatan->nama_syarikat,
                ] : null,
            ] : null,
            'company' => $placement->relationLoaded('company') && $placement->company ? [
                'id' => $placement->company->id,
                'company_name' => $placement->company->company_name,
                'company_code' => $placement->company->company_code,
                'contact_person' => $placement->company->contact_person,
                'contact_email' => $placement->company->contact_email,
                'contact_phone' => $placement->company->contact_phone,
            ] : null,
            'batch' => $placement->relationLoaded('batch') && $placement->batch ? [
                'id' => $placement->batch->id,
                'batch_name' => $placement->batch->batch_name,
                'year' => $placement->batch->year,
            ] : null,
        ];
    }
}
