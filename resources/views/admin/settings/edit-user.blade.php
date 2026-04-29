@extends('layouts.admin')
@section('title', __('common.edit_user'))
@section('page-title', __('common.edit_user'))

@section('content')
<div class="max-w-2xl">
    <div class="mb-5">
        <a href="{{ route('admin.settings.users') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ __('messages.back') }}
        </a>
    </div>
    <form method="POST" action="{{ route('admin.settings.users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div id="manual-name-group">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.full_name') }} *</label>
                <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            </div>
            <div id="manual-email-group">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.email') }} *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.role') }} *</label>
                <select id="role_id" name="role_id" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" data-role-name="{{ $role->role_name }}" data-desc="{{ $role->description }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name ?? $role->role_name }}
                        </option>
                    @endforeach
                </select>
                <p id="role-description" class="text-xs text-gray-400 mt-1 hidden"></p>
            </div>
            <div id="talent-link-group" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.link_existing_talent') }} *</label>
                <select id="talent_id" name="talent_id" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    <option value="">{{ __('common.select_talent_account_link') }}</option>
                    @foreach($talents as $talent)
                        <option value="{{ $talent->id }}" data-name="{{ $talent->full_name }}" data-email="{{ $talent->email }}" {{ old('talent_id', $user->talent_id) == $talent->id ? 'selected' : '' }}>
                            {{ $talent->full_name }}{{ $talent->talent_code ? ' (' . $talent->talent_code . ')' : '' }}{{ $talent->email ? ' - ' . $talent->email : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="company-link-group" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.link_existing_company') }} *</label>
                <select id="company_id" name="company_id" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    <option value="">{{ __('common.select_company_account_link') }}</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" data-name="{{ $company->contact_person ?: $company->company_name }}" data-email="{{ $company->contact_email }}" {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                            {{ $company->company_name }}{{ $company->company_code ? ' (' . $company->company_code . ')' : '' }}{{ $company->contact_email ? ' - ' . $company->contact_email : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="pelaksana-link-group" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.link_existing_pelaksana') }} *</label>
                <select id="id_pelaksana" name="id_pelaksana" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    <option value="">{{ __('common.select_pelaksana_account_link') }}</option>
                    @foreach($pelaksanaCompanies as $pelaksana)
                        <option value="{{ $pelaksana->id_pelaksana }}" data-name="{{ $pelaksana->pic_syarikat ?: $pelaksana->nama_syarikat }}" data-email="{{ $pelaksana->email_pic }}" {{ old('id_pelaksana', $user->id_pelaksana) == $pelaksana->id_pelaksana ? 'selected' : '' }}>
                            {{ $pelaksana->nama_syarikat }}{{ $pelaksana->id_pelaksana ? ' (' . $pelaksana->id_pelaksana . ')' : '' }}{{ $pelaksana->email_pic ? ' - ' . $pelaksana->email_pic : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="penempatan-link-group" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.link_existing_penempatan') }} *</label>
                <select id="id_syarikat_penempatan" name="id_syarikat_penempatan" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                    <option value="">{{ __('common.select_penempatan_account_link') }}</option>
                    @foreach($placementCompanies as $penempatan)
                        <option value="{{ $penempatan->id_syarikat }}" data-name="{{ $penempatan->pic ?: $penempatan->nama_syarikat }}" data-email="{{ $penempatan->email_pic }}" {{ old('id_syarikat_penempatan', $user->id_syarikat_penempatan) == $penempatan->id_syarikat ? 'selected' : '' }}>
                            {{ $penempatan->nama_syarikat }}{{ $penempatan->id_syarikat ? ' (' . $penempatan->id_syarikat . ')' : '' }}{{ $penempatan->email_pic ? ' - ' . $penempatan->email_pic : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="link-hint" class="hidden rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                {{ __('common.linked_account_autofill_hint') }}
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.status_label') }}</label>
                    <select name="status" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>{{ __('common.active') }}</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>{{ __('common.inactive') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('common.language_label') }}</label>
                    <select name="language" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1E3A5F]">
                        <option value="ms" {{ old('language', $user->language) === 'ms' ? 'selected' : '' }}>Bahasa Melayu</option>
                        <option value="en" {{ old('language', $user->language) === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mt-5 flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-[#1E3A5F] text-white rounded-lg text-sm font-semibold hover:bg-[#152c47] transition-colors">{{ __('common.save_changes') }}</button>
            <a href="{{ route('admin.settings.users') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const roleSelect = document.getElementById('role_id');
        const talentGroup = document.getElementById('talent-link-group');
        const companyGroup = document.getElementById('company-link-group');
        const pelaksanaGroup = document.getElementById('pelaksana-link-group');
        const penempatanGroup = document.getElementById('penempatan-link-group');
        const talentSelect = document.getElementById('talent_id');
        const companySelect = document.getElementById('company_id');
        const pelaksanaSelect = document.getElementById('id_pelaksana');
        const penempatanSelect = document.getElementById('id_syarikat_penempatan');
        const nameInput = document.querySelector('input[name="full_name"]');
        const emailInput = document.querySelector('input[name="email"]');
        const nameGroup = document.getElementById('manual-name-group');
        const emailGroup = document.getElementById('manual-email-group');
        const linkHint = document.getElementById('link-hint');
        const roleDesc = document.getElementById('role-description');

        function selectedRoleName() {
            return roleSelect.options[roleSelect.selectedIndex]?.dataset.roleName || '';
        }

        function syncLinkedValues(select) {
            const option = select.options[select.selectedIndex];
            if (!option) return;
            if (option.dataset.name) nameInput.value = option.dataset.name;
            if (option.dataset.email) emailInput.value = option.dataset.email;
        }

        function toggleFields() {
            const roleName = selectedRoleName();
            const desc = roleSelect.options[roleSelect.selectedIndex]?.dataset.desc || '';
            if (desc) { roleDesc.textContent = desc; roleDesc.classList.remove('hidden'); }
            else { roleDesc.classList.add('hidden'); }
            const linkedTalent = roleName === 'talent';
            const linkedCompany = roleName === 'company_rep';
            const linkedPelaksana = roleName === 'syarikat_pelaksana';
            const linkedPenempatan = roleName === 'rakan_kolaborasi';

            talentGroup.classList.toggle('hidden', !linkedTalent);
            companyGroup.classList.toggle('hidden', !linkedCompany);
            pelaksanaGroup.classList.toggle('hidden', !linkedPelaksana);
            penempatanGroup.classList.toggle('hidden', !linkedPenempatan);
            linkHint.classList.toggle('hidden', !(linkedTalent || linkedCompany || linkedPelaksana || linkedPenempatan));
            nameInput.readOnly = linkedTalent || linkedCompany || linkedPelaksana || linkedPenempatan;
            emailInput.readOnly = linkedTalent || linkedCompany || linkedPelaksana || linkedPenempatan;
            nameGroup.classList.toggle('opacity-80', linkedTalent || linkedCompany || linkedPelaksana || linkedPenempatan);
            emailGroup.classList.toggle('opacity-80', linkedTalent || linkedCompany || linkedPelaksana || linkedPenempatan);

            if (linkedTalent) {
                syncLinkedValues(talentSelect);
            } else if (linkedCompany) {
                syncLinkedValues(companySelect);
            } else if (linkedPelaksana) {
                syncLinkedValues(pelaksanaSelect);
            } else if (linkedPenempatan) {
                syncLinkedValues(penempatanSelect);
            }
        }

        roleSelect.addEventListener('change', toggleFields);
        talentSelect.addEventListener('change', () => {
            if (selectedRoleName() === 'talent') syncLinkedValues(talentSelect);
        });
        companySelect.addEventListener('change', () => {
            if (selectedRoleName() === 'company_rep') syncLinkedValues(companySelect);
        });
        pelaksanaSelect.addEventListener('change', () => {
            if (selectedRoleName() === 'syarikat_pelaksana') syncLinkedValues(pelaksanaSelect);
        });
        penempatanSelect.addEventListener('change', () => {
            if (selectedRoleName() === 'rakan_kolaborasi') syncLinkedValues(penempatanSelect);
        });

        toggleFields();
    })();
</script>
@endpush
