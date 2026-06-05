<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use App\Models\Corte;
use App\Models\Estudiante;
use App\Models\Maestro;
use App\Models\HistorialEstudiante;
use App\Models\HistorialMaestro;
use App\Models\AsistenciaEstudiante;
use App\Models\AsistenciaMaestroHistorica;
use App\Models\TipoAsistencia;
use App\Models\Reporte;
use App\Models\Estado;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    // ================== VISTA PRINCIPAL (listado de asistencias por fecha) ==================
    public function index(Request $request)
    {
        $secciones = Seccion::orderBy('nombre')->get();
        $cortes = Corte::all();

        $filtros = [
            'seccion_id' => $request->get('seccion_id'),
            'corte_id'   => $request->get('corte_id'),
            'desde'      => $request->get('desde', Carbon::now()->startOfMonth()->toDateString()),
            'hasta'      => $request->get('hasta', Carbon::now()->toDateString()),
        ];

        $reporteQuery = Reporte::where('tipo', 'estudiante')
            ->whereBetween('fecha', [$filtros['desde'], $filtros['hasta']]);
        if ($filtros['seccion_id']) {
            $reporteQuery->where('id_seccion', $filtros['seccion_id']);
        }
        $reportesPorFecha = $reporteQuery
            ->selectRaw('fecha, SUM(crf) as femeninas_presentes, SUM(crm) as varones_presentes')
            ->groupBy('fecha')
            ->orderBy('fecha', 'desc')
            ->get();

        $registros = $reportesPorFecha->map(fn($item) => (object)[
            'fecha' => $item->fecha,
            'F' => $item->femeninas_presentes,
            'V' => $item->varones_presentes,
            'Total' => $item->femeninas_presentes + $item->varones_presentes,
        ]);

        return view('asistencia.index', compact('secciones', 'cortes', 'filtros', 'registros'));
    }

    // ================== ASISTENCIA ESTUDIANTES ==================
    public function createEstudiantes(Request $request)
    {
        $secciones = Seccion::orderBy('nombre')->get();
        $cortes = Corte::all();
        $seccionId = $request->get('seccion_id');
        $fecha = $request->get('fecha', date('Y-m-d'));
        $corteId = $request->get('corte_id', 1);

        $estudiantes = collect();
        if ($seccionId) {
            // Obtener HISTORIALES activos en la fecha actual (para crear nuevas asistencias)
            $historiales = HistorialEstudiante::where('seccion_id', $seccionId)
                ->where('fecha_inicio', '<=', $fecha)
                ->where(function($q) use ($fecha) {
                    $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
                })
                ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
                ->with('estudiante')
                ->orderBy('numero_lista')
                ->get();

            // Transformar a objeto con los datos necesarios para la vista
            $estudiantes = $historiales->map(function($h) {
                $est = $h->estudiante;
                $est->numero_lista = $h->numero_lista;
                $est->historial_id = $h->id;
                return $est;
            });
        }

        return view('asistencia.estudiantes', compact('secciones', 'cortes', 'estudiantes', 'seccionId', 'fecha', 'corteId'));
    }

    public function storeEstudiantes(Request $request)
    {
        $request->validate([
            'fecha'      => 'required|date',
            'id_corte'   => 'required|exists:cortes,id',
            'id_seccion' => 'required|exists:secciones,id',
            'asistencia' => 'array',
        ]);

        $fecha = $request->fecha;
        $corteId = $request->id_corte;
        $seccionId = $request->id_seccion;

        // Verificar si ya existe alguna asistencia para esa fecha, corte y sección
        $existe = AsistenciaEstudiante::where('fecha', $fecha)
            ->where('id_corte', $corteId)
            ->whereHas('historial', fn($q) => $q->where('seccion_id', $seccionId))
            ->exists();

        if ($existe) {
            return redirect()->back()->withInput()->with('error_modal', 'Ya existe una asistencia para esta fecha, corte y sección.');
        }

        // Obtener los HISTORIALES activos en esa fecha (solo los que permiten asistencia)
        $historiales = HistorialEstudiante::where('seccion_id', $seccionId)
            ->where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
            ->get();

        foreach ($historiales as $h) {
            $asis = $request->asistencia[$h->estudiante_id] ?? 'P';
            $tipo = TipoAsistencia::firstOrCreate(
                ['codigo' => $asis],
                ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
            );

            AsistenciaEstudiante::create([
                'historial_estudiante_id' => $h->id,
                'fecha'        => $fecha,
                'id_corte'     => $corteId,
                'id_tipo_asistencia' => $tipo->id,
            ]);
        }

        $this->actualizarReporte($seccionId, $fecha);
        $this->actualizarReporteMaestros($fecha);

        return redirect()->route('asistencia.estudiantes.create', [
            'seccion_id' => $seccionId,
            'corte_id'   => $corteId,
            'fecha'      => $fecha,
        ])->with('success_modal', 'Asistencia de estudiantes guardada correctamente.');
    }

    // ================== ASISTENCIA MAESTROS ==================
