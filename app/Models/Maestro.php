<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
    use HasFactory;

    protected $table = 'maestros';
    protected $fillable = ['name', 'estado', 'genero'];

    // Relación: una sección tutelada (un maestro puede guiar varias secciones, pero la UI permitirá solo una)
    public function seccionesGuiadas()
    {
        return $this->hasMany(Seccion::class, 'id_maestro_guia');
    }

    // Método auxiliar para obtener la sección principal (la primera asignada)
    public function getTuteladoAttribute()
    {
        return $this->seccionesGuiadas->first();
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaMaestro::class, 'id_maestro');
    }

      public function seccionGuiada()
    {
        return $this->hasOne(Seccion::class, 'id_maestro_guia');
    }
}
