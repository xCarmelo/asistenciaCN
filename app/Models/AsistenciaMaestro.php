<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaMaestro extends Model
{
    use HasFactory;

    protected $table = 'asistencias_maestros';
protected $fillable = [
    'fecha',
    'id_maestro',
    'asis',
    'justificado',
    'injustificado',
    'id_corte',
    'tutelado',
    'id_tipo_asistencia', // ← Agregar
];

    public function maestro()
    {
        return $this->belongsTo(Maestro::class, 'id_maestro');
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


