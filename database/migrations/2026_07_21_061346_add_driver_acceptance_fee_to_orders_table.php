<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            /*
             * La tarifa que se descuenta al conductor cuando toma el pedido.
             */
            $table->bigInteger('driver_acceptance_fee_cents')
                ->default(500)
                ->after('driver_id');

            /*
             * Marca cuándo se cobró la tarifa.
             */
            $table->timestamp('driver_fee_charged_at')
                ->nullable()
                ->after('driver_acceptance_fee_cents');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'driver_acceptance_fee_cents',
                'driver_fee_charged_at',
            ]);
        });
    }
};