<?php

namespace Tests\Feature;

use App\Models\ApplicantRequest;
use App\Models\Role;
use App\Models\SyarikatPelaksana;
use App\Models\SyarikatPenempatan;
use App\Models\Talent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_approved_public_talents_including_assigned_talents(): void
    {
        $placementCompany = $this->placementCompany('PEN_000', 'Placement Public');
        $implementingCompany = $this->implementingCompany('PEL_000', 'Implementing Public');

        $this->talent(['talent_code' => 'TAL-PUB-001', 'full_name' => 'Public Available Talent']);
        $this->talent([
            'talent_code' => 'TAL-PUB-002',
            'full_name' => 'Public Assigned Talent',
            'id_pelaksana' => $implementingCompany->id_pelaksana,
            'id_syarikat_penempatan' => $placementCompany->id_syarikat,
            'status' => 'assigned',
            'status_aktif' => 'Aktif',
        ]);

        $this->get(route('portal.index'))
            ->assertOk()
            ->assertSee('Public Available Talent')
            ->assertSee('Public Assigned Talent')
            ->assertDontSee('Request Applicant');
    }

    public function test_guest_cannot_submit_applicant_request(): void
    {
        $implementingCompany = $this->implementingCompany('PEL_GUEST', 'Guest Target');
        $talent = $this->talent(['talent_code' => 'TAL-GUEST']);

        $this->post(route('portal.request-applicant', $talent), [
            'implementing_company_id' => $implementingCompany->id_pelaksana,
            'request_message' => 'Guest request',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('applicant_requests', 0);
    }

    public function test_admin_can_view_assigned_and_unassigned_talents_in_portal(): void
    {
        $placementCompany = $this->placementCompany('PEN_ADMIN', 'Placement Admin');
        $implementingCompany = $this->implementingCompany('PEL_ADMIN', 'Implementing Admin');
        $admin = $this->user('pmo_admin');

        $this->talent(['talent_code' => 'TAL-ADM-001', 'full_name' => 'Admin Available Talent']);
        $this->talent([
            'talent_code' => 'TAL-ADM-002',
            'full_name' => 'Admin Assigned Talent',
            'id_pelaksana' => $implementingCompany->id_pelaksana,
            'id_syarikat_penempatan' => $placementCompany->id_syarikat,
            'status' => 'assigned',
            'status_aktif' => 'Aktif',
        ]);

        $this->actingAs($admin)
            ->get(route('portal.index'))
            ->assertOk()
            ->assertSee('Admin Available Talent')
            ->assertSee('Admin Assigned Talent')
            ->assertDontSee('Request Applicant');
    }

    public function test_implementation_company_cannot_request_talent_from_portal(): void
    {
        $implementingCompany = $this->implementingCompany('PEL_001', 'Implementing A');
        $user = $this->user('syarikat_pelaksana', ['id_pelaksana' => $implementingCompany->id_pelaksana]);
        $talent = $this->talent(['talent_code' => 'TAL-001']);

        $this->actingAs($user)
            ->post(route('portal.request-applicant', $talent), [
                'implementing_company_id' => $implementingCompany->id_pelaksana,
                'request_message' => 'Wrong flow',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('applicant_requests', 0);

        $this->actingAs($user)
            ->get(route('portal.index'))
            ->assertOk()
            ->assertDontSee('Test Talent');
    }

    public function test_placement_company_can_only_view_available_talents_in_portal(): void
    {
        $placementCompany = $this->placementCompany('PEN_001', 'Placement A');
        $implementingCompany = $this->implementingCompany('PEL_002', 'Implementing B');
        $user = $this->user('rakan_kolaborasi', ['id_syarikat_penempatan' => $placementCompany->id_syarikat]);

        $this->talent(['talent_code' => 'TAL-002', 'full_name' => 'Available Talent']);
        $this->talent([
            'talent_code' => 'TAL-003',
            'full_name' => 'Implementation Assigned Talent',
            'id_pelaksana' => $implementingCompany->id_pelaksana,
        ]);
        $this->talent([
            'talent_code' => 'TAL-004',
            'full_name' => 'Placement Assigned Talent',
            'id_syarikat_penempatan' => $placementCompany->id_syarikat,
        ]);

        $this->actingAs($user)
            ->get(route('portal.index'))
            ->assertOk()
            ->assertSee('Available Talent')
            ->assertDontSee('Implementation Assigned Talent')
            ->assertDontSee('Placement Assigned Talent');
    }

    public function test_placement_company_cannot_request_already_assigned_talent(): void
    {
        $placementCompany = $this->placementCompany('PEN_ASSIGNED', 'Placement Assigned');
        $otherPlacementCompany = $this->placementCompany('PEN_ASSIGNED_OTHER', 'Other Placement Assigned');
        $implementingCompany = $this->implementingCompany('PEL_ASSIGNED', 'Implementing Assigned');
        $user = $this->user('rakan_kolaborasi', ['id_syarikat_penempatan' => $placementCompany->id_syarikat]);
        $talent = $this->talent([
            'talent_code' => 'TAL-ASSIGNED',
            'id_pelaksana' => $implementingCompany->id_pelaksana,
            'id_syarikat_penempatan' => $otherPlacementCompany->id_syarikat,
            'status' => 'assigned',
            'status_aktif' => 'Aktif',
        ]);

        $this->actingAs($user)
            ->post(route('portal.request-applicant', $talent), [
                'implementing_company_id' => $implementingCompany->id_pelaksana,
                'request_message' => 'Already assigned',
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('applicant_requests', 0);
    }

    public function test_placement_company_request_requires_implementation_then_admin_approval(): void
    {
        $placementCompany = $this->placementCompany('PEN_002', 'Placement B');
        $implementingCompany = $this->implementingCompany('PEL_003', 'Implementing C');
        $placementUser = $this->user('rakan_kolaborasi', ['id_syarikat_penempatan' => $placementCompany->id_syarikat]);
        $implementationUser = $this->user('syarikat_pelaksana', ['id_pelaksana' => $implementingCompany->id_pelaksana]);
        $admin = $this->user('pmo_admin');
        $talent = $this->talent(['talent_code' => 'TAL-005', 'full_name' => 'Requested Talent']);

        $this->actingAs($placementUser)
            ->post(route('portal.request-applicant', $talent), [
                'implementing_company_id' => $implementingCompany->id_pelaksana,
                'request_message' => 'Please consider this talent.',
            ])
            ->assertRedirect();

        $applicantRequest = ApplicantRequest::firstOrFail();
        $this->assertSame(ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW, $applicantRequest->status);
        $this->assertSame($implementingCompany->id_pelaksana, $applicantRequest->implementing_company_id);
        $this->assertSame($placementCompany->id_syarikat, $applicantRequest->placement_company_id);

        $this->actingAs($placementUser)
            ->post(route('admin.applicant-requests.approve', $applicantRequest))
            ->assertForbidden();

        $this->actingAs($implementationUser)
            ->post(route('admin.applicant-requests.accept-implementation', $applicantRequest))
            ->assertRedirect();

        $this->assertSame(
            ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL,
            $applicantRequest->fresh()->status
        );
        $this->assertNull($talent->fresh()->id_pelaksana);
        $this->assertNull($talent->fresh()->id_syarikat_penempatan);

        $this->actingAs($admin)
            ->post(route('admin.applicant-requests.approve', $applicantRequest))
            ->assertRedirect();

        $applicantRequest->refresh();
        $talent->refresh();

        $this->assertSame(ApplicantRequest::STATUS_APPROVED, $applicantRequest->status);
        $this->assertSame($implementingCompany->id_pelaksana, $talent->id_pelaksana);
        $this->assertSame($placementCompany->id_syarikat, $talent->id_syarikat_penempatan);

        $this->actingAs($placementUser)
            ->get(route('portal.index'))
            ->assertOk()
            ->assertDontSee('Requested Talent');
    }

    public function test_rejections_leave_talent_available_and_visible_to_requester(): void
    {
        $placementCompany = $this->placementCompany('PEN_003', 'Placement C');
        $implementingCompany = $this->implementingCompany('PEL_004', 'Implementing D');
        $placementUser = $this->user('rakan_kolaborasi', ['id_syarikat_penempatan' => $placementCompany->id_syarikat]);
        $implementationUser = $this->user('syarikat_pelaksana', ['id_pelaksana' => $implementingCompany->id_pelaksana]);
        $talent = $this->talent(['talent_code' => 'TAL-006', 'full_name' => 'Rejected Talent']);

        $applicantRequest = ApplicantRequest::create([
            'talent_id' => $talent->id,
            'implementing_company_id' => $implementingCompany->id_pelaksana,
            'placement_company_id' => $placementCompany->id_syarikat,
            'requested_by_user_id' => $placementUser->id,
            'status' => ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW,
        ]);

        $this->actingAs($implementationUser)
            ->post(route('admin.applicant-requests.reject-implementation', $applicantRequest), [
                'review_notes' => 'Not suitable right now.',
            ])
            ->assertRedirect();

        $this->assertSame(ApplicantRequest::STATUS_REJECTED_BY_IMPLEMENTATION, $applicantRequest->fresh()->status);
        $this->assertNull($talent->fresh()->id_pelaksana);
        $this->assertNull($talent->fresh()->id_syarikat_penempatan);

        $this->actingAs($placementUser)
            ->get(route('admin.applicant-requests.index'))
            ->assertOk()
            ->assertSee('Rejected Talent')
            ->assertSee('Rejected by Implementation Company');
    }

    public function test_implementation_company_can_only_review_own_requests(): void
    {
        $placementCompany = $this->placementCompany('PEN_SCOPE', 'Placement Scope');
        $ownImplementingCompany = $this->implementingCompany('PEL_SCOPE_OWN', 'Implementing Scope Own');
        $otherImplementingCompany = $this->implementingCompany('PEL_SCOPE_OTHER', 'Implementing Scope Other');
        $placementUser = $this->user('rakan_kolaborasi', ['id_syarikat_penempatan' => $placementCompany->id_syarikat]);
        $ownImplementationUser = $this->user('syarikat_pelaksana', ['id_pelaksana' => $ownImplementingCompany->id_pelaksana]);
        $otherImplementationUser = $this->user('syarikat_pelaksana', ['id_pelaksana' => $otherImplementingCompany->id_pelaksana]);
        $talent = $this->talent(['talent_code' => 'TAL-SCOPE']);

        $applicantRequest = ApplicantRequest::create([
            'talent_id' => $talent->id,
            'implementing_company_id' => $ownImplementingCompany->id_pelaksana,
            'placement_company_id' => $placementCompany->id_syarikat,
            'requested_by_user_id' => $placementUser->id,
            'status' => ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW,
        ]);

        $this->actingAs($otherImplementationUser)
            ->post(route('admin.applicant-requests.accept-implementation', $applicantRequest))
            ->assertForbidden();

        $this->actingAs($ownImplementationUser)
            ->post(route('admin.applicant-requests.accept-implementation', $applicantRequest))
            ->assertRedirect();

        $this->assertSame(ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL, $applicantRequest->fresh()->status);
    }

    public function test_admin_rejection_after_implementation_acceptance_does_not_assign_talent(): void
    {
        $placementCompany = $this->placementCompany('PEN_004', 'Placement D');
        $implementingCompany = $this->implementingCompany('PEL_005', 'Implementing E');
        $placementUser = $this->user('rakan_kolaborasi', ['id_syarikat_penempatan' => $placementCompany->id_syarikat]);
        $implementationUser = $this->user('syarikat_pelaksana', ['id_pelaksana' => $implementingCompany->id_pelaksana]);
        $admin = $this->user('super_admin');
        $talent = $this->talent(['talent_code' => 'TAL-007']);

        $applicantRequest = ApplicantRequest::create([
            'talent_id' => $talent->id,
            'implementing_company_id' => $implementingCompany->id_pelaksana,
            'placement_company_id' => $placementCompany->id_syarikat,
            'requested_by_user_id' => $placementUser->id,
            'status' => ApplicantRequest::STATUS_PENDING_IMPLEMENTATION_REVIEW,
        ]);

        $this->actingAs($implementationUser)
            ->post(route('admin.applicant-requests.accept-implementation', $applicantRequest))
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.applicant-requests.reject', $applicantRequest), [
                'review_notes' => 'Capacity limit reached.',
            ])
            ->assertRedirect();

        $this->assertSame(ApplicantRequest::STATUS_REJECTED_BY_ADMIN, $applicantRequest->fresh()->status);
        $this->assertNull($talent->fresh()->id_pelaksana);
        $this->assertNull($talent->fresh()->id_syarikat_penempatan);
    }

    public function test_approved_request_appears_in_both_manage_placement_scopes(): void
    {
        $placementCompany = $this->placementCompany('PEN_005', 'Placement E');
        $implementingCompany = $this->implementingCompany('PEL_006', 'Implementing F');
        $placementUser = $this->user('rakan_kolaborasi', ['id_syarikat_penempatan' => $placementCompany->id_syarikat]);
        $implementationUser = $this->user('syarikat_pelaksana', ['id_pelaksana' => $implementingCompany->id_pelaksana]);
        $talent = $this->talent([
            'talent_code' => 'TAL-008',
            'full_name' => 'Approved Placement Talent',
            'id_pelaksana' => $implementingCompany->id_pelaksana,
            'id_syarikat_penempatan' => $placementCompany->id_syarikat,
            'status' => 'assigned',
            'status_aktif' => 'Aktif',
        ]);

        $this->actingAs($implementationUser)
            ->get(route('admin.manage-placement.index'))
            ->assertOk()
            ->assertSee('Approved Placement Talent');

        $this->actingAs($placementUser)
            ->get(route('admin.manage-placement.index'))
            ->assertOk()
            ->assertSee('Approved Placement Talent');

        $this->assertSame($placementCompany->id_syarikat, $talent->fresh()->id_syarikat_penempatan);
    }

    public function test_mindef_viewer_has_read_only_request_access(): void
    {
        $placementCompany = $this->placementCompany('PEN_006', 'Placement F');
        $implementingCompany = $this->implementingCompany('PEL_007', 'Implementing G');
        $requester = $this->user('rakan_kolaborasi', ['id_syarikat_penempatan' => $placementCompany->id_syarikat]);
        $viewer = $this->user('mindef_viewer');
        $talent = $this->talent(['talent_code' => 'TAL-009']);
        $applicantRequest = ApplicantRequest::create([
            'talent_id' => $talent->id,
            'implementing_company_id' => $implementingCompany->id_pelaksana,
            'placement_company_id' => $placementCompany->id_syarikat,
            'requested_by_user_id' => $requester->id,
            'status' => ApplicantRequest::STATUS_PENDING_ADMIN_APPROVAL,
        ]);

        $this->actingAs($viewer)
            ->get(route('admin.applicant-requests.index'))
            ->assertOk();

        $this->actingAs($viewer)
            ->post(route('admin.applicant-requests.approve', $applicantRequest))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->post(route('admin.applicant-requests.reject', $applicantRequest))
            ->assertForbidden();
    }

    private function user(string $roleName, array $attributes = []): User
    {
        $role = Role::firstOrCreate(['role_name' => $roleName]);

        return User::create(array_merge([
            'full_name' => ucfirst(str_replace('_', ' ', $roleName)),
            'email' => $roleName . uniqid() . '@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'status' => 'active',
            'language' => 'en',
        ], $attributes));
    }

    private function implementingCompany(string $id, string $name): SyarikatPelaksana
    {
        return SyarikatPelaksana::create([
            'id_pelaksana' => $id,
            'nama_syarikat' => $name,
            'projek_kontrak' => 'Project',
            'pic_syarikat' => 'PIC',
            'email_pic' => $id . '@example.test',
        ]);
    }

    private function placementCompany(string $id, string $name): SyarikatPenempatan
    {
        return SyarikatPenempatan::create([
            'id_syarikat' => $id,
            'nama_syarikat' => $name,
            'sektor_industri' => 'Technology',
            'pic' => 'PIC',
            'no_telefon_pic' => '0123456789',
            'email_pic' => $id . '@example.test',
        ]);
    }

    private function talent(array $attributes = []): Talent
    {
        return Talent::create(array_merge([
            'talent_code' => 'TAL-' . uniqid(),
            'id_graduan' => 'GRD-' . uniqid(),
            'full_name' => 'Test Talent',
            'ic_passport_no' => 'IC-' . uniqid(),
            'email' => 'talent' . uniqid() . '@example.test',
            'public_visibility' => true,
            'status' => 'approved',
            'status_aktif' => 'Aktif',
        ], $attributes));
    }
}
