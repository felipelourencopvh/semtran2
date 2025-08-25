<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('relatorio_atividades', function (Blueprint $t) {
            $t->id();
            $t->foreignId('report_id')->constrained('reports')->cascadeOnDelete();

            $t->foreignId('tipo_atividade_id')->constrained('tipos_atividade');
            $t->foreignId('situacao_atividade_id')->constrained('situacoes_atividade');
            $t->foreignId('medida_atividade_id')->constrained('medidas_atividade');

            $t->string('endereco')->nullable();
            $t->unsignedInteger('ordem')->default(0);
            $t->timestamps();
        });

        if (!Schema::hasColumn('reports', 'descricao_manual')) {
            Schema::table('reports', function (Blueprint $t) {
                $t->text('descricao_manual')->nullable();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('relatorio_atividades');
        if (Schema::hasColumn('reports', 'descricao_manual')) {
            Schema::table('reports', function (Blueprint $t) {
                $t->dropColumn('descricao_manual');
            });
        }
    }
};
