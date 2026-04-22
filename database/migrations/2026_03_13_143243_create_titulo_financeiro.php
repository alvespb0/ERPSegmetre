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
        Schema::create('titulo_financeiro', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('centro_custo_id')->nullable();
            $table->unsignedBigInteger('categoria_financeira_id')->nullable();
            $table->unsignedBigInteger('entidade_id');
            $table->string('descricao');
            $table->text('observacoes')->nullable();
            $table->string('numero_nf')->nullable(); # pode não aplicar
            $table->decimal('valor_total', 10, 2);
            $table->date('data_emissao');
            $table->enum('tipo', ['pagar', 'receber']);
            $table->enum('status', ['ativo', 'cancelado', 'renegociado'])->default('ativo');
            $table->softDeletes();
            $table->foreign('centro_custo_id')->references('id')->on('centro_custo')->nullOnDelete();
            $table->foreign('categoria_financeira_id')->references('id')->on('categoria_financeira')->nullOnDelete();
            $table->foreign('entidade_id')->references('id')->on('entidade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titulo_financeiro');
    }
};
