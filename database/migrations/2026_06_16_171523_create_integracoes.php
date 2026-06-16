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
        Schema::create('integracoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_parametro_id');
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->enum('escopo', ['sistema', 'banco', 'fiscal', 'externo'])->default('externo');
            $table->enum('tecnologia', ['rest','soap']);
            $table->enum('autenticacao', ['none', 'basic', 'bearer', 'oauth2', 'mtls', 'outro']);
            $table->string('autenticacao_especifica')->nullable();
            $table->string('endpoint');
            $table->boolean('nativa')->default(false);
            $table->softDeletes();
            $table->foreign('empresa_parametro_id')->references('id')->on('empresa_parametro')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integracoes');
    }
};
