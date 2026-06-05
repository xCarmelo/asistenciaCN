<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use App\Models\Corte;
use App\Models\HistorialMaestro;
use App\Models\AsistenciaMaestroHistorica;
use App\Models\TipoAsistencia;
use App\Models\Reporte;
use App\Models\Estado;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaestroAsistenciaController extends Controller
{
    // Listar asistencias de maestros agrupadas por fecha y corte
    public function index(Request $request)
    {
        $query = AsistenciaMaestroHistorica::with(['historial.maestro', 'corte', 'tipoAsistencia']);

        if ($request->filled('fecha')) $query->where('fecha', $request->fecha);
        if ($request->filled('maestro_id')) {
            // Buscar historiales que correspondan a ese maestro
            $historialIds = HistorialMaestro::where('maestro_id', $request->maestro_id)->pluck('id');
            $query->whereIn('historial_maestro_id', $historialIds);
        }
        if ($request->filled('corte_id')) $query->where('id_corte', $request->corte_id);

        $asistencias = $query->get();

        // Agrupar por fecha y corte
        $grupos = $asistencias->groupBy(function ($item) {
            return $item->fecha . '|' . $item->id_corte;
        });

        $resultados = [];
        foreach ($grupos as $key => $grupo) {
            list($fecha, $corteId) = explode('|', $key);
            $corte = Corte::find($corteId);
            $total = $grupo->count();
            $presentes = $grupo->filter(fn($a) => $a->tipoAsistencia && $a->tipoAsistencia->codigo == 'P')->count();
            $ausentes = $grupo->filter(fn($a) => $a->tipoAsistencia && $a->tipoAsistencia->codigo == 'A')->count();
            $justificados = $grupo->filter(fn($a) => $a->tipoAsistencia && $a->tipoAsistencia->codigo == 'J')->count();
            $llegadasTarde = $grupo->filter(fn($a) => $a->tipoAsistencia && $a->tipoAsistencia->codigo == 'T')->count();

            $resultados[] = (object) [
                'fecha' => $fecha,
                'corte' => $corte,
                'total' => $total,
                'presentes' => $presentes,
                'ausentes' => $ausentes,
                'justificados' => $justificados,
                'llegadas_tarde' => $llegadasTarde,
            ];
        }

        // Ordenar por fecha descendente
        usort($resultados, fn($a, $b) => strtotime($b->fecha) - strtotime($a->fecha));

        // Maestros para el filtro (solo los que tienen historial activo o inactivo, pero se muestran todos)
        $maestros = Maestro::orderBy('name')->get();
        $cortes = Corte::all();

        return view('maestros.asistencias.index', compact('resultados', 'maestros', 'cortes'));
    }

    // Redirigir al formulario de creación (lo maneja AsistenciaController)
    public function create()
    {
        return redirect()->route('asistencia.maestros.create');
    }

    // Store (redirige a creación)
    public function store(Request $request)
    {
        return redirect()->route('asistencia.maestros.create');
    }

    // Editar una asistencia individual
    public function edit($id)
    {
        $asistencia = AsistenciaMaestroHistorica::with('historial.maestro')->findOrFail($id);
        $cortes = Corte::all();
        $tipos = TipoAsistencia::all();

        return view('maestros.asistencias.edit', compact('asistencia', 'cortes', 'tipos'));
    }

    // Actualizar una asistencia individual
    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha' => 'required|date',
            'id_corte' => 'required|exists:cortes,id',
            'id_tipo_asistencia' => 'required|exists:tipos_asistencia,id',
        ]);

        $asistencia = AsistenciaMaestroHistorica::findOrFail($id);
        $tipo = TipoAsistencia::findOrFail($request->id_tipo_asistencia);

        $asistencia->update([
            'fecha' => $request->fecha,
            'id_corte' => $request->id_corte,
            'id_tipo_asistencia' => $tipo->id,
            // No se modifica historial_maestro_id, ya que es histórico
        ]);

        // Recalcular reporte de maestros para esa fecha
        $this->actualizarReporteMaestros($asistencia->fecha);

        return redirect()->route('maestros.asistencias.index')
            ->with('success', 'Asistencia actualizada correctamente.');
    }

    // Eliminar una asistencia individual
    public function destroy($id)
    {
        $asistencia = AsistenciaMaestroHistorica::findOrFail($id);
        $fecha = $asistencia->fecha;
        $asistencia->delete();

        // Recalcular reporte
        $this->actualizarReporteMaestros($fecha);

        return redirect()->route('maestros.asistencias.index')
            ->with('success', 'Asistencia eliminada correctamente.');
    }

    // Eliminar todas las asistencias de una fecha (opcionalmente por corte)
    public function destroyByDate($fecha, Request $request)
    {
        $query = AsistenciaMaestroHistorica::where('fecha', $fecha);
        if ($request->filled('corte_id')) {
            $query->where('id_corte', $request->corte_id);
        }
        $deleted = $query->delete();

        // Recalcular reporte (aunque ya no haya registros, se actualiza a cero)
        $this->actualizarReporteMaestros($fecha);

        if ($deleted) {
            return redirect()->route('maestros.asistencias.index')
                ->with('success', "Se eliminaron $deleted registros de asistencia para la fecha $fecha.");
        } else {
            return redirect()->route('maestros.asistencias.index')
                ->with('error', "No se encontraron registros para eliminar en la fecha $fecha.");
        }
    }

    // Edición masiva de asistencias para una fecha (todos los maestros activos en ese momento)
    public function updateMaestros(Request $request)
    {
        $request->validate([
            'fecha'      => 'required|date',
            'id_corte'   => 'required|exists:cortes,id',
            'asistencia' => 'array',
        ]);

        $fecha   = $request->fecha;
        $corteId = $request->id_corte;

        // Obtener todos los HISTORIALES ACTIVOS en esa fecha (que cubren la fecha)
        $historialesActivos = HistorialMaestro::where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->with('maestro')
            ->get();

        foreach ($historialesActivos as $historial) {
            $asis = $request->asistencia[$historial->maestro->id] ?? '';

            if ($asis === '') {
                // Eliminar si existía previamente
                AsistenciaMaestroHistorica::where('fecha', $fecha)
                    ->where('historial_maestro_id', $historial->id)
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
                    'fecha' => $fecha,
                    'historial_maestro_id' => $historial->id,
                    'id_corte' => $corteId,
                ],
                [
                    'id_tipo_asistencia' => $tipo->id,
                ]
            );
        }

        // Recalcular reporte
        $this->actualizarReporteMaestros($fecha);

        return redirect()->route('maestros.asistencias.index')
            ->with('success', 'Asistencia de maestros actualizada correctamente.');
    }

    /**
     * Actualiza el reporte global de maestros para una fecha específica
     */
    private function actualizarReporteMaestros($fecha)
    {
        // Obtener todos los historiales activos en esa fecha (los que deberían haber asistido)
        $historialesActivos = HistorialMaestro::where('fecha_inicio', '<=', $fecha)
            ->where(function($q) use ($fecha) {
                $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', $fecha);
            })
            ->get();

        $cef = 0;
        $cem = 0;
        foreach ($historialesActivos as $h) {
            if ($h->maestro->genero == 'F') $cef++;
            elseif ($h->maestro->genero == 'M') $cem++;
        }

        $asistencias = AsistenciaMaestroHistorica::where('fecha', $fecha)
            ->whereHas('tipoAsistencia', function($q) {
                $q->whereIn('codigo', ['P', 'T']); // presentes o llegadas tarde
            })
            ->with('historial.maestro')
            ->get();

        $crf = 0;
        $crm = 0;
        foreach ($asistencias as $asis) {
            $genero = $asis->historial->maestro->genero;
            if ($genero == 'F') $crf++;
            elseif ($genero == 'M') $crm++;
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
}