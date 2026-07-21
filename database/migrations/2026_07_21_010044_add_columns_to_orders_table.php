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
        Schema::table('orders', function (Blueprint $table) {
           $table->string('order_number')->nullable()->after('id');
              $table->string('payment_status')->nullable()->after('status');
              $table->string('payment_method')->nullable()->after('payment_status');
              $table->string('notes')->nullable()->after('payment_method');
              $table->decimal('tax', 10, 2)->nullable()->after('notes');
        });

         Schema::table('users', function (Blueprint $table) {
           $table->string('address')->nullable()->after('name');
              $table->string('apartment')->nullable()->after('address');
              $table->string('city')->nullable()->after('apartment');
              $table->string('state')->nullable()->after('city');
              $table->string('zip_code')->nullable()->after('state');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
