<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            // ── Active Roles (PDF Spec) ──
            [
                'role_name'        => 'super_admin',
                'display_name'     => 'PMO Super Admin',
                'description'      => 'Full access to all modules. Programme management, approvals, exports, audit.',
                'permissions_json' => json_encode(['*']),
                'is_active'        => true,
                'sort_order'       => 1,
            ],
            [
                'role_name'        => 'pmo_admin',
                'display_name'     => 'PMO Admin',
                'description'      => 'Full access. Day-to-day programme operations and monitoring.',
                'permissions_json' => json_encode(['*']),
                'is_active'        => true,
                'sort_order'       => 2,
            ],
            [
                'role_name'        => 'mindef_viewer',
                'display_name'     => 'MINDEF Viewer',
                'description'      => 'View-only access to all modules. Oversight and compliance monitoring.',
                'permissions_json' => json_encode(['dashboard.view', 'reports.view', 'talents.view', 'companies.view', 'placements.view', 'budget.view', 'feedback.view']),
                'is_active'        => true,
                'sort_order'       => 3,
            ],
            [
                'role_name'        => 'syarikat_pelaksana',
                'display_name'     => 'Implementing Company',
                'description'      => 'Own company data only. Manage kewangan, status surat, and view logbooks.',
                'permissions_json' => json_encode(['dashboard.view', 'talents.view', 'placements.view', 'kewangan.view', 'kewangan.update', 'status_surat.view', 'status_surat.update', 'logbook.view']),
                'is_active'        => true,
                'sort_order'       => 4,
            ],
            [
                'role_name'        => 'rakan_kolaborasi',
                'display_name'     => 'Placement Company',
                'description'      => 'Own participants only. Manage kehadiran, logbook, and training records.',
                'permissions_json' => json_encode(['dashboard.view', 'talents.view', 'kehadiran.view', 'kehadiran.update', 'logbook.view', 'logbook.update', 'training.view', 'training.update']),
                'is_active'        => true,
                'sort_order'       => 5,
            ],
            [
                'role_name'        => 'talent',
                'display_name'     => 'Graduate / Protege',
                'description'      => 'Access own profile, daily logs, placement info, and allowance status.',
                'permissions_json' => json_encode(['profile.view', 'daily_logs.create', 'feedback.create']),
                'is_active'        => true,
                'sort_order'       => 6,
            ],

        ];

        // Remove legacy roles
        Role::whereIn('role_name', ['programme_admin', 'finance_admin', 'management_viewer', 'company_rep', 'public'])
            ->whereDoesntHave('users')
            ->delete();

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['role_name' => $role['role_name']],
                $role
            );
        }
    }
}
