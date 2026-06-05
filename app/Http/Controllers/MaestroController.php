<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use App\Models\Seccion;
use App\Models\HistorialMaestro;
use App\Models\Estado;
use App\Imports\MaestrosImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaestroController extends Controller
{
public function index()
{
    $maestros = Maestro::with('historialActivo.seccion', 'historialActivo.estado')
        ->orderBy('name')
        ->paginate(15);   // Cambiado de get() a paginate()

    // Solo secciones disponibles (sin maestro activo)
    $seccionesDisponibles = Seccion::whereDoesntHave('historialMaestros', function($q) {
        $q->whereNull('fecha_fin')->whereHas('estado', function($e) {
            $e->where('permite_asistencia', true);
        });
    })->orderBy('nombre')->get();

    return view('maestros.index', compact('maestros', 'seccionesDisponibles'));
}

    public function create()
    {
        $seccionesDisponibles = Seccion::whereDoesntHave('historialMaestros', function($q) {
            $q->whereNull('fecha_fin')->whereHas('estado', function($e) {
                $e->where('permite_asistencia', true);
            });
        })->orderBy('nombre')->get();
        return view('maestros.create', compact('seccionesDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100|unique:maestros,name',
            'genero'    => 'required|in:M,F',
            'tutelado'  => 'nullable|exists:secciones,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $maestro = Maestro::create([
                    'name'   => $request->name,
                    'genero' => $request->genero,
                    'estado_general' => 1,
                ]);

                $estadoActivo = Estado::where('nombre', 'Activo')->first();

                HistorialMaestro::create([
                    'maestro_id'    => $maestro->id,
                    'seccion_id'    => $request->tutelado,
                    'estado_id'     => $estadoActivo->id,
                    'fecha_inicio'  => now()->toDateString(),
                    'fecha_fin'     => null,
                ]);
            });
            return redirect()->route('maestros.index')->with('success', 'Maestro creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear maestro: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear el maestro.')->withInput();
        }
    }

    public function edit(Maestro $maestro)
    {
        $historialActivo = $maestro->historialActivo;
        $seccionesDisponibles = Seccion::whereDoesntHave('historialMaestros', function($q) use ($maestro) {
            $q->whereNull('fecha_fin')
              ->where('maestro_id', '!=', $maestro->id)
              ->whereHas('estado', function($e) {
                  $e->where('permite_asistencia', true);
              });
        })->orderBy('nombre')->get();

        return view('maestros.edit', compact('maestro', 'historialActivo', 'seccionesDisponibles'));
    }

    public function update(Request $request, Maestro $maestro)
    {
        $request->validate([
            'name'      => 'required|string|max:100|unique:maestros,name,' . $maestro->id,
            'genero'    => 'required|in:M,F',
            'tutelado'  => 'nullable|exists:secciones,id',
            'estado'    => 'nullable|in:Activo,Inactivo',
        ]);

        try {
            DB::transaction(function () use ($request, $maestro) {
                $historialActual = $maestro->historialActivo;
                $nombreEstado = $request->estado ?? ($historialActual->estado->nombre ?? 'Activo');
                $nuevoEstado = Estado::where('nombre', $nombreEstado)->first();
                $nuevaSeccion = $request->tutelado;

                $cambios = false;

                // Actualizar datos fijos del maestro
                $maestro->update([
                    'name'   => $request->name,
                    'genero' => $request->genero,
                ]);

                // Verificar cambios en sección o estado
                if ($historialActual->seccion_id != $nuevaSeccion ||
                    $historialActual->estado_id != $nuevoEstado->id) {

                    $cambios = true;
                    // Cerrar historial actual
                    $historialActual->fecha_fin = now()->subDay()->toDateString();
                    $historialActual->save();

                    // Crear nuevo historial
                    HistorialMaestro::create([
                        'maestro_id'    => $maestro->id,
                        'seccion_id'    => $nuevaSeccion,
                        'estado_id'     => $nuevoEstado->id,
                        'fecha_inicio'  => now()->toDateString(),
                        'fecha_fin'     => null,
                    ]);
                }

                // Si se cambió la sección, liberar la anterior (no es necesario hacer nada adicional)
            });
            return redirect()->route('maestros.index')->with('success', 'Maestro actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el maestro.');
        }
    }

    public function destroy(Maestro $maestro)
    {
        try {
            DB::transaction(function () use ($maestro) {
                $historialActual = $maestro->historialActivo;
                if ($historialActual && $historialActual->estado->permite_asistencia) {
                    // Desactivar: cerrar historial actual y crear uno con estado Inactivo
                    $historialActual->fecha_fin = now()->subDay()->toDateString();
                    $historialActual->save();

                    $estadoInactivo = Estado::where('nombre', 'Inactivo')->first();
                    HistorialMaestro::create([
                        'maestro_id'    => $maestro->id,
                        'seccion_id'    => $historialActual->seccion_id,
                        'estado_id'     => $estadoInactivo->id,
                        'fecha_inicio'  => now()->toDateString(),
                        'fecha_fin'     => null,
                    ]);
                }
            });
            return redirect()->route('maestros.index')->with('success', 'Maestro desactivado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al desactivar: ' . $e->getMessage());
            return redirect()->route('maestros.index')->with('error', 'No se pudo desactivar el maestro.');
        }
    }

    public function reactivar(Maestro $maestro)
    {
        try {
            DB::transaction(function () use ($maestro) {
                $historialActual = $maestro->historialActivo;
                if ($historialActual && $historialActual->estado->nombre == 'Inactivo') {
                    $historialActual->fecha_fin = now()->subDay()->toDateString();
                    $historialActual->save();

                    $estadoActivo = Estado::where('nombre', 'Activo')->first();
                    HistorialMaestro::create([
                        'maestro_id'    => $maestro->id,
                        'seccion_id'    => $historialActual->seccion_id,
                        'estado_id'     => $estadoActivo->id,
                        'fecha_inicio'  => now()->toDateString(),
                        'fecha_fin'     => null,
                    ]);
                }
            });
            return redirect()->route('maestros.index')->with('success', 'Maestro reactivado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al reactivar: ' . $e->getMessage());
            return redirect()->route('maestros.index')->with('error', 'Error al reactivar el maestro.');
        }
    }

    public function importForm()
    {
        $secciones = Seccion::orderBy('nombre')->get();
        return view('maestros.import', compact('secciones'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MaestrosImport, $request->file('archivo'));
            return redirect()->route('maestros.index')->with('success', 'Maestros importados correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al importar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}
