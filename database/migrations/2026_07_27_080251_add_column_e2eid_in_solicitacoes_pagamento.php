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
        Schema::table('solicitacoes_pagamento', function (Blueprint $table) {
            $table->string('end_to_end_id')->nullable();
            $table->string('id_pagamento')->nullable();
            $table->string('codigo_autenticador')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitacoes_pagamento', function (Blueprint $table) {
            //
        });
    }
};
