<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;

class Veiculo extends Model
{
    protected $table = 'veiculos';
    protected $fillable = [
        'placa','marca','especie','modelo','odometro_atual','department_owner_id'
    ];

    public function proprietario() {
        return $this->belongsTo(Department::class, 'department_owner_id');
    }

    public function departamentosAutorizados() {
        return $this->belongsToMany(Department::class, 'veiculo_departamentos', 'veiculo_id', 'department_id');
    }

    // escopo: visíveis para um departamento
    public function scopeVisiveisParaDepartamento($q, int $departmentId) {
        return $q->where('department_owner_id', $departmentId)
            ->orWhereHas('departamentosAutorizados', fn($s) => $s->where('departments.id',$departmentId));
    }

    public function getDescricaoAttribute() {
        return "{$this->placa} ({$this->modelo})";
    }

}
