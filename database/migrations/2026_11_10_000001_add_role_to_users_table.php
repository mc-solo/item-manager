<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a role to users. Admin can do everything, including delete
     * devices and manage other users. Staff can create, view, edit and
     * complete devices, but cannot delete them or manage users.
     * Everyone created before this migration stays staff, except the
     * seeded shop account which the seeder marks as admin.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('staff')->after('email');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
