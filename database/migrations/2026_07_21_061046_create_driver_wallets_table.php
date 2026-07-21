<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_wallets', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * El saldo siempre se guarda en centavos.
             *
             * $25.50 = 2550
             */
            $table->bigInteger('balance_cents')->default(0);

            $table->char('currency', 3)->default('usd');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_wallets');
    }
};