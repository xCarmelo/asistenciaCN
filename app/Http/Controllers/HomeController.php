<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Maestro;
use App\Models\Seccion;
use App\Models\Asistencia;
use App\Models\AsistenciaMaestro;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Totales principales
        $totalEstudiantes = Estudiante::count();
        $totalMaestros = Maestro::count();
        $totalSecciones = Seccion::count();

        // Fecha actual (ya con zona horaria America/Managua)
        $hoy = Carbon::now()->toDateString();

        // Número de SECCIONES distintas que tienen al menos una asistencia registrada hoy
        $seccionesConAsistenciaHoy = Asistencia::where('fecha', $hoy)
            ->distinct('id_seccion')
            ->count('id_seccion');

        // Maestros ausentes hoy
        $maestrosAusentes = AsistenciaMaestro::where('fecha', $hoy)
            ->where('asis', 'A')
            ->with(['maestro.seccionesGuiadas'])
            ->get();

        return view('home', compact(
            'totalEstudiantes',
            'totalMaestros',
            'totalSecciones',
            'seccionesConAsistenciaHoy',
            'maestrosAusentes'
        ));
    }
}
