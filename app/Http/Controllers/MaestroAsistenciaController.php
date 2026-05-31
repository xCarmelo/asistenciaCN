<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use App\Models\Corte;
use App\Models\AsistenciaMaestro;
use App\Models\TipoAsistencia;
use Illuminate\Http\Request;
use App\Models\Reporte;
use Carbon\Carbon;

class MaestroAsistenciaController extends Controller
{
    // Listar todas las asistencias de maestros con filtros
public function index(Request $request)
{
    $query = AsistenciaMaestro::with(['maestro', 'corte', 'tipoAsistencia']);

    if ($request->filled('fecha')) $query->where('fecha', $request->fecha);
    if ($request->filled('maestro_id')) $query->where('id_maestro', $request->maestro_id);
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
    usort($resultados, fn($a,$b) => strtotime($b->fecha) - strtotime($a->fecha));

    $maestros = Maestro::orderBy('name')->get();
    $cortes = Corte::all();

    return view('maestros.asistencias.index', compact('resultados', 'maestros', 'cortes', 'asistencias'));
}

    // Formulario para crear una nueva asistencia (redirige al existente)
    public function create()
    {
        return redirect()->route('asistencia.maestros.create');
    }

    // Almacenar (se maneja en AsistenciaController, pero lo dejamos por si acaso)
    public function store(Request $request)
    {
        // Redirige al método original
        return redirect()->route('asistencia.maestros.create');
    }

    // Formulario para editar una asistencia individual
    public function edit($id)
    {
        $asistencia = AsistenciaMaestro::with('maestro')->findOrFail($id);
        $cortes = Corte::all();
        $tipos = TipoAsistencia::all();

        return view('maestros.asistencias.edit', compact('asistencia', 'cortes', 'tipos'));
    }

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


    // Actualizar una asistencia
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
        $asis = $request->asistencia[$m->id] ?? 'P';
        $justificado   = ($asis == 'J');
        $injustificado = ($asis == 'A');
        $tipo = TipoAsistencia::firstOrCreate(
            ['codigo' => $asis],
            ['nombre' => $asis, 'es_presente' => in_array($asis, ['P','T'])]
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

    // Redirigir al listado de asistencias de maestros con mensaje de éxito
    return redirect()->route('maestros.asistencias.index')
        ->with('success', 'Asistencia de maestros actualizada correctamente.');
}

    // Eliminar una asistencia
    public function destroy($id)
    {
        $asistencia = AsistenciaMaestro::findOrFail($id);
        $asistencia->delete();

        return redirect()->route('maestros.asistencias.index')
            ->with('success', 'Asistencia eliminada correctamente.');
    }

    public function destroyByDate($fecha, Request $request)
{
    $query = AsistenciaMaestro::where('fecha', $fecha);
    if ($request->filled('corte_id')) {
        $query->where('id_corte', $request->corte_id);
    }
    $deleted = $query->delete();

    if ($deleted) {
        return redirect()->route('maestros.asistencias.index')
            ->with('success', "Se eliminaron $deleted registros de asistencia para la fecha $fecha.");
    } else {
        return redirect()->route('maestros.asistencias.index')
            ->with('error', "No se encontraron registros para eliminar en la fecha $fecha.");
    }
}
}