public function createMaestros(Request $request)
{
    $cortes = Corte::all();
    $fecha = $request->get('fecha', date('Y-m-d'));
    $corteId = $request->get('corte_id', 1);

    // Obtener historiales de maestros activos en esa fecha
    $historiales = HistorialMaestro::where('fecha_inicio', '<=', $fecha)
        ->where(function($q) use ($fecha) {
            $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
        })
        ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
        ->with('maestro', 'seccion')
        ->get();

    // Transformar a una colección de objetos con los datos necesarios para la vista
    $maestros = $historiales->map(function($h) {
        $h->maestro->tutelado_nombre = $h->seccion->nombre ?? 'Sin tutelado';
        return $h->maestro;
    });

    return view('asistencia.maestros', compact('cortes', 'maestros', 'fecha', 'corteId'));
}

    public function storeMaestros(Request $request)
    {
        $request->validate([
            'fecha'      => 'required|date',
            'id_corte'   => 'required|exists:cortes,id',
            'asistencia' => 'array',
        ]);

        $fecha = $request->fecha;
        $corteId = $request->id_corte;

        $existe = AsistenciaMaestroHistorica::where('fecha', $fecha)
            ->where('id_corte', $corteId)
            ->exists();

        if ($existe) {
            return redirect()->back()->withInput()->with('error_modal', 'Ya existe una asistencia de maestros para esta fecha y corte.');
        }

        // Obtener HISTORIALES de maestros activos en esa fecha
        $historiales = HistorialMaestro::where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
            ->with('maestro')
            ->get();

        foreach ($historiales as $h) {
            $asis = $request->asistencia[$h->maestro_id] ?? 'P';
            $tipo = TipoAsistencia::firstOrCreate(
                ['codigo' => $asis],
                ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
            );

            AsistenciaMaestroHistorica::create([
                'historial_maestro_id' => $h->id,
                'fecha'      => $fecha,
                'id_corte'   => $corteId,
                'id_tipo_asistencia' => $tipo->id,
            ]);
        }

        $this->actualizarReporteMaestros($fecha);
        return redirect()->route('asistencia.maestros.create', [
            'corte_id' => $corteId,
            'fecha'    => $fecha,
        ])->with('success_modal', 'Asistencia de maestros guardada correctamente.');
    }

    // ================== ELIMINACIÓN POR FECHA ==================
    public function destroyByDate($fecha, Request $request)
    {
        $corteId = $request->get('corte_id');
        $seccionId = $request->get('seccion_id');

        $query = AsistenciaEstudiante::where('fecha', $fecha);
        if ($corteId) $query->where('id_corte', $corteId);
        if ($seccionId) {
            $query->whereHas('historial', fn($q) => $q->where('seccion_id', $seccionId));
        }
        $deleted = $query->delete();

        // Eliminar reportes asociados
        $reporteQuery = Reporte::where('fecha', $fecha);
        if ($seccionId) $reporteQuery->where('id_seccion', $seccionId);
        $reporteQuery->delete();

        if ($deleted) {
            $this->actualizarReporteMaestros($fecha);
            return redirect()->route('asistencia.index', ['corte_id' => $corteId, 'seccion_id' => $seccionId])
                ->with('success_modal', "Se eliminaron $deleted registros de asistencia para la fecha $fecha.");
        } else {
            $this->actualizarReporteMaestros($fecha);
            return redirect()->route('asistencia.index', ['corte_id' => $corteId, 'seccion_id' => $seccionId])
                ->with('error_modal', "No se encontraron registros para eliminar en la fecha $fecha.");
        }
    }

    // ================== REPORTE DIARIO DETALLADO ==================
    public function reporte($fecha, Request $request)
    {
        $corteId = $request->get('corte_id', 1);
        $secciones = Seccion::orderBy('nombre')->get();
        $secciones = $secciones->sortBy(function($seccion) {
            preg_match('/(\d+)/', $seccion->nombre, $matches);
            $grado = isset($matches[1]) ? (int)$matches[1] : 999;
            return $grado . '-' . $seccion->nombre;
        })->values();

        $data = [];
        $dataPorGrado = [];
        $totalRealGeneral = ['F' => 0, 'V' => 0];

        foreach ($secciones as $seccion) {
            $reporte = Reporte::where('id_seccion', $seccion->id)->where('fecha', $fecha)->first();
            if (!$reporte) {
                $this->actualizarReporte($seccion->id, $fecha);
                $reporte = Reporte::where('id_seccion', $seccion->id)->where('fecha', $fecha)->first();
            }

            // Obtener asistencias de estudiantes de esa sección y fecha
            $asistencias = AsistenciaEstudiante::where('fecha', $fecha)
                ->whereHas('historial', fn($q) => $q->where('seccion_id', $seccion->id))
                ->with(['historial.estudiante', 'tipoAsistencia'])
                ->get();

            $estudiantes = $asistencias->map(function($a) {
                $est = $a->historial->estudiante;
                $est->asistencia = $a->tipoAsistencia->codigo;
                $est->numero_lista = $a->historial->numero_lista;
                return $est;
            });

            $ausentes = $estudiantes->filter(fn($e) => $e->asistencia != 'P');

            $data[] = [
                'seccion' => $seccion,
                'reporte' => $reporte,
                'estudiantes' => $estudiantes,
                'ausentes' => $ausentes,
            ];

            // Acumular por grado
            preg_match('/(\d+)/', $seccion->nombre, $matches);
            $grado = isset($matches[1]) ? (int)$matches[1] : 0;
            if (!isset($dataPorGrado[$grado])) {
                $dataPorGrado[$grado] = ['cef' => 0, 'cem' => 0, 'crf' => 0, 'crm' => 0];
            }
            $dataPorGrado[$grado]['cef'] += $reporte->cef;
            $dataPorGrado[$grado]['cem'] += $reporte->cem;
            $dataPorGrado[$grado]['crf'] += $reporte->crf;
            $dataPorGrado[$grado]['crm'] += $reporte->crm;
            $totalRealGeneral['F'] += $reporte->crf;
            $totalRealGeneral['V'] += $reporte->crm;
        }

        $totalRealGeneral['T'] = $totalRealGeneral['F'] + $totalRealGeneral['V'];

        // Maestros para la sección de docentes
        $maestros = Maestro::orderBy('name')->get();
        foreach ($maestros as $m) {
            $asis = AsistenciaMaestroHistorica::where('fecha', $fecha)
                ->whereHas('historial', fn($q) => $q->where('maestro_id', $m->id))
                ->first();
            $m->asistencia = $asis ? $asis->tipoAsistencia->codigo : 'P';
        }
        $this->actualizarReporteMaestros($fecha);

        $docentesEsperados = [
            'F' => $maestros->where('genero', 'F')->count(),
            'V' => $maestros->where('genero', 'M')->count(),
        ];
        $docentesReales = [
            'F' => $maestros->filter(fn($m) => $m->asistencia == 'P' && $m->genero == 'F')->count(),
            'V' => $maestros->filter(fn($m) => $m->asistencia == 'P' && $m->genero == 'M')->count(),
        ];
        $docentesReales['T'] = $docentesReales['F'] + $docentesReales['V'];
        $docentesEsperados['T'] = $docentesEsperados['F'] + $docentesEsperados['V'];

        return view('asistencia.reporte', compact('fecha', 'data', 'dataPorGrado', 'totalRealGeneral', 'maestros', 'docentesEsperados', 'docentesReales'));
    }

    // ================== REPORTE DATA (AJAX) ==================
    public function getReporteData($fecha, Request $request)
    {
        $corteId = $request->get('corte_id', 1);

        $seccionesConAsistencia = AsistenciaEstudiante::where('fecha', $fecha)
            ->where('id_corte', $corteId)
            ->with('historial')
            ->get()
            ->pluck('historial.seccion_id')
            ->unique()
            ->toArray();

        $secciones = Seccion::whereIn('id', $seccionesConAsistencia)->orderBy('nombre')->get();
        $secciones = $secciones->sortBy(function($seccion) {
            preg_match('/(\d+)/', $seccion->nombre, $matches);
            return isset($matches[1]) ? (int)$matches[1] : 999;
        });

        $data = [];
        $totalRealGeneral = ['F' => 0, 'V' => 0];

        foreach ($secciones as $seccion) {
            $reporte = Reporte::where('id_seccion', $seccion->id)->where('fecha', $fecha)->where('tipo', 'estudiante')->first();
            if (!$reporte) {
                $this->actualizarReporte($seccion->id, $fecha);
                $reporte = Reporte::where('id_seccion', $seccion->id)->where('fecha', $fecha)->where('tipo', 'estudiante')->first();
            }

            $asistencias = AsistenciaEstudiante::where('fecha', $fecha)
                ->whereHas('historial', fn($q) => $q->where('seccion_id', $seccion->id))
                ->with(['historial.estudiante', 'tipoAsistencia'])
                ->get();

            $ausentes = [];
            $justificadosF = 0;
            $justificadosM = 0;
            $injustificadosF = 0;
            $injustificadosM = 0;

            foreach ($asistencias as $asis) {
                $est = $asis->historial->estudiante;
                $codigo = $asis->tipoAsistencia->codigo;
                $estado = $codigo;
                if ($estado != 'P') {
                    $ausentes[] = [
                        'id' => $est->id,
                        'name' => $est->name,
                        'numero_lista' => $asis->historial->numero_lista,
                        'asistencia' => $estado,
                    ];
                }
                if ($estado == 'J') {
                    if ($est->genero == 'F') $justificadosF++;
                    else $justificadosM++;
                } elseif ($estado == 'A' || $estado == 'T') {
                    if ($est->genero == 'F') $injustificadosF++;
                    else $injustificadosM++;
                }
            }

            preg_match('/(\d+)/', $seccion->nombre, $matches);
            $grado = isset($matches[1]) ? (int)$matches[1] : 0;
            $data[] = [
                'seccion_id' => $seccion->id,
                'seccion_nombre' => $seccion->nombre,
                'grado' => $grado,
                'cef' => $reporte->cef,
                'cem' => $reporte->cem,
                'crf' => $reporte->crf,
                'crm' => $reporte->crm,
                'justificadosF' => $justificadosF,
                'justificadosM' => $justificadosM,
                'injustificadosF' => $injustificadosF,
                'injustificadosM' => $injustificadosM,
                'ausentes' => $ausentes,
            ];
            $totalRealGeneral['F'] += $reporte->crf;
            $totalRealGeneral['V'] += $reporte->crm;
        }

        // Maestros
        $reporteDocentes = Reporte::where('tipo', 'maestro')->where('fecha', $fecha)->first();
        if ($reporteDocentes) {
            $docentesEsperadosF = $reporteDocentes->cef;
            $docentesEsperadosV = $reporteDocentes->cem;
            $docentesRealesF = $reporteDocentes->crf;
            $docentesRealesV = $reporteDocentes->crm;
        } else {
            $historialesMaestros = HistorialMaestro::where('fecha_inicio', '<=', $fecha)
                ->where(function($q) use ($fecha) {
                    $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
                })
                ->with('maestro')
                ->get();
            $docentesEsperadosF = $historialesMaestros->filter(fn($h) => $h->maestro->genero == 'F')->count();
            $docentesEsperadosV = $historialesMaestros->filter(fn($h) => $h->maestro->genero == 'M')->count();
            $asistenciasMaestros = AsistenciaMaestroHistorica::where('fecha', $fecha)->with('historial.maestro')->get();
            $docentesRealesF = $asistenciasMaestros->filter(fn($a) => $a->historial->maestro->genero == 'F' && $a->tipoAsistencia->codigo == 'P')->count();
            $docentesRealesV = $asistenciasMaestros->filter(fn($a) => $a->historial->maestro->genero == 'M' && $a->tipoAsistencia->codigo == 'P')->count();
            $this->actualizarReporteMaestros($fecha);
        }

        $docentesAusentes = [];
        $maestros = Maestro::all();
        foreach ($maestros as $m) {
            $asis = AsistenciaMaestroHistorica::where('fecha', $fecha)
                ->whereHas('historial', fn($q) => $q->where('maestro_id', $m->id))
                ->first();
            $estado = $asis ? $asis->tipoAsistencia->codigo : 'P';
            if ($estado != 'P') {
                $docentesAusentes[] = ['id' => $m->id, 'name' => $m->name, 'asistencia' => $estado];
            }
        }

        return response()->json([
            'success' => true,
            'secciones' => $data,
            'totalRealGeneral' => ['F' => $totalRealGeneral['F'], 'V' => $totalRealGeneral['V']],
            'docentes' => [
                'esperadosF' => $docentesEsperadosF,
                'esperadosV' => $docentesEsperadosV,
                'realesF' => $docentesRealesF,
                'realesV' => $docentesRealesV,
                'ausentes' => $docentesAusentes,
            ],
        ]);
    }

    // ================== EDICIÓN POR SECCIÓN (ESTUDIANTES) ==================
    public function editEstudiantesSeccion($seccionId, $fecha)
    {
        $seccion = Seccion::findOrFail($seccionId);

        // Obtener historiales que estaban activos en esa fecha (incluyendo inactivos que tengan asistencias)
        $historiales = HistorialEstudiante::where('seccion_id', $seccionId)
            ->where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->with('estudiante')
            ->orderBy('numero_lista')
            ->get();

        // Asignar asistencia_actual si existe
        foreach ($historiales as $h) {
            $asis = AsistenciaEstudiante::where('historial_estudiante_id', $h->id)
                ->where('fecha', $fecha)
                ->first();
            $h->asistencia_actual = $asis ? $asis->tipoAsistencia->codigo : '';
        }

        // Filtrar solo historiales que tengan asistencia (para no mostrar sin registro)
        $historiales = $historiales->filter(fn($h) => $h->asistencia_actual !== '')->values();

        $cortes = Corte::all();
        $corteId = 1;

        return view('asistencia.editar_estudiantes', compact('seccion', 'historiales', 'fecha', 'cortes', 'corteId'));
    }

    public function updateEstudiantesSeccion(Request $request)
    {
        $request->validate([
            'fecha'      => 'required|date',
            'id_seccion' => 'required|exists:secciones,id',
            'id_corte'   => 'required|exists:cortes,id',
            'asistencia' => 'array',
        ]);

        $fecha = $request->fecha;
        $seccionId = $request->id_seccion;
        $corteId = $request->id_corte;

        $historiales = HistorialEstudiante::where('seccion_id', $seccionId)
            ->where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->get();

        foreach ($historiales as $h) {
            $asis = $request->asistencia[$h->estudiante_id] ?? '';
            if ($asis === '') {
                AsistenciaEstudiante::where('historial_estudiante_id', $h->id)
                    ->where('fecha', $fecha)
                    ->where('id_corte', $corteId)
                    ->delete();
                continue;
            }
            $tipo = TipoAsistencia::firstOrCreate(
                ['codigo' => $asis],
                ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
            );
            AsistenciaEstudiante::updateOrCreate(
                [
                    'historial_estudiante_id' => $h->id,
                    'fecha' => $fecha,
                    'id_corte' => $corteId,
                ],
                ['id_tipo_asistencia' => $tipo->id]
            );
        }

        $this->actualizarReporte($seccionId, $fecha);
        return redirect()->route('asistencia.reporte', $fecha)->with('success_modal', 'Asistencia actualizada correctamente.');
    }

    // ================== EDICIÓN DE MAESTROS (POR FECHA) ==================
    public function editMaestros($fecha)
    {
        $historiales = HistorialMaestro::where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->with('maestro')
            ->get();

        foreach ($historiales as $h) {
            $asis = AsistenciaMaestroHistorica::where('historial_maestro_id', $h->id)
                ->where('fecha', $fecha)
                ->first();
            $h->asistencia_actual = $asis ? $asis->tipoAsistencia->codigo : '';
        }

        $cortes = Corte::all();
        $corteId = 1;
        return view('asistencia.editar_maestros', compact('historiales', 'fecha', 'cortes', 'corteId'));
    }

    public function updateMaestros(Request $request)
    {
        $request->validate([
            'fecha'      => 'required|date',
            'id_corte'   => 'required|exists:cortes,id',
            'asistencia' => 'array',
        ]);

        $fecha = $request->fecha;
        $corteId = $request->id_corte;

        $historiales = HistorialMaestro::where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->get();

        foreach ($historiales as $h) {
            $asis = $request->asistencia[$h->maestro_id] ?? '';
            if ($asis === '') {
                AsistenciaMaestroHistorica::where('historial_maestro_id', $h->id)
                    ->where('fecha', $fecha)
                    ->where('id_corte', $corteId)
                    ->delete();
                continue;
            }
            $tipo = TipoAsistencia::firstOrCreate(
                ['codigo' => $asis],
                ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
            );
            AsistenciaMaestroHistorica::updateOrCreate(
                [
                    'historial_maestro_id' => $h->id,
                    'fecha' => $fecha,
                    'id_corte' => $corteId,
                ],
                ['id_tipo_asistencia' => $tipo->id]
            );
        }

        $this->actualizarReporteMaestros($fecha);
        return redirect()->route('maestros.asistencias.index')->with('success_modal', 'Asistencia de maestros actualizada correctamente.');
    }

    // ================== ACTUALIZACIÓN DE REPORTES ==================
    private function actualizarReporte($seccionId, $fecha)
    {
        // Historiales activos en esa fecha (para esperados)
        $historialesActivos = HistorialEstudiante::where('seccion_id', $seccionId)
            ->where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
            ->with('estudiante')
            ->get();

        $cef = $historialesActivos->filter(fn($h) => $h->estudiante->genero == 'F')->count();
        $cem = $historialesActivos->filter(fn($h) => $h->estudiante->genero == 'M')->count();

        // Asistencias reales (presentes o llegadas tarde)
        $asistencias = AsistenciaEstudiante::where('fecha', $fecha)
            ->whereHas('historial', fn($q) => $q->where('seccion_id', $seccionId))
            ->whereHas('tipoAsistencia', fn($q) => $q->whereIn('codigo', ['P','T']))
            ->with('historial.estudiante')
            ->get();

        $crf = $asistencias->filter(fn($a) => $a->historial->estudiante->genero == 'F')->count();
        $crm = $asistencias->filter(fn($a) => $a->historial->estudiante->genero == 'M')->count();

        Reporte::updateOrCreate(
            ['id_seccion' => $seccionId, 'fecha' => $fecha, 'tipo' => 'estudiante'],
            ['cef' => $cef, 'cem' => $cem, 'crf' => $crf, 'crm' => $crm]
        );
    }

    private function actualizarReporteMaestros($fecha)
    {
        $historialesActivos = HistorialMaestro::where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
            ->with('maestro')
            ->get();

        $cef = $historialesActivos->filter(fn($h) => $h->maestro->genero == 'F')->count();
        $cem = $historialesActivos->filter(fn($h) => $h->maestro->genero == 'M')->count();

        $asistencias = AsistenciaMaestroHistorica::where('fecha', $fecha)
            ->whereHas('tipoAsistencia', fn($q) => $q->whereIn('codigo', ['P','T']))
            ->with('historial.maestro')
            ->get();

        $crf = $asistencias->filter(fn($a) => $a->historial->maestro->genero == 'F')->count();
        $crm = $asistencias->filter(fn($a) => $a->historial->maestro->genero == 'M')->count();

        Reporte::updateOrCreate(
            ['tipo' => 'maestro', 'fecha' => $fecha, 'id_maestro' => null],
            ['id_seccion' => null, 'cef' => $cef, 'cem' => $cem, 'crf' => $crf, 'crm' => $crm]
        );
    }

    // ================== APIs PARA EDICIÓN INDIVIDUAL ==================
    public function apiAusentesSeccion(Request $request)
    {
        $seccionId = $request->get('seccion_id');
        $fecha = $request->get('fecha');
        $asistencias = AsistenciaEstudiante::where('fecha', $fecha)
            ->whereHas('historial', fn($q) => $q->where('seccion_id', $seccionId))
            ->whereHas('tipoAsistencia', fn($q) => $q->whereIn('codigo', ['A','J','T']))
            ->with(['historial.estudiante', 'tipoAsistencia'])
            ->get();

        $data = [];
        foreach ($asistencias as $a) {
            $data[] = [
                'id' => $a->id,
                'estudiante' => ['name' => $a->historial->estudiante->name],
                'asis' => $a->tipoAsistencia->codigo,
                'estado_texto' => $this->getEstadoTexto($a->tipoAsistencia->codigo),
            ];
        }
        return response()->json($data);
    }

    public function apiAusentesDocentes(Request $request)
    {
        $fecha = $request->get('fecha');
        $asistencias = AsistenciaMaestroHistorica::where('fecha', $fecha)
            ->whereHas('tipoAsistencia', fn($q) => $q->whereIn('codigo', ['A','J','T']))
            ->with(['historial.maestro', 'tipoAsistencia'])
            ->get();

        $data = [];
        foreach ($asistencias as $a) {
            $data[] = [
                'id' => $a->id,
                'maestro' => ['name' => $a->historial->maestro->name],
                'asis' => $a->tipoAsistencia->codigo,
                'estado_texto' => $this->getEstadoTexto($a->tipoAsistencia->codigo),
            ];
        }
        return response()->json($data);
    }

    public function apiActualizarEstado(Request $request)
    {
        $asistenciaId = $request->get('asistencia_id');
        $nuevoEstado = $request->get('asis');
        $asistencia = AsistenciaEstudiante::find($asistenciaId);
        if (!$asistencia) {
            return response()->json(['success' => false, 'message' => 'No encontrado']);
        }
        $tipo = TipoAsistencia::firstOrCreate(
            ['codigo' => $nuevoEstado],
            ['nombre' => $nuevoEstado, 'es_presente' => in_array($nuevoEstado, ['P','T'])]
        );
        $asistencia->id_tipo_asistencia = $tipo->id;
        $asistencia->save();

        $this->actualizarReporte($asistencia->historial->seccion_id, $asistencia->fecha);
        return response()->json(['success' => true]);
    }

    public function apiActualizarEstadoDocente(Request $request)
    {
        $asistenciaId = $request->get('asistencia_id');
        $nuevoEstado = $request->get('asis');
        $asistencia = AsistenciaMaestroHistorica::find($asistenciaId);
        if (!$asistencia) {
            return response()->json(['success' => false, 'message' => 'No encontrado']);
        }
        $tipo = TipoAsistencia::firstOrCreate(
            ['codigo' => $nuevoEstado],
            ['nombre' => $nuevoEstado, 'es_presente' => in_array($nuevoEstado, ['P','T'])]
        );
        $asistencia->id_tipo_asistencia = $tipo->id;
        $asistencia->save();

        $this->actualizarReporteMaestros($asistencia->fecha);
        return response()->json(['success' => true]);
    }

    // ================== ACTUALIZACIÓN INDIVIDUAL (AJAX) ==================
    public function updateEstudianteAsistencia(Request $request, $id)
    {
        try {
            $request->validate([
                'asis' => 'required|in:P,A,J,T',
                'fecha' => 'required|date',
                'id_corte' => 'required|exists:cortes,id',
            ]);

            $estudiante = Estudiante::findOrFail($id);
            $fecha = $request->fecha;
            $corteId = $request->id_corte;
            $asis = $request->asis;

            // Buscar historial activo del estudiante en esa fecha
            $historial = HistorialEstudiante::where('estudiante_id', $estudiante->id)
                ->where('fecha_inicio', '<=', $fecha)
                ->where(function($q) use ($fecha) {
                    $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
                })
                ->first();

            if (!$historial) {
                return response()->json(['success' => false, 'message' => 'No hay historial activo para esta fecha']);
            }

            $tipo = TipoAsistencia::firstOrCreate(
                ['codigo' => $asis],
                ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
            );

            AsistenciaEstudiante::updateOrCreate(
                [
                    'historial_estudiante_id' => $historial->id,
                    'fecha' => $fecha,
                    'id_corte' => $corteId,
                ],
                ['id_tipo_asistencia' => $tipo->id]
            );

            $this->actualizarReporte($historial->seccion_id, $fecha);
            return response()->json([
                'success' => true,
                'message' => 'Asistencia actualizada correctamente',
                'nuevo_estado' => $asis,
                'estudiante_id' => $estudiante->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    public function updateMaestroAsistencia(Request $request, $id)
    {
        try {
            $request->validate([
                'asis' => 'required|in:P,A,J,T',
                'fecha' => 'required|date',
                'id_corte' => 'required|exists:cortes,id',
            ]);

            $maestro = Maestro::findOrFail($id);
            $fecha = $request->fecha;
            $corteId = $request->id_corte;
            $asis = $request->asis;

            $historial = HistorialMaestro::where('maestro_id', $maestro->id)
                ->where('fecha_inicio', '<=', $fecha)
                ->where(function($q) use ($fecha) {
                    $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
                })
                ->first();

            if (!$historial) {
                return response()->json(['success' => false, 'message' => 'No hay historial activo para esta fecha']);
            }

            $tipo = TipoAsistencia::firstOrCreate(
                ['codigo' => $asis],
                ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
            );

            AsistenciaMaestroHistorica::updateOrCreate(
                [
                    'historial_maestro_id' => $historial->id,
                    'fecha' => $fecha,
                    'id_corte' => $corteId,
                ],
                ['id_tipo_asistencia' => $tipo->id]
            );

            $this->actualizarReporteMaestros($fecha);
            return response()->json([
                'success' => true,
                'message' => 'Asistencia actualizada correctamente',
                'nuevo_estado' => $asis,
                'maestro_id' => $maestro->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    private function getEstadoTexto($asis)
    {
        return match ($asis) {
            'P' => 'Presente',
            'A' => 'Ausente',
            'J' => 'Justificado',
            'T' => 'Llegada tarde',
            default => 'Desconocido',
        };
    }
}