<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Report extends Model
{
    protected $fillable = [
        'user_id', 'same_day', 'start_at', 'end_at', 'service_type', 'shift', 'meta','descricao_manual',
        'informar_dados_veiculo',
        'observacoes',
        'relator_id',
    ];

    protected $casts = [
        'same_day'  => 'boolean',
        'start_at'  => 'datetime',
        'end_at'    => 'datetime',
        'meta'      => 'array',

    ];

    public function team(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'report_user');
    }

    public function author()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function relator() {
        return $this->belongsTo(User::class, 'relator_id');
    }

    public function atividades()
    {
        return $this->hasMany(\App\Models\RelatorioAtividade::class, 'report_id')->orderBy('ordem');
    }
    public function equipamentos()
    {
        return $this->hasMany(\App\Models\RelatorioEquipamento::class, 'report_id')->orderBy('ordem');
    }
    public function condutores()
    {
        return $this->hasMany(\App\Models\RelatorioCondutor::class, 'report_id')->orderBy('ordem');
    }

    public function anexos() {
        return $this->hasMany(ReportAttachment::class, 'report_id')->latest();
    }



}
