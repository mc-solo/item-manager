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
            $table->enum('device_type', ['mobile', 'tablet', 'pc/mac', 'gadget', 'wearable', 'other'])->default('phone');
            $table->string('brand');
            $table->string('model');
            $table->string('imei')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('description')->nullable();

            // status info
            $table->enum('repair_status', ['received', 'in_progress', 'ready', 'delivered'])->default('received');

            // owner info
            $table->string('owner_name');
            $table->string('owner_number');

            // payment info
            $table->enum('payment_method', ['cash', 'transfer']);
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->decimal('price', 6, 2)->default(0.00);

            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('expected_delivery_at')->nullable();
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
