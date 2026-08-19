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
        Schema::table('solicitacoes_pagamento', function (Blueprint $table) {
            $table->unsignedBigInteger('parcela_id')->nullable()->change();
            $table->enum('status', [
                'pendente',
                'recusado',
                'pago',
                'cancelado',
                'agendado',
                'pendente_assinatura',
                'em_processamento',
            ])->default('pendente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitacoes_pagamento', function (Blueprint $table) {
            //
        });
    }
};
