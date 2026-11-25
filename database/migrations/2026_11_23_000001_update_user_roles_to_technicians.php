<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace the single `staff` role with two technician roles.
     * Everyone who was staff becomes a hardware technician, which is
     * also the default for new users and the default assignment type
     * on order creation. An admin can change a person to software
     * technician in Manage Users afterwards.
     */
    public function up(): void
    {
        DB::table('users')->where('role', 'staff')->update(['role' => 'hardware_technician']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('hardware_technician')->change();
        });
    }

    public function down(): void
    {
        DB::table('users')
            ->whereIn('role', ['hardware_technician', 'software_technician'])
            ->update(['role' => 'staff']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('staff')->change();
        });
    }
};
