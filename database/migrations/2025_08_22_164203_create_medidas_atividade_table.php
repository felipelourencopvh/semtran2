<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('medidas_atividade', function (Blueprint $t) {
            $t->id();
            $t->foreignId('situacao_atividade_id')->constrained('situacoes_atividade')->cascadeOnDelete();
            $t->string('slug')->unique();
            $t->string('nome');
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('medidas_atividade');
    }
};

