<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use App\Models\Seccion;
use App\Imports\MaestrosImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaestroController extends Controller
{
    public function index(Request $request)
    {
        $query = Maestro::with('seccionesGuiadas')->orderBy('name');

        if ($request->filled('nombre')) {
            $query->where('name', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('genero')) {
            $query->where('genero', $request->genero);
        }
        if ($request->filled('estado')) {
            $estadoVal = $request->estado == 'Activo' ? 1 : 0;
            $query->where('estado', $estadoVal);
        } else {
            $query->where('estado', 1);
        }

        $maestros = $query->paginate(15)->appends($request->query());

        // Secciones disponibles (activas y sin maestro guía)
        $seccionesDisponibles = Seccion::where('estado', 1)
            ->whereNull('id_maestro_guia')
            ->orderBy('nombre')
            ->get();

        return view('maestros.index', compact('maestros', 'seccionesDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100|unique:maestros,name',
            'genero'    => 'required|in:M,F',
            'tutelado'  => 'nullable|exists:secciones,id',
        ]);

        // Verificar que la sección seleccionada esté disponible (sin maestro)
        if ($request->filled('tutelado')) {
            $seccion = Seccion::find($request->tutelado);
            if (!$seccion || $seccion->id_maestro_guia !== null) {
                return redirect()->back()
                    ->with('error', 'La sección seleccionada ya tiene un maestro guía asignado.')
                    ->withInput();
            }
        }

        try {
            DB::transaction(function () use ($request) {
                $maestro = Maestro::create([
                    'name'   => $request->name,
                    'genero' => $request->genero,
                    'estado' => 1,
                ]);
                if ($request->filled('tutelado')) {
                    $seccion = Seccion::find($request->tutelado);
                    $maestro->seccionesGuiadas()->save($seccion);
                }
            });
            return redirect()->route('maestros.index', request()->query())->with('success', 'Maestro creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear maestro: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el maestro.')->withInput();
        }
    }

    public function update(Request $request, Maestro $maestro)
    {
        $request->validate([
            'name'      => 'required|string|max:100|unique:maestros,name,' . $maestro->id,
            'genero'    => 'required|in:M,F',
            'tutelado'  => 'nullable|exists:secciones,id',
            'estado'    => 'nullable|in:Activo,Inactivo',
        ]);

        // Obtener la sección actual que tiene el maestro (si tiene)
        $seccionActual = $maestro->seccionesGuiadas->first();

        // Si se seleccionó una nueva sección y es diferente a la actual
        if ($request->filled('tutelado') && $request->tutelado != ($seccionActual ? $seccionActual->id : null)) {
            // Verificar que la nueva sección esté disponible
            $nuevaSeccion = Seccion::find($request->tutelado);
            if (!$nuevaSeccion || $nuevaSeccion->id_maestro_guia !== null) {
                return redirect()->back()
                    ->with('error', 'La sección seleccionada ya tiene otro maestro guía asignado.')
                    ->withInput();
            }
        }

        try {
            DB::transaction(function () use ($request, $maestro, $seccionActual) {
                // Actualizar datos del maestro
                $maestro->update([
                    'name'   => $request->name,
                    'genero' => $request->genero,
                    'estado' => $request->estado == 'Activo' ? 1 : 0,
                ]);

                // Liberar la sección actual (si existe)
                if ($seccionActual) {
                    $seccionActual->id_maestro_guia = null;
                    $seccionActual->save();
                }

                // Asignar nueva sección si se seleccionó una
                if ($request->filled('tutelado')) {
                    $nuevaSeccion = Seccion::find($request->tutelado);
                    $maestro->seccionesGuiadas()->save($nuevaSeccion);
                }
            });
            return redirect()->route('maestros.index', request()->query())->with('success', 'Maestro actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el maestro.');
        }
    }

    public function destroy(Maestro $maestro)
    {
        try {
            // Liberar la sección que tuviera asignada (poner id_maestro_guia = null)
            $seccionActual = $maestro->seccionesGuiadas->first();
            if ($seccionActual) {
                $seccionActual->id_maestro_guia = null;
                $seccionActual->save();
            }

            // Desactivar maestro
            $maestro->estado = 0;
            $maestro->save();

            return redirect()->route('maestros.index', request()->query())->with('success', 'Maestro desactivado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al desactivar: ' . $e->getMessage());
            return redirect()->route('maestros.index', request()->query())->with('error', 'No se pudo desactivar el maestro.');
        }
    }

    public function reactivar(Maestro $maestro)
    {
        try {
            $maestro->estado = 1;
            $maestro->save();
            return redirect()->route('maestros.index', request()->query())->with('success', 'Maestro reactivado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al reactivar: ' . $e->getMessage());
            return redirect()->route('maestros.index', request()->query())->with('error', 'Error al reactivar el maestro.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MaestrosImport, $request->file('archivo'));
            return redirect()->route('maestros.index', request()->query())->with('success', 'Maestros importados correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al importar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}