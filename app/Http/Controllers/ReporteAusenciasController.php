<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use App\Models\Corte;
use App\Models\AsistenciaEstudiante;
use App\Models\HistorialEstudiante;
use App\Models\TipoAsistencia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AusenciasExport;

class ReporteAusenciasController extends Controller
{
    public function index(Request $request)
    {
        $secciones = Seccion::orderBy('nombre')->get();  // ya no hay columna 'estado' en secciones
        $cortes = Corte::orderBy('id')->get();

        // Obtener años dinámicos desde asistencias de estudiantes (nueva tabla)
        $anios = AsistenciaEstudiante::selectRaw('DISTINCT YEAR(fecha) as año')
            ->orderBy('año', 'desc')
            ->pluck('año')
            ->toArray();

        if (empty($anios)) {
            $anios = [date('Y')];
        }

        $filtros = [
            'seccion_id' => $request->get('seccion_id'),
            'corte_id'   => $request->get('corte_id'),
            'anio'       => $request->get('anio'),
            'desde'      => $request->get('desde'),
            'hasta'      => $request->get('hasta'),
        ];

        if ($filtros['corte_id'] && empty($filtros['anio']) && (empty($filtros['desde']) || empty($filtros['hasta']))) {
            return redirect()->back()->with('error', 'Al seleccionar un corte, debe seleccionar también un año (a menos que use rango de fechas).');
        }

        $resultados = null;
        if ($filtros['seccion_id'] && ($filtros['corte_id'] || ($filtros['desde'] && $filtros['hasta']))) {
            $resultados = $this->consultarAsistencias($filtros);
        }

        return view('reporte-ausencias', compact('secciones', 'cortes', 'anios', 'filtros', 'resultados'));
    }

    private function consultarAsistencias($filtros)
    {
        $seccionId = $filtros['seccion_id'];

        // Obtener todos los HISTORIALES de estudiantes que pertenecen a la sección
        $historialesQuery = HistorialEstudiante::where('seccion_id', $seccionId);
        // (No filtramos por estado aquí porque queremos incluir inactivos con asistencias históricas)

        // Obtener las asistencias a través de los historiales
        $query = AsistenciaEstudiante::with(['historial.estudiante', 'tipoAsistencia', 'corte'])
            ->whereHas('historial', function($q) use ($seccionId) {
                $q->where('seccion_id', $seccionId);
            });

        if (!empty($filtros['corte_id'])) {
            $query->where('id_corte', $filtros['corte_id']);
        }

        if (!empty($filtros['anio']) && empty($filtros['desde']) && empty($filtros['hasta'])) {
            $query->whereYear('fecha', $filtros['anio']);
        }

        if (!empty($filtros['desde']) && !empty($filtros['hasta'])) {
            $query->whereBetween('fecha', [$filtros['desde'], $filtros['hasta']]);
        }

        $asistencias = $query->get();

        if ($asistencias->isEmpty()) {
            return [
                'estudiantes'    => [],
                'llegadasTarde'  => [],
                'meses'          => [],
                'seccion'        => Seccion::find($seccionId),
                'corte'          => $filtros['corte_id'] ? Corte::find($filtros['corte_id']) : null,
            ];
        }

        // Obtener meses únicos ordenados cronológicamente (en español)
        $meses = $asistencias->map(fn($a) => Carbon::parse($a->fecha)->locale('es')->translatedFormat('F'))
            ->unique()
            ->values()
            ->sortBy(function ($mes) use ($asistencias) {
                $fechaPrimera = $asistencias->first(fn($a) => Carbon::parse($a->fecha)->locale('es')->translatedFormat('F') === $mes);
                return $fechaPrimera ? Carbon::parse($fechaPrimera->fecha) : now();
            })
            ->values()
            ->toArray();

        $estudiantesData = [];
        $llegadasTardeData = [];

        foreach ($asistencias as $asis) {
            $est = $asis->historial->estudiante;
            if (!$est) continue;
            $nombre = $est->name;
            $tipo = $asis->tipoAsistencia;
            if (!$tipo) continue;

            $mes = Carbon::parse($asis->fecha)->locale('es')->translatedFormat('F');
            $codigo = $tipo->codigo;

            if ($codigo === 'A' || $codigo === 'J') {
                if (!isset($estudiantesData[$nombre])) {
                    $estudiantesData[$nombre] = [
                        'nombre'                   => $nombre,
                        'ausencias_justificadas'   => 0,
                        'ausencias_injustificadas' => 0,
                        'detalle_mensual'          => [],
                    ];
                }
                if ($codigo === 'A') {
                    $estudiantesData[$nombre]['ausencias_injustificadas']++;
                } else {
                    $estudiantesData[$nombre]['ausencias_justificadas']++;
                }
                $estudiantesData[$nombre]['detalle_mensual'][$mes] = ($estudiantesData[$nombre]['detalle_mensual'][$mes] ?? 0) + 1;
            } elseif ($codigo === 'T') {
                if (!isset($llegadasTardeData[$nombre])) {
                    $llegadasTardeData[$nombre] = [
                        'nombre'  => $nombre,
                        'detalle' => [],
                        'total'   => 0,
                    ];
                }
                $llegadasTardeData[$nombre]['detalle'][$mes] = ($llegadasTardeData[$nombre]['detalle'][$mes] ?? 0) + 1;
                $llegadasTardeData[$nombre]['total']++;
            }
        }

        // Filtrar estudiantes con al menos una ausencia
        $estudiantes = [];
        foreach ($estudiantesData as $data) {
            $total = $data['ausencias_justificadas'] + $data['ausencias_injustificadas'];
            if ($total > 0) {
                $data['total_ausencias'] = $total;
                $estudiantes[] = $data;
            }
        }

        usort($estudiantes, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
        $llegadasTarde = array_values($llegadasTardeData);
        usort($llegadasTarde, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));

        return [
            'estudiantes'    => $estudiantes,
            'llegadasTarde'  => $llegadasTarde,
            'meses'          => $meses,
            'seccion'        => Seccion::find($seccionId),
            'corte'          => $filtros['corte_id'] ? Corte::find($filtros['corte_id']) : null,
        ];
    }

    // Los métodos generarPDF, exportarExcel, exportarWord, vistaPrevia se mantienen igual
    // porque solo usan $resultados y la vista PDF, que ya está adaptada.
}