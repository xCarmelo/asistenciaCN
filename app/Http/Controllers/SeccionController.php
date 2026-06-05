<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use App\Models\Maestro;
use App\Models\HistorialMaestro;
use App\Imports\SeccionesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SeccionController extends Controller
{
public function index(Request $request)
{
    $query = Seccion::query();

    if ($request->filled('nombre')) {
        $query->where('nombre', 'like', '%' . $request->nombre . '%');
    }

    // Filtro por estado: podrías considerar activa si tiene algún estudiante activo
    if ($request->filled('estado')) {
        // Aquí la lógica depende de lo que consideres "activo"
        // Por ejemplo: sección activa si tiene al menos un historial_estudiante activo
        if ($request->estado == 1) {
            $query->whereHas('historialEstudiantes', fn($q) => $q->whereNull('fecha_fin')->whereHas('estado', fn($e) => $e->where('permite_asistencia', true)));
        } else {
            $query->whereDoesntHave('historialEstudiantes', fn($q) => $q->whereNull('fecha_fin')->whereHas('estado', fn($e) => $e->where('permite_asistencia', true)));
        }
    }

    $secciones = $query->with(['maestroActual' => fn($q) => $q->with('maestro')])
        ->paginate(15)
        ->appends($request->query());

    // Maestros libres (sin historial activo)
    $maestrosLibres = Maestro::whereDoesntHave('historialActivo')
        ->orderBy('name')
        ->get();

    // Para el modal de edición necesitas todos los maestros y los IDs ocupados
    $todosMaestros = Maestro::orderBy('name')->get();
    $occupiedMaestroIds = HistorialMaestro::whereNull('fecha_fin')
        ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
        ->pluck('maestro_id')
        ->toArray();

    return view('secciones.index', compact('secciones', 'maestrosLibres', 'todosMaestros', 'occupiedMaestroIds'));
}

public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:50|unique:secciones,nombre', // validación simple
    ]);

    // Crear la sección (sin estado ni maestro asignado directamente)
    $seccion = Seccion::create(['nombre' => $request->nombre]);

    // Si además deseas asignar un maestro a la sección al crearla (opcional)
    if ($request->filled('id_maestro_guia')) {
        $maestroId = $request->id_maestro_guia;
        // Verificar que el maestro no tenga ya un historial activo en otra sección
        $tieneHistorialActivo = HistorialMaestro::where('maestro_id', $maestroId)
            ->whereNull('fecha_fin')
            ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
            ->exists();

        if ($tieneHistorialActivo) {
            // Opcional: eliminar la sección recién creada o manejarlo
            $seccion->delete();
            return redirect()->back()->with('error', 'El maestro ya tiene una sección activa asignada.')->withInput();
        }

        // Crear historial activo para el maestro en esta sección
        HistorialMaestro::create([
            'maestro_id' => $maestroId,
            'seccion_id' => $seccion->id,
            'estado_id' => Estado::where('nombre', 'Activo')->first()->id, // asumiendo que existe 'Activo' con permite_asistencia=1
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => null,
        ]);
    }

    return redirect()->route('secciones.index')->with('success', 'Sección creada correctamente.');
}

public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'required|string|max:50',
        'id_maestro_guia' => 'nullable|exists:maestros,id',
        'estado' => 'nullable|boolean',
    ]);

    // Buscar sección manualmente
    $seccion = Seccion::findOrFail($id);

    // Limpiar espacios
    $nombreNuevo = trim($request->nombre);

    /*
    |--------------------------------------------------------------------------
    | VALIDAR DUPLICADOS
    |--------------------------------------------------------------------------
    */

    $duplicada = Seccion::whereRaw('TRIM(nombre) = ?', [$nombreNuevo])
        ->where('estado', 1)
        ->where('id', '!=', $id)
        ->exists();

    if ($duplicada) {

        return redirect()->back()
            ->with(
                'error',
                'Ya existe otra sección activa con el nombre "' . $nombreNuevo . '".'
            )
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR MAESTRO OCUPADO
    |--------------------------------------------------------------------------
    */

    if (
        $request->filled('id_maestro_guia') &&
        $request->id_maestro_guia != $seccion->id_maestro_guia
    ) {

        $maestroOcupado = Seccion::where('estado', 1)
            ->where('id_maestro_guia', $request->id_maestro_guia)
            ->where('id', '!=', $id)
            ->exists();

        if ($maestroOcupado) {

            return redirect()->back()
                ->with(
                    'error',
                    'El maestro seleccionado ya está asignado a otra sección activa.'
                )
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    try {

        $seccion->update([
            'nombre' => $nombreNuevo,
            'id_maestro_guia' => $request->id_maestro_guia,
            'estado' => $request->estado ?? $seccion->estado,
        ]);

        return redirect()
            ->route('secciones.index', request()->query())
            ->with('success', 'Sección actualizada correctamente.');

    } catch (\Exception $e) {

        Log::error('Error al actualizar sección: ' . $e->getMessage());

        return redirect()->back()
            ->with(
                'error',
                'Error al actualizar: ' . $e->getMessage()
            )
            ->withInput();
    }
}

    /**
     * Desactivar una sección (NO eliminarla físicamente)
     */
/**
 * Desactivar una sección (eliminación lógica)
 */
public function destroy($id)
{
    try {

        $seccion = Seccion::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR ESTUDIANTES ACTIVOS
        |--------------------------------------------------------------------------
        | Ajusta esta validación según tu BD.
        | Si tu campo estado es 1/0 usa ->where('estado', 1)
        */

        if (method_exists($seccion, 'estudiantes')) {

            $tieneActivos = $seccion->estudiantes()
                ->where('estado', 1)
                ->exists();

            if ($tieneActivos) {

                return redirect()
                    ->route('secciones.index', request()->query())
                    ->with(
                        'error',
                        'No se puede desactivar la sección porque tiene estudiantes activos.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DESACTIVAR SECCIÓN
        |--------------------------------------------------------------------------
        */

        $seccion->update([
            'estado' => 0,
            'id_maestro_guia' => null,
        ]);

        return redirect()
            ->route('secciones.index', request()->query())
            ->with(
                'success',
                'Sección desactivada correctamente.'
            );

    } catch (\Exception $e) {

        Log::error(
            'Error al desactivar sección: ' . $e->getMessage()
        );

        return redirect()
            ->route('secciones.index', request()->query())
            ->with(
                'error',
                'No se pudo desactivar la sección: ' . $e->getMessage()
            );
    }
}

    public function reactivar($id)
    {
        try {
            $seccion = Seccion::findOrFail($id);
            $existenteActiva = Seccion::where('nombre', $seccion->nombre)->where('estado', 1)->where('id', '!=', $id)->first();
            if ($existenteActiva) {
                return redirect()->route('secciones.index')->with('error', 'Ya existe una sección activa con el nombre "' . $seccion->nombre . '".');
            }
            $seccion->estado = 1;
            $seccion->save();
            return redirect()->route('secciones.index')->with('success', 'Sección reactivada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al reactivar: ' . $e->getMessage());
            return redirect()->route('secciones.index')->with('error', 'Error al reactivar la sección.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new SeccionesImport, $request->file('archivo'));
            return redirect()->route('secciones.index', request()->query())->with('success', 'Secciones importadas correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al importar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}