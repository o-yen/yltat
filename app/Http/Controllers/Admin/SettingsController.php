<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\IntakeBatch;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use App\Models\Talent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\PasswordResetByAdminMail;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    // User Management
    public function users(Request $request)
    {
        $query = User::with(['role', 'talent', 'company', 'syarikatPelaksana', 'syarikatPenempatan']);

        if ($request->filled('search')) {
            $query->where(function ($searchQuery) use ($request) {
                $searchQuery->where('full_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->orderBy('full_name')->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('admin.settings.users', compact('users', 'roles'));
    }

    public function createUser()
    {
        $roles = Role::where('is_active', true)->orderBy('sort_order')->get();
        [$talents, $companies, $pelaksanaCompanies, $placementCompanies] = $this->linkableOptions();

        return view('admin.settings.create-user', compact('roles', 'talents', 'companies', 'pelaksanaCompanies', 'placementCompanies'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:200',
            'email' => 'nullable|email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'talent_id' => 'nullable|exists:talents,id|unique:users,talent_id',
            'company_id' => 'nullable|exists:companies,id|unique:users,company_id',
            'id_pelaksana' => 'nullable|exists:syarikat_pelaksana,id_pelaksana|unique:users,id_pelaksana',
            'id_syarikat_penempatan' => 'nullable|exists:syarikat_penempatan,id_syarikat|unique:users,id_syarikat_penempatan',
            'status' => 'required|in:active,inactive',
            'language' => 'required|in:ms,en',
        ]);

        $payload = $this->buildUserPayload($validated);
        $payload['password'] = Hash::make($validated['password']);

        $user = User::create($payload);

        AuditLog::log('settings', 'create_user', $user->id, null, ['full_name' => $user->full_name, 'email' => $user->email]);

        return redirect()->route('admin.settings.users')
            ->with('success', __('messages.user_created'));
    }

    public function editUser(User $user)
    {
        $roles = Role::where('is_active', true)->orderBy('sort_order')->get();
        [$talents, $companies, $pelaksanaCompanies, $placementCompanies] = $this->linkableOptions($user);

        return view('admin.settings.edit-user', compact('user', 'roles', 'talents', 'companies', 'pelaksanaCompanies', 'placementCompanies'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:200',
            'email' => 'nullable|email',
            'role_id' => 'required|exists:roles,id',
            'talent_id' => [
                'nullable',
                'exists:talents,id',
                Rule::unique('users', 'talent_id')->ignore($user->id),
            ],
            'company_id' => [
                'nullable',
                'exists:companies,id',
                Rule::unique('users', 'company_id')->ignore($user->id),
            ],
            'id_pelaksana' => [
                'nullable',
                'exists:syarikat_pelaksana,id_pelaksana',
                Rule::unique('users', 'id_pelaksana')->ignore($user->id),
            ],
            'id_syarikat_penempatan' => [
                'nullable',
                'exists:syarikat_penempatan,id_syarikat',
                Rule::unique('users', 'id_syarikat_penempatan')->ignore($user->id),
            ],
            'status' => 'required|in:active,inactive',
            'language' => 'required|in:ms,en',
        ]);

        $payload = $this->buildUserPayload($validated, $user);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $payload['password'] = Hash::make($request->password);
        }

        $user->update($payload);

        AuditLog::log('settings', 'update_user', $user->id, null, ['email' => $user->email]);

        return redirect()->route('admin.settings.users')
            ->with('success', __('messages.user_updated'));
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('messages.cannot_delete_self'));
        }

        $user->delete();

        return redirect()->route('admin.settings.users')
            ->with('success', __('messages.user_deleted'));
    }

    public function resetPassword(User $user)
    {
        $newPassword = strtoupper(Str::random(4)) . strtolower(Str::random(4)) . random_int(10, 99) . '!';

        $user->update(['password' => Hash::make($newPassword)]);

        AuditLog::log('settings', 'reset_password', $user->id, null, ['email' => $user->email]);

        // Send new password via email
        try {
            Mail::to($user->email)->send(new PasswordResetByAdminMail($user, $newPassword));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', __('messages.password_reset_success', [
            'name' => $user->full_name,
            'password' => $newPassword,
        ]));
    }

    // Intake Batches
    public function batches()
    {
        $batches = IntakeBatch::withCount('placements')->orderByDesc('year')->paginate(20);
        return view('admin.settings.batches', compact('batches'));
    }

    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'batch_name' => 'required|string|max:200',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'year' => 'required|integer|min:2000|max:2099',
            'status' => 'required|in:active,closed,planned',
        ]);

        IntakeBatch::create($validated);

        return redirect()->route('admin.settings.batches')
            ->with('success', __('messages.batch_created'));
    }

    public function updateBatch(Request $request, IntakeBatch $batch)
    {
        $validated = $request->validate([
            'batch_name' => 'required|string|max:200',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'year' => 'required|integer|min:2000|max:2099',
            'status' => 'required|in:active,closed,planned',
        ]);

        $batch->update($validated);

        return redirect()->route('admin.settings.batches')
            ->with('success', __('messages.batch_updated'));
    }

    public function destroyBatch(IntakeBatch $batch)
    {
        $batch->delete();
        return redirect()->route('admin.settings.batches')
            ->with('success', __('messages.batch_deleted'));
    }

    // Roles
    public function roles()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.settings.roles', compact('roles'));
    }

    protected function buildUserPayload(array $validated, ?User $user = null): array
    {
        $role = Role::findOrFail($validated['role_id']);

        $payload = [
            'role_id' => $validated['role_id'],
            'status' => $validated['status'],
            'language' => $validated['language'],
            'talent_id' => null,
            'company_id' => null,
            'id_pelaksana' => null,
            'id_syarikat_penempatan' => null,
        ];

        if ($role->role_name === 'talent') {
            validator($validated, [
                'talent_id' => 'required|exists:talents,id',
            ])->validate();

            $talent = Talent::findOrFail($validated['talent_id']);

            validator(
                ['email' => $talent->email],
                ['email' => 'required|email'],
                ['email.required' => __('common.talent_account_email_required')]
            )->validate();

            $payload['full_name'] = $talent->full_name;
            $payload['email'] = $talent->email;
            $payload['talent_id'] = $talent->id;
        } elseif ($role->role_name === 'company_rep') {
            validator($validated, [
                'company_id' => 'required|exists:companies,id',
            ])->validate();

            $company = Company::findOrFail($validated['company_id']);

            validator(
                ['email' => $company->contact_email],
                ['email' => 'required|email'],
                ['email.required' => __('common.company_account_email_required')]
            )->validate();

            $payload['full_name'] = $company->contact_person ?: $company->company_name;
            $payload['email'] = $company->contact_email;
            $payload['company_id'] = $company->id;
        } elseif ($role->role_name === 'syarikat_pelaksana') {
            validator($validated, [
                'id_pelaksana' => 'required|exists:syarikat_pelaksana,id_pelaksana',
            ])->validate();

            $pelaksana = SyarikatPelaksana::findOrFail($validated['id_pelaksana']);

            validator(
                ['email' => $pelaksana->email_pic],
                ['email' => 'required|email'],
                ['email.required' => __('common.pelaksana_account_email_required')]
            )->validate();

            $payload['full_name'] = $pelaksana->pic_syarikat ?: $pelaksana->nama_syarikat;
            $payload['email'] = $pelaksana->email_pic;
            $payload['id_pelaksana'] = $pelaksana->id_pelaksana;
        } elseif ($role->role_name === 'rakan_kolaborasi') {
            validator($validated, [
                'id_syarikat_penempatan' => 'required|exists:syarikat_penempatan,id_syarikat',
                'full_name' => 'required|string|max:200',
                'email' => 'required|email',
            ])->validate();

            $payload['full_name'] = $validated['full_name'];
            $payload['email'] = $validated['email'];
            $payload['id_syarikat_penempatan'] = $validated['id_syarikat_penempatan'];
        } else {
            $payload['full_name'] = $validated['full_name'] ?? $user?->full_name;
            $payload['email'] = $validated['email'] ?? $user?->email;
        }

        validator($payload, [
            'full_name' => 'required|string|max:200',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user?->id)],
        ])->validate();

        return $payload;
    }

    protected function linkableOptions(?User $user = null): array
    {
        $talents = Talent::query()
            ->where(function ($query) use ($user) {
                $query->whereDoesntHave('linkedUser');
                if ($user?->talent_id) {
                    $query->orWhere('id', $user->talent_id);
                }
            })
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'email', 'talent_code']);

        $companies = Company::query()
            ->where(function ($query) use ($user) {
                $query->whereDoesntHave('linkedUser');
                if ($user?->company_id) {
                    $query->orWhere('id', $user->company_id);
                }
            })
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'contact_person', 'contact_email', 'company_code']);

        $pelaksanaCompanies = SyarikatPelaksana::query()
            ->where(function ($query) use ($user) {
                $query->whereDoesntHave('users');
                if ($user?->id_pelaksana) {
                    $query->orWhere('id_pelaksana', $user->id_pelaksana);
                }
            })
            ->orderBy('nama_syarikat')
            ->get(['id_pelaksana', 'nama_syarikat', 'pic_syarikat', 'email_pic']);

        $placementCompanies = SyarikatPenempatan::query()
            ->orderBy('nama_syarikat')
            ->get(['id_syarikat', 'nama_syarikat', 'pic', 'email_pic']);

        return [$talents, $companies, $pelaksanaCompanies, $placementCompanies];
    }
}
