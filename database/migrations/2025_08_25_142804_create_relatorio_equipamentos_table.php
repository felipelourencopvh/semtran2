<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('relatorio_equipamentos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $t->string('tipo'); // cones, cavaletes, barreiras, outros
            $t->string('outro_texto')->nullable();
            $t->unsignedInteger('quantidade')->default(1);
            $t->unsignedInteger('ordem')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('relatorio_equipamentos');
    }
};
