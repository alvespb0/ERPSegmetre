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
        Schema::create('integracao_credenciais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('integracao_id');

            /**
             * Credenciais Genéricas
             */
            $table->string('username')->nullable();
            $table->text('password_enc')->nullable();

            /**
             * OAuth2.0
             */

            $table->string('client_id')->nullable();
            $table->text('client_secret_enc')->nullable();
            $table->longText('access_token')->nullable();
            $table->longText('refresh_token')->nullable();
            $table->dateTime('token_expires_at')->nullable();

            /**
             * Certificado Digital
             */
            $table->unsignedBigInteger('certificado_digital_id')->nullable();

            $table->foreign('integracao_id')->references('id')->on('integracoes')->onDelete('cascade');
            $table->foreign('certificado_digital_id')->references('id')->on('certificados_digitais')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integracao_credenciais');
    }
};
