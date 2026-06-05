<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
    use HasFactory;

    protected $table = 'maestros';

    protected $fillable = ['name', 'estado_general', 'genero'];

    public function historiales()
    {
        return $this->hasMany(HistorialMaestro::class, 'maestro_id');
    }

    public function historialActivo()
    {
        return $this->hasOne(HistorialMaestro::class, 'maestro_id')
            ->whereNull('fecha_fin')
            ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true));
    }
}