<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialMaestro extends Model
{
    use HasFactory;

    protected $table = 'historial_maestros';

    protected $fillable = [
        'maestro_id',
        'seccion_id',
        'estado_id',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function maestro()
    {
        return $this->belongsTo(Maestro::class, 'maestro_id');
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
        return $this->hasMany(AsistenciaMaestroHistorica::class, 'historial_maestro_id');
    }
}