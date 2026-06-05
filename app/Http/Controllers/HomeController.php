<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Maestro;
use App\Models\Seccion;
use App\Models\HistorialEstudiante;
use App\Models\HistorialMaestro;
use App\Models\AsistenciaEstudiante;
use App\Models\AsistenciaMaestroHistorica;
use App\Models\TipoAsistencia;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
{
    $hoy = Carbon::now()->toDateString();

    // Total estudiantes activos
    $totalEstudiantes = HistorialEstudiante::whereNull('fecha_fin')
        ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
        ->distinct('estudiante_id')
        ->count('estudiante_id');

    // Total maestros activos
    $totalMaestros = HistorialMaestro::whereNull('fecha_fin')
        ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
        ->distinct('maestro_id')
        ->count('maestro_id');

    $totalSecciones = Seccion::count();

    // Secciones con asistencia de estudiantes hoy
    $seccionesConAsistenciaHoy = AsistenciaEstudiante::where('fecha', $hoy)
    ->whereHas('historial', fn($q) => $q->whereNull('fecha_fin'))
    ->join('historial_estudiantes', 'asistencias_estudiantes.historial_estudiante_id', '=', 'historial_estudiantes.id')
    ->distinct('historial_estudiantes.seccion_id')
    ->count('historial_estudiantes.seccion_id');

    // Maestros ausentes hoy (devolviendo las asistencias con el maestro y sus secciones)
    $tipoAusente = TipoAsistencia::where('codigo', 'A')->first();
    $maestrosAusentes = collect();

    if ($tipoAusente) {
        $maestrosAusentes = AsistenciaMaestroHistorica::where('fecha', $hoy)
            ->where('id_tipo_asistencia', $tipoAusente->id)
            ->with(['historial.maestro.seccionesGuiadas', 'historial.seccion'])
            ->get();
    }

    return view('home', compact(
        'totalEstudiantes',
        'totalMaestros',
        'totalSecciones',
        'seccionesConAsistenciaHoy',
        'maestrosAusentes'
    ));
}
}