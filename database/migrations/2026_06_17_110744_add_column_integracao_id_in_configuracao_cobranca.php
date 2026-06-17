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
        Schema::table('configuracao_cobranca', function (Blueprint $table) {
            $table->unsignedBigInteger('integracao_id')->after('empresa_parametro_id')->nullable();
            $table->foreign('integracao_id')->references('id')->on('integracoes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracao_cobranca', function (Blueprint $table) {
            //
        });
    }
};
