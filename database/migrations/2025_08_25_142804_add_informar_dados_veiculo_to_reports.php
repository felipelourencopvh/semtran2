<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('reports','informar_dados_veiculo')) {
            Schema::table('reports', function (Blueprint $t) {
                $t->boolean('informar_dados_veiculo')->default(false)->after('shift');
            });
        }
    }
    public function down(): void {
        if (Schema::hasColumn('reports','informar_dados_veiculo')) {
            Schema::table('reports', function (Blueprint $t) {
                $t->dropColumn('informar_dados_veiculo');
            });
        }
    }
};
