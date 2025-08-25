<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Campos novos
            $table->string('telefone', 20)->nullable()->after('email');
            $table->string('matricula', 30)->nullable()->unique()->after('telefone');
            $table->string('nome_farda', 120)->nullable()->after('matricula');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remover índice único antes da coluna
            $table->dropUnique('users_matricula_unique');
            $table->dropColumn(['telefone', 'matricula', 'nome_farda']);
        });
    }
};
