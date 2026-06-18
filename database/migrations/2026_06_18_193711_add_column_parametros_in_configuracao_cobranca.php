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
        Schema::table('configuracao_cobranca', function (Blueprint $table) {
            $table->enum('codigo_juros', [
                '0', // Isento
                '1', // Valor por dia
                '2', // Taxa mensal
            ])->default('0')->after('codigo_cedente')->nullable();
            $table->decimal('valor_juros', 10, 4)->nullable()->after('codigo_juros')->nullable();
            $table->integer('dias_inicio_juros')->default(1)->after('valor_juros')->nullable(); # dias após vencimento para aplicar juro

            $table->enum('codigo_multa', [
                '0', // Isento
                '1', // Valor Fixo
                '2', // Percentual
            ])->default('0')->after('dias_inicio_juros')->nullable();
            $table->decimal('valor_multa', 10, 2)->nullable()->after('codigo_multa')->nullable();
            $table->integer('dias_inicio_multa')->default(1)->after('valor_multa')->nullable(); # dias após vencimento para aplicar multa

            $table->integer('dias_limite_pagamento')->default(30)->after('dias_inicio_multa')->nullable(); # dias para baixa automatica do boleto após o vencimento
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracao_cobranca', function (Blueprint $table) {
            //
        });
    }
};
