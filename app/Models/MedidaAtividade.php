<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedidaAtividade extends Model
{
    protected $table = 'medidas_atividade';
    protected $fillable = ['situacao_atividade_id','slug','nome'];

    public function situacao() {
        return $this->belongsTo(SituacaoAtividade::class, 'situacao_atividade_id');
    }
}
