<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaEstudiante extends Model
{
    use HasFactory;

    protected $table = 'asistencias_estudiantes';
    protected $fillable = ['historial_estudiante_id', 'fecha', 'id_corte', 'id_tipo_asistencia'];

    public function historial()
    {
        return $this->belongsTo(HistorialEstudiante::class, 'historial_estudiante_id');
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
