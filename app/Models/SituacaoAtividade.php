<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SituacaoAtividade extends Model
{
    protected $table = 'situacoes_atividade';
    protected $fillable = ['tipo_atividade_id','slug','nome'];

    public function tipo() {
        return $this->belongsTo(TipoAtividade::class, 'tipo_atividade_id');
    }
    public function medidas() {
        return $this->hasMany(MedidaAtividade::class, 'situacao_atividade_id');
    }
}
