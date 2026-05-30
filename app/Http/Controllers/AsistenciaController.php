<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use App\Models\Corte;
use App\Models\Estudiante;
use App\Models\Maestro;
use App\Models\Asistencia;
use App\Models\AsistenciaMaestro;
use App\Models\TipoAsistencia;
use App\Models\Reporte;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $secciones = Seccion::where('estado', 1)->orderBy('nombre')->get();
        $cortes = Corte::all();

        $filtros = [
            'seccion_id' => $request->get('seccion_id'),
            'corte_id'   => $request->get('corte_id'),
            'desde'      => $request->get('desde', Carbon::now()->startOfMonth()->toDateString()),
            'hasta'      => $request->get('hasta', Carbon::now()->toDateString()),
        ];

        // Usar la tabla reporte para mostrar los totales por fecha
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

        $registros = $reportesPorFecha->map(function ($item) {
            return (object) [
                'fecha' => $item->fecha,
                'F' => $item->femeninas_presentes,
                'V' => $item->varones_presentes,
                'Total' => $item->femeninas_presentes + $item->varones_presentes,
            ];
        });

        return view('asistencia.index', compact('secciones', 'cortes', 'filtros', 'registros'));
    }

    // ================== ASISTENCIA ESTUDIANTES ==================
    public function createEstudiantes(Request $request)
    {
        $secciones = Seccion::where('estado', 1)->orderBy('nombre')->get();
        $cortes = Corte::all();
        $seccionId = $request->get('seccion_id');
        $fecha = $request->get('fecha', date('Y-m-d'));
        $corteId = $request->get('corte_id', 1);

        $estudiantes = collect();
        if ($seccionId) {
        $estudiantes = Estudiante::where('id_seccion', $seccionId)
            ->where('estado', 'Activo')
            ->orderBy('numero_lista')
            ->get();
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

    // Verificar si ya existe asistencia para esta fecha, corte y sección
    $existe = Asistencia::where('fecha', $fecha)
        ->where('id_corte', $corteId)
        ->where('id_seccion', $seccionId)
        ->exists();

    if ($existe) {
        return redirect()->back()
            ->withInput()
            ->with('error_modal', 'Ya existe una asistencia para esta fecha, corte y sección. No se puede duplicar.');
    }

    // Obtener SOLO estudiantes ACTIVOS (para crear nuevas asistencias)
    $estudiantes = Estudiante::where('id_seccion', $seccionId)
        ->where('estado', 'Activo')
        ->orderBy('numero_lista')
        ->get();

    foreach ($estudiantes as $est) {
        $asis = $request->asistencia[$est->id] ?? 'P';

        // Obtener o crear el tipo de asistencia (P, A, J, T)
        $tipo = TipoAsistencia::firstOrCreate(
            ['codigo' => $asis],
            ['nombre' => $asis, 'es_presente' => in_array($asis, ['P', 'T'])]
        );

        // Guardar la asistencia (es nueva, no existe duplicado)
        Asistencia::create([
            'fecha'         => $fecha,
            'id_estudiante' => $est->id,
            'id_corte'      => $corteId,
            'id_seccion'    => $seccionId,
            'asis'          => $asis,
            'justificado'   => ($asis == 'J'),
            'injustificado' => ($asis == 'A'),
            'id_tipo_asistencia' => $tipo->id,
        ]);
    }

    // Actualizar reportes consolidados
    $this->actualizarReporte($seccionId, $fecha);
    $this->actualizarReporteMaestros($fecha);

    return redirect()->route('asistencia.estudiantes.create', [
        'seccion_id' => $seccionId,
        'corte_id'   => $corteId,
        'fecha'      => $fecha,
    ])->with('success_modal', 'Asistencia de estudiantes guardada correctamente.');
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

        $existe = AsistenciaMaestro::where('fecha', $fecha)
            ->where('id_corte', $corteId)
            ->exists();

        if ($existe) { 
            return redirect()->back()
                ->withInput()
                ->with('error_modal', 'Ya existe una asistencia de maestros para esta fecha y corte.');
        }

        $maestros = Maestro::where('estado', 1)->get();

        foreach ($maestros as $m) {
            $asis = $request->asistencia[$m->id] ?? 'P';
            $justificado = ($asis == 'J');
            $injustificado = ($asis == 'A');
            // Guardar el valor real de asistencia
            $tipo = TipoAsistencia::firstOrCreate(
                ['codigo' => $asis],
                ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
            );

            AsistenciaMaestro::create([
                "fecha"      => $fecha,
                "id_maestro" => $m->id,
                "id_corte"   => $corteId,
                "asis"       => $asis,
                "justificado"=> $justificado,
                "injustificado"=> $injustificado,
                "tutelado"   => null,
                "id_tipo_asistencia" => $tipo->id,
            ]);
        }
        $this->actualizarReporteMaestros($fecha);
        return redirect()->route('asistencia.maestros.create', [
            'corte_id' => $corteId,
            'fecha'    => $fecha,
        ])->with('success_modal', 'Asistencia de maestros guardada correctamente.');
    }

    /**
     * Eliminar todos los registros de asistencia (estudiantes) para una fecha específica.
     * Opcionalmente se puede filtrar por corte_id y seccion_id.
     */
public function destroyByDate($fecha, Request $request)
{
    $corteId = $request->get('corte_id');
    $seccionId = $request->get('seccion_id');

    $query = Asistencia::where('fecha', $fecha);
    if ($corteId) {
        $query->where('id_corte', $corteId);
    }
    if ($seccionId) {
        $query->where('id_seccion', $seccionId);
    }
    $deleted = $query->delete();

    // Eliminar reportes asociados
    $reporteQuery = Reporte::where('fecha', $fecha);
    if ($seccionId) {
        $reporteQuery->where('id_seccion', $seccionId);
    }
    $reporteQuery->delete();

    if ($deleted) {
        $this->actualizarReporteMaestros($fecha);
        return redirect()->route('asistencia.index', ['corte_id' => $corteId, 'seccion_id' => $seccionId])
            ->with('success_modal', "Se eliminaron $deleted registros de asistencia y sus reportes para la fecha $fecha.");
    } else {
        $this->actualizarReporteMaestros($fecha);
        return redirect()->route('asistencia.index', ['corte_id' => $corteId, 'seccion_id' => $seccionId])
            ->with('error_modal', "No se encontraron registros para eliminar en la fecha $fecha.");
    }
}

        private function actualizarReporte($seccionId, $fecha)
    {
        // Solo estudiantes con asistencia real y tipo válido
        $asistencias = \App\Models\Asistencia::where('fecha', $fecha)
            ->where('id_seccion', $seccionId)
            ->whereNotNull('id_tipo_asistencia')
            ->get();

        $estudiantesIds = $asistencias->pluck('id_estudiante')->unique();
        $estudiantes = \App\Models\Estudiante::whereIn('id', $estudiantesIds)->get();

        $cef = $estudiantes->where('genero', 'F')->count();
        $cem = $estudiantes->where('genero', 'M')->count();

        $presentesTarde = $asistencias->whereIn('asis', ['P', 'T']);
        $crf = 0;
        $crm = 0;
        foreach ($presentesTarde as $asis) {
            $estudiante = $estudiantes->find($asis->id_estudiante);
            if ($estudiante) {
                if ($estudiante->genero == 'F') {
                    $crf++;
                } elseif ($estudiante->genero == 'M') {
                    $crm++;
                }
            }
        }

        \App\Models\Reporte::updateOrCreate(
            [
                'id_seccion' => $seccionId,
                'fecha'      => $fecha,
            ],
            [
                'cef' => $cef,
                'cem' => $cem,
                'crf' => $crf,
                'crm' => $crm,
            ]
        );
    }

    /**
     * Muestra el reporte detallado de asistencia para una fecha específica.
     */
    public function reporte($fecha, Request $request)
    {
        $corteId = $request->get('corte_id', 1);
        $secciones = Seccion::where('estado', 1)->orderBy('nombre')->get();

        // Ordenar secciones por grado (extraer número del nombre)
        $secciones = $secciones->sortBy(function($seccion) {
            preg_match('/(d+)/', $seccion->nombre, $matches);
            $grado = isset($matches[1]) ? (int)$matches[1] : 999;
            return $grado . '-' . $seccion->nombre;
        })->values();

        $data = [];
        $dataPorGrado = [];
        $totalRealGeneral = ['F' => 0, 'V' => 0];

        foreach ($secciones as $seccion) {
            // Obtener reporte existente o calcularlo
            $reporte = Reporte::where('id_seccion', $seccion->id)
                ->where('fecha', $fecha)
                ->first();

            if (!$reporte) {
                // Calcular desde cero
                $cef = Estudiante::where('id_seccion', $seccion->id)->where('estado', 'Activo')->where('genero', 'F')->count();
                $cem = Estudiante::where('id_seccion', $seccion->id)->where('estado', 'Activo')->where('genero', 'M')->count();
                $asistencias = Asistencia::where('fecha', $fecha)
                    ->where('id_seccion', $seccion->id)
                    ->whereIn('asis', ['P', 'T'])
                    ->get();
                $crf = 0; $crm = 0;
                foreach ($asistencias as $asis) {
                    $est = Estudiante::find($asis->id_estudiante);
                    if ($est) {
                        if ($est->genero == 'F') $crf++;
                        elseif ($est->genero == 'M') $crm++;
                    }
                }
                $reporte = (object) ['cef' => $cef, 'cem' => $cem, 'crf' => $crf, 'crm' => $crm];
            }

            // Obtener estudiantes con su asistencia
            $estudiantes = Estudiante::where('id_seccion', $seccion->id)
                ->orderBy('numero_lista')
                ->get();
            foreach ($estudiantes as $est) {
                $asis = Asistencia::where('fecha', $fecha)
                    ->where('id_estudiante', $est->id)
                    ->first();
                $est->asistencia = $asis ? ($asis->justificado ? 'J' : ($asis->asis == 'P' ? 'P' : ($asis->asis == 'T' ? 'T' : 'A'))) : 'P';
            }

            $ausentes = $estudiantes->filter(function($est) {
                return $est->asistencia != 'P';
            });

            $data[] = [
                'seccion' => $seccion,
                'reporte' => $reporte,
                'estudiantes' => $estudiantes,
                'ausentes' => $ausentes,
            ];

            // Acumular por grado
            preg_match('/(d+)/', $seccion->nombre, $matches);
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

        // DATOS DE DOCENTES
        $maestros = Maestro::where('estado', 1)->orderBy('name')->get();
        foreach ($maestros as $m) {
            $asis = AsistenciaMaestro::where('fecha', $fecha)
                ->where('id_maestro', $m->id)
                ->first();
            $m->asistencia = $asis ? ($asis->justificado ? 'J' : ($asis->asis == 'P' ? 'P' : ($asis->asis == 'T' ? 'T' : 'A'))) : 'P';
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

public function apiAusentesSeccion(Request $request)
{
    $seccionId = $request->get('seccion_id');
    $fecha = $request->get('fecha');
    $asistencias = Asistencia::where('fecha', $fecha)
        ->where('id_seccion', $seccionId)
        ->whereIn('asis', ['A', 'J', 'T'])
        ->with('estudiante')
        ->get();
    $data = [];
    foreach ($asistencias as $a) {
        $data[] = [
            'id' => $a->id,
            'estudiante' => ['name' => $a->estudiante->name],
            'asis' => $a->asis,
            'estado_texto' => $this->getEstadoTexto($a->asis),
        ];
    }
    return response()->json($data);
}

public function apiAusentesDocentes(Request $request)
{
    $fecha = $request->get('fecha');
    $asistencias = AsistenciaMaestro::where('fecha', $fecha)
        ->whereIn('asis', ['A', 'J', 'T'])
        ->with('maestro')
        ->get();
    $data = [];
    foreach ($asistencias as $a) {
        $data[] = [
            'id' => $a->id,
            'maestro' => ['name' => $a->maestro->name],
            'asis' => $a->asis,
            'estado_texto' => $this->getEstadoTexto($a->asis),
        ];
    }
    return response()->json($data);
}

public function apiActualizarEstado(Request $request)
{
    $asistenciaId = $request->get('asistencia_id');
    $nuevoEstado = $request->get('asis');
    $asistencia = Asistencia::find($asistenciaId);
    if (!$asistencia) {
        return response()->json(['success' => false, 'message' => 'No encontrado']);
    }
    $asistencia->asis = ($nuevoEstado == 'P') ? 'P' : 'A';
    $asistencia->justificado = ($nuevoEstado == 'J');
    $asistencia->injustificado = ($nuevoEstado == 'A');
    $asistencia->save();
    // Actualizar reporte
    $this->actualizarReporte($asistencia->id_seccion, $asistencia->fecha);
    return response()->json(['success' => true]);
}

public function apiActualizarEstadoDocente(Request $request)
{
    $asistenciaId = $request->get('asistencia_id');
    $nuevoEstado = $request->get('asis');
    $asistencia = AsistenciaMaestro::find($asistenciaId);
    if (!$asistencia) {
        return response()->json(['success' => false, 'message' => 'No encontrado']);
    }
    $asistencia->asis = ($nuevoEstado == 'P') ? 'P' : 'A';
    $asistencia->justificado = ($nuevoEstado == 'J');
    $asistencia->injustificado = ($nuevoEstado == 'A');
    $asistencia->save();
    // Para docentes no hay reporte, pero podrías tener una lógica similar si se requiere.
    return response()->json(['success' => true]);
}

private function getEstadoTexto($asis)
{
    switch ($asis) {
        case 'P': return 'Presente';
        case 'A': return 'Ausente';
        case 'J': return 'Justificado';
        case 'T': return 'Llegada tarde';
        default: return 'Desconocido';
    }
}




    /**
     * Actualizar la asistencia de un estudiante individual (vía AJAX)
     */
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
            $justificado = ($asis == 'J');
            $injustificado = ($asis == 'A');
            $llegadaTarde = ($asis == 'T');
            $asisGuardar = $asis; // Guardar el valor real
            $tipo = TipoAsistencia::firstOrCreate(
                ['codigo' => $asis],
                ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
            );

            Asistencia::updateOrCreate(
                [
                    'fecha' => $fecha,
                    'id_estudiante' => $estudiante->id,
                    'id_corte' => $corteId,
                ],
                [
                    'id_seccion' => $estudiante->id_seccion,
                    'asis' => $asisGuardar,
                    'justificado' => $justificado,
                    'injustificado' => $injustificado,
                    'id_tipo_asistencia' => $tipo->id,
                ]
            );

            // Actualizar reporte de la sección
            $this->actualizarReporte($estudiante->id_seccion, $fecha);

            return response()->json([
                'success' => true,
                'message' => 'Asistencia actualizada correctamente',
                'nuevo_estado' => $asis,
                'estudiante_id' => $estudiante->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar la asistencia de un maestro individual (vía AJAX)
     */
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
            $justificado = ($asis == 'J');
            $injustificado = ($asis == 'A');
            $asisGuardar = $asis; // Guardar el valor real
            $tipo = TipoAsistencia::firstOrCreate(
                ['codigo' => $asis],
                ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
            );

            AsistenciaMaestro::updateOrCreate(
                [
                    'fecha' => $fecha,
                    'id_maestro' => $maestro->id,
                    'id_corte' => $corteId,
                ],
                [
                    'asis' => $asisGuardar,
                    'justificado' => $justificado,
                    'injustificado' => $injustificado,
                    'id_tipo_asistencia' => $tipo->id,
                ]
            );
            $this->actualizarReporteMaestros($fecha);
            return response()->json([
                'success' => true,
                'message' => 'Asistencia actualizada correctamente',
                'nuevo_estado' => $asis,
                'maestro_id' => $maestro->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    
    /**
     * Obtener datos del reporte en formato JSON para la vista
     */
public function getReporteData($fecha, Request $request)
{
    $corteId = $request->get('corte_id', 1);
    
    // Obtener todas las secciones que tienen asistencia en esta fecha
    $seccionesConAsistencia = Asistencia::where('fecha', $fecha)
        ->where('id_corte', $corteId)
        ->distinct('id_seccion')
        ->pluck('id_seccion')
        ->toArray();
    
    $secciones = Seccion::whereIn('id', $seccionesConAsistencia)
        ->where('estado', 1)
        ->get();
    
    // Ordenar por grado
    $secciones = $secciones->sortBy(function($seccion) {
        preg_match('/(\d+)/', $seccion->nombre, $matches);
        return isset($matches[1]) ? (int)$matches[1] : 999;
    });
    
    $data = [];
    $totalRealGeneral = ['F' => 0, 'V' => 0];
    
    foreach ($secciones as $seccion) {
        // Obtener reporte de estudiantes
        $reporte = Reporte::where('id_seccion', $seccion->id)
            ->where('fecha', $fecha)
            ->where('tipo', 'estudiante')
            ->first();
        
        if (!$reporte) {
            $this->actualizarReporte($seccion->id, $fecha);
            $reporte = Reporte::where('id_seccion', $seccion->id)
                ->where('fecha', $fecha)
                ->where('tipo', 'estudiante')
                ->first();
        }
        
        // Obtener estudiantes con asistencia
            $estudiantes = Estudiante::where('id_seccion', $seccion->id)
                ->orderBy('numero_lista')
                ->get();
        
        $ausentes = [];
        $justificadosF = 0;
        $justificadosM = 0;
        $injustificadosF = 0;
        $injustificadosM = 0;
        
        foreach ($estudiantes as $est) {
            $asis = Asistencia::where('fecha', $fecha)
                ->where('id_estudiante', $est->id)
                ->first();
            
            $estado = $asis ? ($asis->justificado ? 'J' : ($asis->asis == 'P' ? 'P' : ($asis->asis == 'T' ? 'T' : 'A'))) : 'P';
            $est->asistencia = $estado;
            
            if ($estado != 'P') {
                $ausentes[] = [
                    'id' => $est->id,
                    'name' => $est->name,
                    'numero_lista' => $est->numero_lista,
                    'asistencia' => $estado
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
            'ausentes' => $ausentes
        ];
        
        $totalRealGeneral['F'] += $reporte->crf;
        $totalRealGeneral['V'] += $reporte->crm;
    }
    
    // ================== DATOS DE DOCENTES (desde tabla reportes - histórico) ==================
    $reporteDocentes = Reporte::where('tipo', 'maestro')
        ->where('fecha', $fecha)
        ->first();
    
    if ($reporteDocentes) {
        // Leer desde el reporte histórico
        $docentesEsperadosF = $reporteDocentes->cef;
        $docentesEsperadosV = $reporteDocentes->cem;
        $docentesRealesF = $reporteDocentes->crf;
        $docentesRealesV = $reporteDocentes->crm;
        
        // Obtener lista de maestros ausentes/justificados/tarde para mostrar en la tabla interna
        $maestros = Maestro::where('estado', 1)->orderBy('name')->get();
        $docentesAusentes = [];
        foreach ($maestros as $m) {
            $asis = AsistenciaMaestro::where('fecha', $fecha)
                ->where('id_maestro', $m->id)
                ->first();
            $estado = $asis ? ($asis->justificado ? 'J' : ($asis->asis == 'P' ? 'P' : ($asis->asis == 'T' ? 'T' : 'A'))) : 'P';
            if ($estado != 'P') {
                $docentesAusentes[] = [
                    'id' => $m->id,
                    'name' => $m->name,
                    'asistencia' => $estado
                ];
            }
        }
    } else {
        // Si no hay reporte histórico (por si acaso), calcular en vivo y crearlo
        $maestros = Maestro::where('estado', 1)->orderBy('name')->get();
        $docentesEsperadosF = $maestros->where('genero', 'F')->count();
        $docentesEsperadosV = $maestros->where('genero', 'M')->count();
        $docentesRealesF = 0;
        $docentesRealesV = 0;
        $docentesAusentes = [];
        
        foreach ($maestros as $m) {
            $asis = AsistenciaMaestro::where('fecha', $fecha)
                ->where('id_maestro', $m->id)
                ->first();
            $estado = $asis ? ($asis->justificado ? 'J' : ($asis->asis == 'P' ? 'P' : ($asis->asis == 'T' ? 'T' : 'A'))) : 'P';
            if ($estado == 'P') {
                if ($m->genero == 'F') $docentesRealesF++;
                else $docentesRealesV++;
            } else {
                $docentesAusentes[] = [
                    'id' => $m->id,
                    'name' => $m->name,
                    'asistencia' => $estado
                ];
            }
        }
        
        // Crear el reporte histórico para futuras consultas
        $this->actualizarReporteMaestros($fecha);
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
            'ausentes' => $docentesAusentes
        ]
    ]);
}

    
    /**
     * Muestra el formulario para editar la asistencia de una sección completa.
     */
    public function editEstudiantesSeccion($seccionId, $fecha)
    {
        $seccion = Seccion::findOrFail($seccionId);
        $estudiantes = Estudiante::where('id_seccion', $seccionId)
            ->orderBy('numero_lista')
            ->get();

        // Cargar asistencias existentes SOLO con tipo válido
        foreach ($estudiantes as $est) {
            $asis = Asistencia::where('fecha', $fecha)
                ->where('id_estudiante', $est->id)
                ->whereNotNull('id_tipo_asistencia')
                ->first();

            if ($asis) {
                if ($asis->justificado) {
                    $est->asistencia_actual = 'J';
                } elseif ($asis->asis == 'A') {
                    $est->asistencia_actual = 'A';
                } elseif ($asis->asis == 'T') {
                    $est->asistencia_actual = 'T';
                } else {
                    $est->asistencia_actual = 'P';
                }
            } else {
                // Sin registro válido: valor vacío para que no se muestre
                $est->asistencia_actual = '';
            }
        }

        // Filtrar solo estudiantes con asistencia válida
        $estudiantes = $estudiantes->filter(function($est) {
            return $est->asistencia_actual !== '';
        })->values();

        $cortes = Corte::all();
        $corteId = 1; // o podrías pasar el corte actual desde el reporte

        return view('asistencia.editar_estudiantes', compact('seccion', 'estudiantes', 'fecha', 'cortes', 'corteId'));
}
/**
 * Actualiza la asistencia de todos los estudiantes de una sección.
 */
public function updateEstudiantesSeccion(Request $request)
{
    $request->validate([
        'fecha'      => 'required|date',
        'id_seccion' => 'required|exists:secciones,id',
        'id_corte'   => 'required|exists:cortes,id',
        'asistencia' => 'array',
    ]);

    $fecha     = $request->fecha;
    $seccionId = $request->id_seccion;
    $corteId   = $request->id_corte;

    $estudiantes = Estudiante::where('id_seccion', $seccionId)
        ->orderBy('numero_lista')
        ->get();

    foreach ($estudiantes as $est) {
        $asis = $request->asistencia[$est->id] ?? '';

        // Si el valor es vacío, eliminar cualquier registro existente (si lo hay)
        if ($asis === '') {
            Asistencia::where('fecha', $fecha)
                ->where('id_estudiante', $est->id)
                ->where('id_corte', $corteId)
                ->whereNotNull('id_tipo_asistencia')
                ->delete();
            continue;
        }

        // Para estados válidos, actualizar o crear
        $justificado   = ($asis == 'J');
        $injustificado = ($asis == 'A');

        $tipo = TipoAsistencia::firstOrCreate(
            ['codigo' => $asis],
            ['nombre' => $asis, 'es_presente' => in_array($asis, ['P', 'T'])]
        );

        Asistencia::updateOrCreate(
            [
                'fecha'         => $fecha,
                'id_estudiante' => $est->id,
                'id_corte'      => $corteId,
            ],
            [
                'id_seccion'           => $seccionId,
                'asis'                 => $asis,
                'justificado'          => $justificado,
                'injustificado'        => $injustificado,
                'id_tipo_asistencia'   => $tipo->id,
            ]
        );
    }

    // Actualizar reporte de estudiantes
    $this->actualizarReporte($seccionId, $fecha);
    $this->actualizarReporteMaestros($fecha);

    return redirect()->route('asistencia.reporte', $fecha)
        ->with('success_modal', 'Asistencia actualizada correctamente.');
}

    /**
     * Muestra el formulario para editar la asistencia de todos los maestros.
     */
public function editMaestros($fecha)
{
    $maestros = Maestro::where('estado', 1)->orderBy('name')->get();

    foreach ($maestros as $m) {
        $asis = AsistenciaMaestro::where('fecha', $fecha)
            ->where('id_maestro', $m->id)
            ->first();

        if ($asis) {
            if ($asis->justificado) {
                $m->asistencia_actual = 'J';
            } elseif ($asis->asis == 'A') {
                $m->asistencia_actual = 'A';
            } elseif ($asis->asis == 'T') {
                $m->asistencia_actual = 'T';
            } else {
                $m->asistencia_actual = 'P';
            }
        } else {
            // Sin registro: valor vacío para que muestre "Seleccionar"
            $m->asistencia_actual = '';
        }
    }

    $this->actualizarReporteMaestros($fecha);

    $cortes = Corte::all();
    $corteId = 1;

    return view('asistencia.editar_maestros', compact('maestros', 'fecha', 'cortes', 'corteId'));
}

 /**
 * Actualiza la asistencia de todos los maestros.
 */
public function updateMaestros(Request $request)
{
    $request->validate([
        'fecha'      => 'required|date',
        'id_corte'   => 'required|exists:cortes,id',
        'asistencia' => 'array',
    ]);

    $fecha   = $request->fecha;
    $corteId = $request->id_corte;

    $maestros = Maestro::where('estado', 1)->get();

    foreach ($maestros as $m) {
        $asis = $request->asistencia[$m->id] ?? '';

        // Si el valor es vacío, eliminar cualquier registro existente
        if ($asis === '') {
            AsistenciaMaestro::where('fecha', $fecha)
                ->where('id_maestro', $m->id)
                ->where('id_corte', $corteId)
                ->delete();
            continue;
        }

        $justificado   = ($asis == 'J');
        $injustificado = ($asis == 'A');

        $tipo = TipoAsistencia::firstOrCreate(
            ['codigo' => $asis],
            ['nombre' => $asis, 'es_presente' => in_array($asis, ['P', 'T'])]
        );

        AsistenciaMaestro::updateOrCreate(
            [
                'fecha'      => $fecha,
                'id_maestro' => $m->id,
                'id_corte'   => $corteId,
            ],
            [
                'asis'                 => $asis,
                'justificado'          => $justificado,
                'injustificado'        => $injustificado,
                'id_tipo_asistencia'   => $tipo->id,
            ]
        );
    }

    $this->actualizarReporteMaestros($fecha);

    return redirect()->route('asistencia.reporte', $fecha)
        ->with('success_modal', 'Asistencia de maestros actualizada correctamente.');
}


        /**
     * Actualiza el reporte global de maestros para una fecha específica
     */
    private function actualizarReporteMaestros($fecha)
    {
        $maestros = Maestro::where('estado', 1)->get();
        
        $cef = $maestros->where('genero', 'F')->count();
        $cem = $maestros->where('genero', 'M')->count();
        
        $asistencias = AsistenciaMaestro::where('fecha', $fecha)
            ->whereIn('asis', ['P', 'T']) // Contar presentes y llegadas tarde
            ->get();
        
        $crf = 0;
        $crm = 0;
        foreach ($asistencias as $asis) {
            $maestro = $maestros->find($asis->id_maestro);
            if ($maestro) {
                if ($maestro->genero == 'F') $crf++;
                elseif ($maestro->genero == 'M') $crm++;
            }
        }
        
        Reporte::updateOrCreate(
            [
                'tipo' => 'maestro',
                'fecha' => $fecha,
                'id_maestro' => null,
            ],
            [
                'id_seccion' => null,
                'cef' => $cef,
                'cem' => $cem,
                'crf' => $crf,
                'crm' => $crm,
            ]
        );
    }

    
    // ================== ASISTENCIA MAESTROS ==================
public function createMaestros(Request $request)
{
    $cortes = Corte::all();
    $maestros = Maestro::where('estado', 1)->orderBy('name')->get();
    $fecha = $request->get('fecha', date('Y-m-d'));
    $corteId = $request->get('corte_id', 1);
    return view('asistencia.maestros', compact('cortes', 'maestros', 'fecha', 'corteId'));
}
}




