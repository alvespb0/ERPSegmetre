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
        Schema::table('integracoes', function (Blueprint $table) {
            $table->string('provider')->after('slug')->nullable(); # classe responsavel por executar essa integracao
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integracoes', function (Blueprint $table) {
            //
        });
    }
};
