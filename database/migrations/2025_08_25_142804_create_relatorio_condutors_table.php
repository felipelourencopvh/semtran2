<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('relatorio_condutores', function (Blueprint $t) {
            $t->id();
            $t->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $t->foreignId('veiculo_id')->constrained('veiculos');
            $t->foreignId('motorista_id')->constrained('users'); // integrante da equipe
            $t->string('matricula')->nullable(); // cache do cadastro na época
            $t->unsignedInteger('odometro_inicial')->nullable();
            $t->unsignedInteger('odometro_final')->nullable();
            $t->unsignedInteger('ordem')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('relatorio_condutores');
    }
};
