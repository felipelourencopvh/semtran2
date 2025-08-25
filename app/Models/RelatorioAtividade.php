<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatorioAtividade extends Model
{
    protected $table = 'relatorio_atividades';

    protected $fillable = [
        'report_id',
        'tipo_atividade_id',
        'situacao_atividade_id',
        'medida_atividade_id',
        'endereco',
        'ordem',
    ];

    public function report()   { return $this->belongsTo(\App\Models\Report::class); }
    public function tipo()     { return $this->belongsTo(TipoAtividade::class, 'tipo_atividade_id'); }
    public function situacao() { return $this->belongsTo(SituacaoAtividade::class, 'situacao_atividade_id'); }
    public function medida()   { return $this->belongsTo(MedidaAtividade::class, 'medida_atividade_id'); }
}
