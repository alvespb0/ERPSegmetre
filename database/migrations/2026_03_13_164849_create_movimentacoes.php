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
        Schema::create('movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forma_pagamento_id')->nullable();
            $table->unsignedBigInteger('parcela_id');
            $table->unsignedBigInteger('conta_id')->nullable();
            $table->decimal('valor_pago', 10, 2);
            $table->date('data_pagamento');
            $table->foreign('forma_pagamento_id')->references('id')->on('forma_pagamento')->nullOnDelete();
            $table->foreign('parcela_id')->references('id')->on('parcelas')->onDelete('cascade');
            $table->foreign('conta_id')->references('id')->on('conta')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacoes');
    }
};
