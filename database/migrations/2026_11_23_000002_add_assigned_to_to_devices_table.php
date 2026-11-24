<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the technician an order is assigned to. Nullable because
     * an order can be created before any technician of that type
     * exists; in that case it stays unassigned and is only visible
     * to admins until someone is assigned.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('repair_status')->constrained('users')->nullOnDelete();
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropIndex(['assigned_to']);
            $table->dropColumn('assigned_to');
        });
    }
};
