<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAsistencia extends Model
{
    use HasFactory;

    protected $table = 'tipos_asistencia';
    protected $fillable = ['codigo', 'nombre', 'es_presente'];
}
