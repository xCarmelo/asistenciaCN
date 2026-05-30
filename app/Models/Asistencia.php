<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';
protected $fillable = [
    'fecha',
    'id_seccion',
    'asis',
    'justificado',
    'injustificado',
    'id_estudiante',
    'id_corte',
    'id_tipo_asistencia',
];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'id_seccion');
    }

    public function corte()
    {
        return $this->belongsTo(Corte::class, 'id_corte');
    }

        public function tipoAsistencia()
    {
        return $this->belongsTo(TipoAsistencia::class, 'id_tipo_asistencia');
    }
}
