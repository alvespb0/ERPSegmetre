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
        Schema::create('boleto_cobranca', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parcela_id');
            $table->unsignedBigInteger('configuracao_cobranca_id')->nullable();
            $table->unsignedBigInteger('arquivo_remessa_id')->nullable();
            $table->unsignedBigInteger('arquivo_retorno_id')->nullable();
            $table->string('nosso_numero', 20)->nullable()->unique(); # identificador bancário do boleto
            $table->string('modalidade'); # 01 Cobrança simples ; 04 Cobrança vinculada ; 03 Cobrança Caucionada ; 05 Carnê de Pagamentos 
            $table->bigInteger('sequencial_boleto');
            $table->string('numero_documento'); # identificador interno ERP
            $table->string('linha_digitavel')
                ->nullable();
            $table->string('codigo_barras')
                ->nullable();

            $table->enum('status', [
                'pendente',
                'remetido',
                'registrado',
                'liquidado',
                'baixado',
                'rejeitado',
                'cancelado',
            ])->default('pendente');

            $table->decimal('valor_multa', 15, 2)->default(0);
            $table->decimal('valor_juros', 15, 2)->default(0);
            $table->timestamp('data_registro')->nullable();
            $table->timestamp('data_liquidacao')->nullable();
            $table->foreign('parcela_id')->references('id')->on('parcelas')->onDelete('cascade');
            $table->foreign('configuracao_cobranca_id')->references('id')->on('configuracao_cobranca')->nullOnDelete();
            $table->foreign('arquivo_remessa_id')->references('id')->on('arquivo_remessa')->nullOnDelete();
            $table->foreign('arquivo_retorno_id')->references('id')->on('arquivo_retorno')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boleto_cobranca');
    }
};
