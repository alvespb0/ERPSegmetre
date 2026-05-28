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
        Schema::create('configuracao_cobranca', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conta_id');
            $table->unsignedBigInteger('empresa_parametro_id');
            $table->string('codigo_cedente');
            $table->string('carteira')->nullable();
            $table->string('layout_cnab')->default('240');
            $table->enum('ambiente', ['homologacao', 'producao']);
            $table->integer('numero_inicial_cobranca')->default(1);
            $table->foreign('conta_id')->references('id')->on('conta')->onDelete('cascade');
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_cobranca');
    }
};
