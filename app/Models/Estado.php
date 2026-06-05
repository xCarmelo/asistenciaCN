<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'estados';

    protected $fillable = ['nombre', 'permite_asistencia', 'visible_en_listados'];

    protected $casts = [
        'permite_asistencia' => 'boolean',
        'visible_en_listados' => 'boolean',
    ];

    public function historialesEstudiantes()
    {
        return $this->hasMany(HistorialEstudiante::class, 'estado_id');
    }

    public function historialesMaestros()
    {
        return $this->hasMany(HistorialMaestro::class, 'estado_id');
    }
}