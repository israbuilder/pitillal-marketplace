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
        Schema::table('driver_locations', function (Blueprint $table) {
             $table->decimal('accuracy', 10, 2)->nullable();
            $table->decimal('heading', 10, 2)->nullable();
            $table->decimal('speed', 10, 2)->nullable();
            $table->timestamp('recorded_at')->nullable();

            $table->index(['driver_id', 'recorded_at']);
            $table->index(['order_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliver_locations', function (Blueprint $table) {
            //
        });
    }
};
