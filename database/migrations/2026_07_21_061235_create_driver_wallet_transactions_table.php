<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_wallet_transactions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('driver_wallet_id')
                ->constrained('driver_wallets')
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            /*
             * credit: aumenta saldo
             * debit: disminuye saldo
             * refund: devuelve saldo
             * adjustment: ajuste administrativo
             */
            $table->string('type', 30);

            /*
             * Ejemplos:
             * stripe_top_up
             * order_acceptance
             * order_acceptance_refund
             * admin_adjustment
             */
            $table->string('reason', 60);

            $table->bigInteger('amount_cents');

            $table->bigInteger('balance_before_cents');
            $table->bigInteger('balance_after_cents');

            $table->string('reference_type')->nullable();
            $table->string('reference_id')->nullable();

            $table->string('description')->nullable();

            $table->jsonb('metadata')->nullable();

            $table->timestamps();

            $table->index([
                'driver_wallet_id',
                'created_at',
            ]);

            /*
             * Evita aplicar dos veces una misma operación externa.
             */
            $table->unique([
                'reference_type',
                'reference_id',
                'reason',
            ], 'driver_wallet_transactions_reference_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_wallet_transactions');
    }
};