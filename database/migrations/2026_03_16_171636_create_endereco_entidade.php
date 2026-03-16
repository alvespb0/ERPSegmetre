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
        Schema::create('endereco_entidade', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entidade_id');
            $table->string('rua');
            $table->string('bairro');
            $table->string('numero')->default('n/a');
            $table->string('cep');
            $table->string('cidade');
            $table->string('uf');
            $table->string('complemento')->nullable();
            $table->foreign('entidade_id')->references('id')->on('entidade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endereco_entidade');
    }
};
