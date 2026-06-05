<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaEstudiante extends Model
{
    use HasFactory;

    protected $table = 'asistencias_estudiantes';

    protected $fillable = [
        'historial_estudiante_id',
        'fecha',
        'id_corte',
        'id_tipo_asistencia',
    ];

    /**
     * Relación con el historial del estudiante.
     * Una asistencia pertenece a un registro histórico concreto.
     */
    public function historial()
    {
        return $this->belongsTo(HistorialEstudiante::class, 'historial_estudiante_id');
    }

    /**
     * Relación con el corte evaluativo.
     */
    public function corte()
    {
        return $this->belongsTo(Corte::class, 'id_corte');
    }

    /**
     * Relación con el tipo de asistencia (Presente, Ausente, Justificado, Tarde).
     */
    public function tipoAsistencia()
    {
        return $this->belongsTo(TipoAsistencia::class, 'id_tipo_asistencia');
    }

    // Accesores útiles para obtener información contextual histórica
    public function getEstudianteNombreAttribute()
    {
        return $this->historial?->estudiante?->name;
    }

    public function getSeccionNombreAttribute()
    {
        return $this->historial?->seccion?->nombre;
    }

    public function getNumeroListaAttribute()
    {
        return $this->historial?->numero_lista;
    }
}