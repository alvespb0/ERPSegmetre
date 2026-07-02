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
        Schema::create('arquivo_retorno', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('configuracao_cobranca_id')->nullable();
            $table->string('numero_retorno');
            $table->string('nome_arquivo');
            $table->string('path');
            $table->enum('status', [
                'pendente',
                'processado',
                'erro',
            ])->default('pendente');
            $table->timestamp('data_processamento')->nullable();
            $table->foreign('configuracao_cobranca_id')->references('id')->on('configuracao_cobranca')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arquivo_retorno');
    }
};
