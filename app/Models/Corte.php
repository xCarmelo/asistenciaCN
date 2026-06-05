<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Corte extends Model
{
    use HasFactory;

    protected $table = 'cortes';

    protected $fillable = ['nombre'];

    public function asistenciasEstudiantes()
    {
        return $this->hasMany(AsistenciaEstudiante::class, 'id_corte');
    }

    public function asistenciasMaestros()
    {
        return $this->hasMany(AsistenciaMaestroHistorica::class, 'id_corte');
    }
}