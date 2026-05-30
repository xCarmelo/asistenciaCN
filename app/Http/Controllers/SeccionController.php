<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use App\Models\Maestro;
use App\Imports\SeccionesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SeccionController extends Controller
{
    public function index(Request $request)
    {
        $query = Seccion::with('maestroGuia')->orderBy('nombre');

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $secciones = $query->paginate(15)->appends($request->query());

        // Maestros libres (sin ninguna sección activa asignada)
        $maestrosLibres = Maestro::where('estado', 1)
            ->whereDoesntHave('seccionesGuiadas', function($q) {
                $q->where('estado', 1);
            })
            ->orderBy('name')
            ->get();

        $occupiedMaestroIds = Seccion::where('estado', 1)
            ->whereNotNull('id_maestro_guia')
            ->pluck('id_maestro_guia')
            ->toArray();

        $todosMaestros = Maestro::where('estado', 1)->orderBy('name')->get();

        return view('secciones.index', compact('secciones', 'maestrosLibres', 'todosMaestros', 'occupiedMaestroIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|max:50',
            'id_maestro_guia' => 'nullable|exists:maestros,id',
        ]);

        if ($request->filled('id_maestro_guia')) {
            $maestroOcupado = Seccion::where('estado', 1)
                ->where('id_maestro_guia', $request->id_maestro_guia)
                ->exists();
            if ($maestroOcupado) {
                return redirect()->back()->with('error', 'El maestro seleccionado ya está asignado a otra sección activa.')->withInput();
            }
        }

        $existenteActiva = Seccion::where('nombre', $request->nombre)->where('estado', 1)->first();
        if ($existenteActiva) {
            return redirect()->back()->with('error', 'Ya existe una sección activa con el nombre "' . $request->nombre . '".')->withInput();
        }

        $existenteInactiva = Seccion::where('nombre', $request->nombre)->where('estado', 0)->first();
        if ($existenteInactiva) {
            return redirect()->back()->with('warning', 'La sección "' . $request->nombre . '" ya existe pero está desactivada. ¿Desea reactivarla?')
                ->with('reactivar_id', $existenteInactiva->id)
                ->withInput();
        }

        try {
            Seccion::create([
                'nombre'          => $request->nombre,
                'id_maestro_guia' => $request->id_maestro_guia,
                'estado'          => 1,
            ]);
            return redirect()->route('secciones.index', request()->query())->with('success', 'Sección creada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear sección: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear la sección.')->withInput();
        }
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