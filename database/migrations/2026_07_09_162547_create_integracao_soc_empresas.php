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
        Schema::create('integracao_soc_empresas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entidade_id');
            $table->unsignedBigInteger('codigo_empresa');
            $table->unsignedBigInteger('codigo_unidade')->nullable();
            $table->string('nome_unidade')->nullable();
            $table->unique(['codigo_empresa', 'codigo_unidade'], 'soc_empresa_unidade_unique'); # Nao pode haver codigo_empresa com mesmo codigo de unidade mais de uma vez (exemplo)
            $table->foreign('entidade_id')->references('id')->on('entidade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integracao_soc_empresas');
    }
};
