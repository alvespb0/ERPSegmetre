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
        Schema::create('solicitacoes_pagamento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parcela_id');
            $table->unsignedBigInteger('movimentacao_id')->nullable();
            $table->unsignedBigInteger('empresa_parametro_id')->nullable();
            $table->string('chave_idempotente');
            $table->enum('tipo', ['codigo_barras','pix','pix_copia_cola','tributo']);
            $table->string('identificador');
            $table->decimal('valor', 15, 2);
            $table->dateTime('data_solicitacao');
            $table->dateTime('data_pagamento')->nullable();
            $table->string('comprovante_path')->nullable();
            $table->enum('status', ['pendente','recusado','pago',])->default('pendente');
            $table->foreign('parcela_id')->references('id')->on('parcelas')->onDelete('cascade');
            $table->foreign('movimentacao_id')->references('id')->on('movimentacoes')->nullOnDelete();
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
            $table->unique(['chave_idempotente', 'empresa_parametro_id'], 'sol_pag_chave_emp_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacoes_pagamento');
    }
};
