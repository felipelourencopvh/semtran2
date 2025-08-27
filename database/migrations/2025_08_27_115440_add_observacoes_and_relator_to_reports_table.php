<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('reports', function (Blueprint $t) {
            if (!Schema::hasColumn('reports', 'observacoes')) {
                $t->longText('observacoes')->nullable();
            }
            if (!Schema::hasColumn('reports', 'relator_id')) {
                $t->foreignId('relator_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }
    public function down(): void {
        Schema::table('reports', function (Blueprint $t) {
            if (Schema::hasColumn('reports', 'relator_id')) {
                $t->dropConstrainedForeignId('relator_id');
            }
            if (Schema::hasColumn('reports', 'observacoes')) {
                $t->dropColumn('observacoes');
            }
        });
    }
};
