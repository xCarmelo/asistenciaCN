<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';
    protected $fillable = ['nombre'];

    /**
     * Relación con el historial de maestros (uno a muchos).
     * Una sección puede tener múltiples registros históricos de maestros.
     */
    public function historialMaestros()
    {
        return $this->hasMany(HistorialMaestro::class, 'seccion_id');
    }

    /**
     * Relación con el maestro actual (historial activo).
     * Retorna el registro de historial_maestros donde fecha_fin es NULL
     * y el estado asociado permite asistencia.
     */
    public function maestroActual()
    {
        return $this->hasOne(HistorialMaestro::class, 'seccion_id')
            ->whereNull('fecha_fin')
            ->whereHas('estado', function ($q) {
                $q->where('permite_asistencia', true);
            })
            ->with('maestro');
    }

    /**
     * Relación con el historial de estudiantes (uno a muchos).
     * Una sección puede tener múltiples registros históricos de estudiantes.
     */
    public function historialEstudiantes()
    {
        return $this->hasMany(HistorialEstudiante::class, 'seccion_id');
    }

    /**
     * Relación con los estudiantes activos actualmente en la sección.
     * Útil para listados de alumnos presentes.
     */
    public function estudiantesActivos()
    {
        return $this->hasMany(HistorialEstudiante::class, 'seccion_id')
            ->whereNull('fecha_fin')
            ->whereHas('estado', function ($q) {
                $q->where('permite_asistencia', true);
            })
            ->with('estudiante');
    }

    // NOTA: Las relaciones directas con Asistencia, Reporte, etc., deben apuntar a través de historiales.
    // Por eso se eliminan los métodos antiguos como estudiantes(), asistencias(), reportes().
}