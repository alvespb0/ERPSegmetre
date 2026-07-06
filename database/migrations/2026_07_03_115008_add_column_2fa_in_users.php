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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('tipo', ['dev', 'admin', 'visualizador', 'pagador', 'cobranca']); # dev - desenvolvedor, admin - todas as funcionalidades, visualizador - apenas visualiza não cadastra e nem excluí nada, pagador - conta responsável por 'pagar' a unica que realiza pagamentos, cobranca - só realiza o cadastro das cobranças
            $table->boolean('two_factor_enabled')->default(false);
            $table->text('two_factor_secret')->nullable(); // deve ser salvo com crypt
            $table->timestamp('two_factor_confirmed_at')->nullable();

            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_login_ip')->nullable();

            $table->unsignedTinyInteger('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            $table->dropColumn('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
