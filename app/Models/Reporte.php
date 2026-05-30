<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';
    protected $fillable = [
        'id_seccion',
        'tipo',
        'id_maestro',
        'cef',
        'cem',
        'crf',
        'crm',
        'fecha'
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'id_seccion');
    }

    public function maestro()
    {
        return $this->belongsTo(Maestro::class, 'id_maestro');
    }
}
