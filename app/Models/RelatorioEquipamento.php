<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RelatorioEquipamento extends Model
{
    protected $table = 'relatorio_equipamentos';
    protected $fillable = ['report_id','tipo','outro_texto','quantidade','ordem'];

    public function report(){ return $this->belongsTo(Report::class); }
}
