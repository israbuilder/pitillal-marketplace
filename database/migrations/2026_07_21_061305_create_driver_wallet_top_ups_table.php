<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_wallet_top_ups', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('driver_wallet_id')
                ->constrained('driver_wallets')
                ->cascadeOnDelete();

            $table->bigInteger('amount_cents');
            $table->char('currency', 3)->default('usd');

            /*
             * pending
             * paid
             * failed
             * expired
             * refunded
             */
            $table->string('status', 30)->default('pending');

            $table->string('stripe_checkout_session_id')
                ->nullable()
                ->unique();

            $table->string('stripe_payment_intent_id')
                ->nullable()
                ->unique();

            $table->string('stripe_event_id')
                ->nullable()
                ->unique();

            $table->timestamp('paid_at')->nullable();

            $table->jsonb('metadata')->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_wallet_top_ups');
    }
};