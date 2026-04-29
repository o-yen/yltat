<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('role_name', 'super_admin')->first();

        if ($superAdminRole) {
            User::firstOrCreate(
                ['email' => 'admin@yltat.gov.my'],
                [
                    'full_name' => 'System Administrator',
                    'email' => 'admin@yltat.gov.my',
                    'password' => Hash::make('Admin@1234'),
                    'role_id' => $superAdminRole->id,
                    'status' => 'active',
                    'language' => 'ms',
                ]
            );
        }
    }
}
