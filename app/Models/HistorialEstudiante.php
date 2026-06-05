<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstudiante extends Model
{
    use HasFactory;

    protected $table = 'historial_estudiantes';

    protected $fillable = [
        'estudiante_id',
        'seccion_id',
        'estado_id',
        'numero_lista',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaEstudiante::class, 'historial_estudiante_id');
    }
}