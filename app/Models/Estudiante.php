<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    protected $fillable = [
        'name', 'numero_lista', 'genero', 'año',
        'id_seccion', 'estado'
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'id_seccion');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_estudiante');
    }
}
