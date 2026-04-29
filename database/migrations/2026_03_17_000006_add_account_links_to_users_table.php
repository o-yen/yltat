<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('talent_id')->nullable()->after('role_id')->constrained('talents')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->after('talent_id')->constrained('companies')->nullOnDelete();
            $table->unique('talent_id');
            $table->unique('company_id');
        });

        DB::table('users')
            ->join('talents', 'talents.email', '=', 'users.email')
            ->whereNull('users.talent_id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('roles')
                    ->whereColumn('roles.id', 'users.role_id')
                    ->where('roles.role_name', 'talent');
            })
            ->update(['users.talent_id' => DB::raw('talents.id')]);

        DB::table('users')
            ->join('companies', 'companies.contact_email', '=', 'users.email')
            ->whereNull('users.company_id')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('roles')
                    ->whereColumn('roles.id', 'users.role_id')
                    ->where('roles.role_name', 'company_rep');
            })
            ->update(['users.company_id' => DB::raw('companies.id')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['talent_id']);
            $table->dropUnique(['company_id']);
            $table->dropConstrainedForeignId('talent_id');
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
