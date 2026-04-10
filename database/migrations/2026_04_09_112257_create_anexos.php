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
        Schema::create('anexos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anexavel_id');
            $table->string('anexavel_type');
            $table->string('descricao')->nullable();
            $table->string('path');
            $table->enum('tipo', ['comprovante', 'pix', 'boleto', 'fatura', 'outros']);
            $table->index(['anexavel_id', 'anexavel_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anexos');
    }
};
