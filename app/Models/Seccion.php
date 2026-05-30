<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';
    protected $fillable = ['nombre', 'id_maestro_guia', 'estado'];

    public function maestroGuia()
    {
        return $this->belongsTo(Maestro::class, 'id_maestro_guia');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id_seccion');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_seccion');
    }

    public function reportes()
    {
        return $this->hasMany(Reporte::class, 'id_seccion');
    }
}
