<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('situacoes_atividade', function (Blueprint $t) {
            $t->id();
            $t->foreignId('tipo_atividade_id')->constrained('tipos_atividade')->cascadeOnDelete();
            $t->string('slug')->unique();
            $t->string('nome');
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('situacoes_atividade');
    }
};

