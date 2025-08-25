<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RelatorioCondutor extends Model
{
    protected $table = 'relatorio_condutores';
    protected $fillable = [
        'report_id','veiculo_id','motorista_id','matricula',
        'odometro_inicial','odometro_final','ordem'
    ];

    public function report()   { return $this->belongsTo(Report::class); }
    public function veiculo()  { return $this->belongsTo(Veiculo::class); }
    public function motorista(){ return $this->belongsTo(User::class, 'motorista_id'); }
}
