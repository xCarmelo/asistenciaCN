<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaMaestroHistorica extends Model
{
    use HasFactory;

    protected $table = 'asistencias_maestros_historicas';
    protected $fillable = ['historial_maestro_id', 'fecha', 'id_corte', 'id_tipo_asistencia'];

    public function historial()
    {
        return $this->belongsTo(HistorialMaestro::class, 'historial_maestro_id');
    }

    public function corte()
    {
        return $this->belongsTo(Corte::class, 'id_corte');
    }

    public function tipoAsistencia()
    {
        return $this->belongsTo(TipoAsistencia::class, 'id_tipo_asistencia');
    }
}
