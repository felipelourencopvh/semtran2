<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('veiculos', function (Blueprint $t) {
            $t->id();
            $t->string('placa')->unique();
            $t->string('marca');
            $t->string('especie'); // passageiro, carga, etc
            $t->string('modelo');
            $t->unsignedInteger('odometro_atual')->default(0);
            $t->foreignId('department_owner_id')->constrained('departments');
            $t->timestamps();
        });

        Schema::create('veiculo_departamentos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('veiculo_id')->constrained('veiculos')->cascadeOnDelete();
            $t->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $t->unique(['veiculo_id','department_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('veiculo_departamentos');
        Schema::dropIfExists('veiculos');
    }
};
