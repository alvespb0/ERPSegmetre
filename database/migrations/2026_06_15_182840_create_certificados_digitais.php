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
        Schema::create('certificados_digitais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_parametro_id');
            $table->string('nome_certificado');
            $table->string('cert_path');
            $table->text('senha');
            $table->string('cpf_cnpj')->nullable();
            $table->string('titular')->nullable();
            $table->string('numero_serie')->nullable();
            $table->dateTime('emitido_em')->nullable();
            $table->dateTime('vence_em')->nullable();
            $table->softDeletes();
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados_digitais');
    }
};
