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
        Schema::table('anexos', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('arquivo_remessa', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('arquivo_retorno', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('banco', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('boleto_cobranca', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('categoria_financeira', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('centro_custo', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('conta', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('contato', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('endereco_entidade', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('entidade', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('forma_pagamento', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('integracao_credenciais', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('integracao_soc_empresas', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('movimentacoes', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('parcelas', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('tipo_conta', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('titulo_financeiro', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        /*
        |--------------------------------------------------------------------------
        | Foreign Keys
        |--------------------------------------------------------------------------
        */

        Schema::table('anexos', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('arquivo_remessa', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('arquivo_retorno', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('banco', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('boleto_cobranca', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('categoria_financeira', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('centro_custo', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('conta', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('contato', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('endereco_entidade', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('entidade', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('forma_pagamento', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('integracao_credenciais', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('integracao_soc_empresas', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('movimentacoes', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('parcelas', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('tipo_conta', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });

        Schema::table('titulo_financeiro', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
