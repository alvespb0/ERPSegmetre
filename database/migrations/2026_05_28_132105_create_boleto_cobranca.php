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
            $table->bigInteger('sequencial_boleto');
            $table->string('numero_documento'); # identificador interno ERP
            $table->string('modalidade'); # 01 Cobrança simples ; 04 Cobrança vinculada ; 03 Cobrança Caucionada ; 05 Carnê de Pagamentos 
            $table->enum('especie_documento', [
                'CH',  // Cheque
                'DM',  // Duplicata Mercantil
                'DMI', // Duplicata Mercantil Indicação
                'DS',  // Duplicata de Serviço
                'DSI', // Duplicata Serviço Indicação
                'DR',  // Duplicata Rural
                'LC',  // Letra de Câmbio
                'NCC', // Nota de Crédito Comercial
                'NCE', // Nota de Crédito Exportação
                'NCI', // Nota de Crédito Industrial
                'NCR', // Nota de Crédito Rural
                'NP',  // Nota Promissória
                'NPR', // Nota Promissória Rural
                'TM',  // Triplicata Mercantil
                'TS',  // Triplicata de Serviço
                'NS',  // Nota de Seguro
                'RC',  // Recibo
                'FAT', // Fatura
                'ND',  // Nota de Débito
                'AP',  // Apólice de Seguro
                'ME',  // Mensalidade Escolar
                'PC',  // Pagamento de Consórcio
                'NF',  // Nota Fiscal
                'DD',  // Documento de Dívida
                'BDP', // Boleto Proposta
                'OU',  // Outros
            ]);
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
            $table->enum('codigo_multa',[
                '0', // isento
                '1', // valor fixo
                '2', // percentual
            ]);
            $table->enum('codigo_juros', [
                '0', // isento
                '1', // valor por dia
                '2', // taxa mensal
            ])->default('0');
            $table->enum('codigo_protesto', [
                '1', // protestar dias corridos
                '2', // valor dias uteis
                '3', // não protestar
                '8', // negativação sem protesto
                '9', // negativação automática
            ])->default('3');

            $table->decimal('valor_multa', 15, 2)->default(0);
            $table->decimal('valor_juros', 15, 2)->default(0);
            
            $table->timestamp('data_registro')->nullable();
            $table->timestamp('data_multa')->nullable(); # deve ser menor que data limite de pagamento da duplicata e maior que a data de vencimento da parcela
            $table->timestamp('data_liquidacao')->nullable();
            $table->integer('prazo_protesto')->nullable(); # dias após vencimento



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
