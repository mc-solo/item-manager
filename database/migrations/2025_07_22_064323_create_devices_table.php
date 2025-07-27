<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('client_name');
            $table->string('client_number');

            $table->enum('device_type', ['mobile', 'laptop', 'tablet', 'pc', 'mac', 'other'])->default('mobile');
            $table->string('device_model');
            $table->string('serial_number')->nullable();
            $table->string('imei')->nullable();

            $table->text('issue_description')->nullable();
            $table->enum('repair_status', ['pending', 'in_progress', 'completed', 'cancelled', 'delivered'])->default('pending');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
