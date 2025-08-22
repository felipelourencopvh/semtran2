<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // criador
            $table->boolean('same_day')->default(true);

            // Guardaremos o período consolidado:
            $table->timestampTz('start_at');
            $table->timestampTz('end_at');

            $table->string('service_type'); // 'ordinario' | 'extraordinario'
            $table->string('shift');        // 'plantao' | 'manha' | 'tarde' | 'noite'

            $table->jsonb('meta')->nullable(); // espaço p/ futuras perguntas
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
