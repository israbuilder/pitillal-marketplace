<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('business_id')->constrained()->cascadeOnDelete();
    $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();

    $table->string('status')->default('pending');
    // pending, accepted_by_business, ready_for_pickup, assigned_driver,
    // picked_up, delivered, cancelled

    $table->decimal('subtotal', 10, 2)->default(0);
    $table->decimal('delivery_fee', 10, 2)->default(0);
    $table->decimal('total', 10, 2)->default(0);

    $table->text('delivery_address');
    $table->decimal('delivery_lat', 10, 7)->nullable();
    $table->decimal('delivery_lng', 10, 7)->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
