<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAtividade extends Model
{
    protected $table = 'tipos_atividade';
    protected $fillable = ['slug','nome'];

    public function situacoes() {
        return $this->hasMany(SituacaoAtividade::class, 'tipo_atividade_id');
    }
}
