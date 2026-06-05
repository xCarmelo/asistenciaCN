<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';

    protected $fillable = ['name', 'genero'];

    public function historiales()
    {
        return $this->hasMany(HistorialEstudiante::class, 'estudiante_id');
    }

    public function historialActivo()
    {
        return $this->hasOne(HistorialEstudiante::class, 'estudiante_id')
            ->whereNull('fecha_fin')
            ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true));
    }
}