<?php

namespace App\Http\Controllers;

use App\Mail\TalentWelcomeMail;
use App\Models\Role;
use App\Models\Talent;
use App\Models\TalentDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('portal.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Personal
            'full_name'             => 'required|string|max:255',
            'ic_passport_no'        => 'required|string|max:20|unique:talents,ic_passport_no',
            'gender'                => 'required|in:Lelaki,Perempuan',
            'date_of_birth'         => 'required|date',
            'phone'                 => 'required|string|max:20',
            'address'               => 'required|string',
            'email'                 => 'required|email|unique:talents,email|unique:users,email',
            // Background
            'background_type'       => 'required|in:anak_atm,anak_veteran_atm,anak_awam_mindef',
            'guardian_name'         => 'required|string|max:255',
            'guardian_ic'           => 'required|string|max:20',
            'guardian_relationship' => 'required|string|max:100',
            // Academic
            'highest_qualification' => 'required|string',
            'university'            => 'required|string|max:255',
            'programme'             => 'required|string|max:255',
            'graduation_year'       => 'required|integer|min:1990|max:' . (date('Y') + 2),
            'cgpa'                  => 'nullable|numeric|min:0|max:4',
            // Placement
            'preferred_sectors'     => 'required|array|min:1',
            'preferred_locations'   => 'required|array|min:1',
            'currently_employed'    => 'required|in:0,1',
            'available_start_date'  => 'nullable|date',
            // Docs
            'resume'                => 'required|file|mimes:pdf|max:10240',
            'ic_copy'               => 'required|file|mimes:pdf|max:10240',
            'transcript'            => 'required|file|mimes:pdf|max:10240',
            'military_card'         => 'nullable|file|mimes:pdf|max:10240',
            // PDPA
            'pdpa_consent'          => 'required|accepted',
            'declaration_signature' => 'required|string|max:255',
        ], [
            'ic_passport_no.unique'  => 'No Kad Pengenalan ini telah didaftarkan.',
            'email.unique'           => 'Emel ini telah didaftarkan.',
            'pdpa_consent.accepted'  => 'Sila berikan persetujuan PDPA.',
            'preferred_sectors.required' => 'Sila pilih sekurang-kurangnya satu sektor penempatan.',
            'preferred_locations.required' => 'Sila pilih sekurang-kurangnya satu lokasi penempatan.',
        ]);

        $talentRole = Role::firstOrCreate(
            ['role_name' => 'talent'],
            [
                'display_name' => 'Graduate / Protege',
                'description' => 'Access own profile, daily logs, placement info, and allowance status.',
                'permissions_json' => json_encode(['profile.view', 'daily_logs.create', 'feedback.create']),
                'is_active' => true,
                'sort_order' => 6,
            ]
        );
        // Password will be generated and sent only after admin approval
        $placeholderPassword = Str::random(40);

        [$talent, $user] = DB::transaction(function () use ($request, $talentRole, $placeholderPassword) {
            $talentCode = Talent::generateCode();

            // Map background_type to kategori (PROTEGE field)
            $kategoriMap = [
                'anak_atm' => 'Anak ATM',
                'anak_veteran_atm' => 'Anak Veteran',
                'anak_awam_mindef' => 'Anak Awam MINDEF',
            ];

            // Map highest_qualification to kelayakan display text
            $kelayakanMap = [
                'diploma' => 'Diploma',
                'ijazah' => 'Ijazah Sarjana Muda',
                'sarjana' => 'Ijazah Sarjana',
                'phd' => 'PhD',
                'lain' => 'Lain-lain',
            ];

            // Map gender to standard format
            $genderMap = [
                'Lelaki' => 'male',
                'Perempuan' => 'female',
            ];

            $talent = Talent::create([
                'id_graduan'            => $talentCode,
                'talent_code'           => $talentCode,
                'full_name'             => $request->full_name,
                'ic_passport_no'        => $request->ic_passport_no,
                'gender'                => $genderMap[$request->gender] ?? $request->gender,
                'date_of_birth'         => $request->date_of_birth,
                'phone'                 => $request->phone,
                'address'               => $request->address,
                'email'                 => $request->email,
                'university'            => $request->university,
                'programme'             => $request->programme,
                'graduation_year'       => $request->graduation_year,
                'cgpa'                  => $request->cgpa,
                'status'                => 'applied',
                'public_visibility'     => false,
                'background_type'       => $request->background_type,
                'guardian_name'         => $request->guardian_name,
                'guardian_ic'           => $request->guardian_ic,
                'guardian_military_no'  => $request->guardian_military_no,
                'guardian_relationship' => $request->guardian_relationship,
                'highest_qualification' => $request->highest_qualification,
                'preferred_sectors'     => $request->preferred_sectors,
                'preferred_locations'   => $request->preferred_locations,
                'currently_employed'    => $request->currently_employed,
                'available_start_date'  => $request->available_start_date,
                'pdpa_consent'          => true,
                'declaration_signature' => $request->declaration_signature,
                // Fill duplicate/PROTEGE columns from registration data
                'kategori'              => $kategoriMap[$request->background_type] ?? null,
                'kelayakan'             => $kelayakanMap[$request->highest_qualification] ?? $request->highest_qualification,
                'status_aktif'          => 'Dalam Proses',
            ]);

            $user = User::create([
                'full_name' => $talent->full_name,
                'email' => $talent->email,
                'password' => $placeholderPassword,
                'role_id' => $talentRole->id,
                'talent_id' => $talent->id,
                'status' => 'inactive',
                'language' => app()->getLocale(),
            ]);

            foreach ([
                'resume' => 'resume',
                'ic_copy' => 'ic_copy',
                'transcript' => 'transcript',
                'military_card' => 'military_card',
            ] as $field => $docType) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = $docType . '_' . $talent->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('documents/' . $talent->id, $filename, 'public');

                    TalentDocument::create([
                        'talent_id' => $talent->id,
                        'document_type' => $docType,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'uploaded_at' => now(),
                    ]);
                }
            }

            return [$talent, $user];
        });

        // Send confirmation email (no password — password sent after admin approval)
        try {
            Mail::to($user->email)->send(new TalentWelcomeMail($talent, $user));
        } catch (\Throwable $e) {
            Log::error('Failed to send registration confirmation email.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('portal.register.success')
            ->with('ref_code', $talent->talent_code)
            ->with('account_email', $user->email);
    }

    public function success()
    {
        return view('portal.register-success');
    }

    protected function generateTemporaryPassword(): string
    {
        return strtoupper(Str::random(4)) . strtolower(Str::random(4)) . random_int(10, 99) . '!';
    }
}
