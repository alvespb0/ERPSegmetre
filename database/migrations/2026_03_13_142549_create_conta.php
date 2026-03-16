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
        Schema::create('conta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banco_id');
            $table->unsignedBigInteger('tipo_conta_id');
            $table->string('nome')->unique();
            $table->enum('modalidade', ['pj', 'pf']);
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->softDeletes();
            $table->foreign('banco_id')->references('id')->on('banco')->onDelete('cascade');
            $table->foreign('tipo_conta_id')->references('id')->on('tipo_conta')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conta');
    }
};
