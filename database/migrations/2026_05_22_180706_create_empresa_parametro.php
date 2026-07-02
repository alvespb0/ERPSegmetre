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
        Schema::create('empresa_parametro', function (Blueprint $table) {
            $table->id();
            $table->string('razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->string('cnpj')->unique();
            $table->string('inscricao_estadual')->nullable();
            $table->string('inscricao_municipal')->nullable();
            $table->string('cnae_principal')->nullable();
            $table->string('cep');
            $table->string('logradouro');
            $table->string('bairro');
            $table->string('numero')->nullable();;
            $table->string('complemento')->nullable();
            $table->string('cidade');
            $table->string('uf', 2);
            $table->string('telefone')->nullable();
            $table->string('email_financeiro')->nullable();
            $table->string('logo_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_parametro');
    }
};
