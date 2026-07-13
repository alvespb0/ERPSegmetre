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
            $table->dropUnique('banco_cnpj_unique');
            $table->unique(
                ['empresa_parametro_id', 'cnpj'],
                'empresa_banco_cnpj_unique'
            );
            $table->index('empresa_parametro_id');
        });

        Schema::table('boleto_cobranca', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
            $table->dropUnique('boleto_cobranca_nosso_numero_unique');
            $table->unique(
                ['empresa_parametro_id', 'nosso_numero'],
                'empresa_boleto_cobranca_nosso_numero_unique'
            );
            $table->index('empresa_parametro_id');
        });

        Schema::table('categoria_financeira', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
            $table->dropUnique('categoria_financeira_nome_unique');
            $table->unique(
                ['empresa_parametro_id', 'nome'],
                'empresa_categoria_financeira_nome_unique'
            );
            $table->index('empresa_parametro_id');
        });

        Schema::table('centro_custo', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
            $table->dropUnique('centro_custo_nome_unique');
            $table->unique(
                ['empresa_parametro_id', 'nome'],
                'empresa_centro_custo_nome_unique'
            );
            $table->index('empresa_parametro_id');
        });

        Schema::table('conta', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
            $table->dropUnique('conta_nome_unique');
            $table->unique(
                ['empresa_parametro_id', 'nome'],
                'empresa_conta_nome_unique'
            );
            $table->index('empresa_parametro_id');
        });

        Schema::table('contato', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('endereco_entidade', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
        });

        Schema::table('entidade', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
            $table->dropUnique('entidade_cpf_cnpj_unique');
            $table->unique(
                ['empresa_parametro_id', 'cpf_cnpj'],
                'entidade_empresa_cpf_cnpj_unique'
            );
            $table->index('empresa_parametro_id');
        });

        Schema::table('forma_pagamento', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_parametro_id')->nullable()->after('id');
            $table->dropUnique('forma_pagamento_nome_unique');
            $table->unique(
                ['empresa_parametro_id', 'nome'],
                'empresa_forma_pagamento_nome_unique'
            );
            $table->index('empresa_parametro_id');
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
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('arquivo_remessa', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('arquivo_retorno', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('banco', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('boleto_cobranca', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('categoria_financeira', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('centro_custo', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('conta', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('contato', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('endereco_entidade', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('entidade', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('forma_pagamento', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('integracao_credenciais', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('integracao_soc_empresas', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('movimentacoes', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('parcelas', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('tipo_conta', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
        });

        Schema::table('titulo_financeiro', function (Blueprint $table) {
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->nullOnDelete();
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
