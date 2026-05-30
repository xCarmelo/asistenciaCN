<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Seccion;
use App\Imports\EstudiantesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EstudianteController extends Controller
{
    public function index(Request $request)
    {
        $query = Estudiante::with('seccion')
            ->join('secciones', 'estudiantes.id_seccion', '=', 'secciones.id')
            ->orderBy('secciones.nombre')
            ->orderBy('estudiantes.numero_lista')
            ->select('estudiantes.*');

        if ($request->filled('nombre')) {
            $query->where('estudiantes.name', 'like', '%' . $request->nombre . '%');
        }
        if ($request->filled('seccion_id')) {
            $query->where('estudiantes.id_seccion', $request->seccion_id);
        }
        if ($request->filled('estado')) {
            $query->where('estudiantes.estado', $request->estado);
        } else {
            $query->where('estudiantes.estado', 'Activo');
        }

        $estudiantes = $query->paginate(15)->appends($request->query());
        $secciones = Seccion::orderBy('nombre')->get();

        return view('estudiantes.index', compact('estudiantes', 'secciones'));
    }

/**
 * Store a newly created student in storage.
 */

public function store(Request $request)
{
    $request->validate([
        'name'          => 'required|string|max:100',
        'numero_lista'  => 'required|integer',
        'genero'        => 'required|in:M,F',
        'año'           => 'nullable|integer',
            'id_seccion'    => 'required|exists:secciones,id',
    ]);

    try {
        DB::transaction(function () use ($request) {
            $año = $request->año ?? date('Y');
            $estudiante = Estudiante::create([
                'name'          => $request->name,
                'numero_lista'  => $request->numero_lista,
                'genero'        => $request->genero,
                'año'           => $año,
                'id_seccion'    => $request->id_seccion,
                'estado'        => 'Activo',
            ]);

            // Reordenar usando la nueva función con los datos del conflicto
            if ($estudiante->id_seccion) {
                $this->renumberActiveStudents(
                    $estudiante->id_seccion,
                    $estudiante->id,
                    $request->numero_lista
                );
            }
        });

        return redirect()->route('estudiantes.index', request()->query())
            ->with('success', 'Estudiante creado exitosamente.');
    } catch (\Exception $e) {
        Log::error('Error al crear estudiante: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error al crear el estudiante.')->withInput();
    }
}

    public function update(Request $request, Estudiante $estudiante)
    {
        $request->validate([
            'id_seccion'    => 'required|exists:secciones,id',
            'numero_lista'  => 'required|integer|min:1',
            'name'          => 'required|string',
            'genero'        => 'required|string',
            'estado'        => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $oldSeccion = $estudiante->id_seccion;
            $oldNumero = $estudiante->numero_lista;
            $oldEstado = $estudiante->estado;
            $newSeccion = $request->id_seccion;
            $newNumero = $request->numero_lista;
            $newEstado = $request->estado;

            // Si cambia de sección o de inactivo a activo, liberar número anterior y reordenar
            if ($oldEstado === 'Activo' && ($oldSeccion != $newSeccion || $newEstado !== 'Activo')) {
                $estudiante->update($request->all());
                $this->renumberActiveStudents($oldSeccion);
            }

            // Si pasa a activo o cambia de sección/número
            if ($newEstado === 'Activo') {
                // Si cambia de sección, desplazar en la nueva sección
                if ($oldSeccion != $newSeccion) {
                    Estudiante::where('id_seccion', $newSeccion)
                        ->where('estado', 'Activo')
                        ->where('numero_lista', '>=', $newNumero)
                        ->increment('numero_lista');
                } else if ($oldNumero != $newNumero) {
                    // Si cambia de número en la misma sección
                    Estudiante::where('id_seccion', $newSeccion)
                        ->where('estado', 'Activo')
                        ->where('numero_lista', '>=', $newNumero)
                        ->where('id', '!=', $estudiante->id)
                        ->increment('numero_lista');
                }
                $estudiante->update($request->all());
                $this->renumberActiveStudents($newSeccion);
            } else {
                $estudiante->update($request->all());
            }

            DB::commit();
            return redirect()->route('estudiantes.index', request()->query())->with('success', 'Estudiante actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

public function destroy(Estudiante $estudiante)
{
    try {
        $seccionId = $estudiante->id_seccion;
        $estudiante->estado = 'Inactivo';
        $estudiante->save();

        // Reordenar los números de lista de los estudiantes activos de esa sección
        if ($seccionId) {
            $this->renumberActiveStudents($seccionId);
        }

        return redirect()->route('estudiantes.index', request()->query())
            ->with('success', 'Estudiante eliminado correctamente.');
    } catch (\Exception $e) {
        // ...
    }
}

    public function reactivar(Estudiante $estudiante)
    {
        DB::beginTransaction();
        try {
            $seccionId = $estudiante->id_seccion;
            $numero = $estudiante->numero_lista ?? 1;
            // Si el número está ocupado, desplazar
            if (Estudiante::where('id_seccion', $seccionId)->where('estado', 'Activo')->where('numero_lista', '>=', $numero)->exists()) {
                Estudiante::where('id_seccion', $seccionId)
                    ->where('estado', 'Activo')
                    ->where('numero_lista', '>=', $numero)
                    ->increment('numero_lista');
            }
            // Si no tenía número, asignar el siguiente disponible
            if (!$estudiante->numero_lista) {
                $max = Estudiante::where('id_seccion', $seccionId)->where('estado', 'Activo')->max('numero_lista');
                $estudiante->numero_lista = $max ? $max + 1 : 1;
            }
            $estudiante->estado = 'Activo';
            $estudiante->save();
            $this->renumberActiveStudents($seccionId);
            DB::commit();
            return redirect()->route('estudiantes.index', request()->query())->with('success', 'Estudiante reactivado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al reactivar: ' . $e->getMessage());
        }
    }

public function import(Request $request)
{
    $request->validate([
        'archivo' => 'required|mimes:xlsx,xls,csv'
    ]);

    try {
        $archivo = $request->file('archivo');
        $data = Excel::toArray([], $archivo);
        $rows = $data[0] ?? [];

        if (empty($rows)) {
            return redirect()->back()->with('error', 'El archivo está vacío.');
        }

        $headers = array_shift($rows); // Primera fila como cabeceras
        $indices = [];
        foreach ($headers as $i => $header) {
            $normalized = strtolower(trim($header));
            if ($normalized === 'nombre') $indices['nombre'] = $i;
            elseif ($normalized === 'seccion') $indices['seccion'] = $i;
            elseif ($normalized === 'numero_lista') $indices['numero_lista'] = $i;
            elseif ($normalized === 'genero') $indices['genero'] = $i;
            elseif ($normalized === 'año') $indices['año'] = $i;
        }

        $required = ['nombre', 'seccion', 'numero_lista', 'genero'];
        foreach ($required as $req) {
            if (!isset($indices[$req])) {
                return redirect()->back()->with('error', "La columna '{$req}' es obligatoria en el archivo.");
            }
        }

        DB::transaction(function () use ($rows, $indices) {
            foreach ($rows as $rowIndex => $row) {
                $nombre = trim($row[$indices['nombre']] ?? '');
                $seccionNombre = trim($row[$indices['seccion']] ?? '');
                $numeroLista = $row[$indices['numero_lista']] ?? null;
                $genero = strtoupper(trim($row[$indices['genero']] ?? ''));
                
                // Año: si la columna existe y tiene valor, usarlo; si no, año actual
                if (isset($indices['año']) && !empty($row[$indices['año']])) {
                    $año = $row[$indices['año']];
                } else {
                    $año = date('Y');
                }

                if (empty($nombre) || empty($seccionNombre) || empty($numeroLista) || empty($genero)) {
                    throw new \Exception("Fila " . ($rowIndex + 2) . ": Faltan campos obligatorios (nombre, sección, número lista, género).");
                }

                if (!in_array($genero, ['M', 'F'])) {
                    throw new \Exception("Fila " . ($rowIndex + 2) . ": Género debe ser M o F.");
                }

                $seccion = Seccion::where('nombre', $seccionNombre)->first();
                if (!$seccion) {
                    throw new \Exception("Fila " . ($rowIndex + 2) . ": La sección '{$seccionNombre}' no existe.");
                }

                // Crear el estudiante (activo)
                $estudiante = Estudiante::create([
                    'name'          => $nombre,
                    'numero_lista'  => $numeroLista,
                    'genero'        => $genero,
                    'año'           => $año,
                    'id_seccion'    => $seccion->id,
                    'estado'        => 'Activo',
                ]);

                // Reordenar números de lista en esa sección (con conflicto)
                $this->renumberActiveStudents($seccion->id, $estudiante->id, $numeroLista);
            }
        });

        return redirect()->route('estudiantes.index', request()->query())
            ->with('success', 'Estudiantes importados correctamente.');
    } catch (\Exception $e) {
        Log::error('Error al importar: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
    }
}

/**
 * Reordena los números de lista de estudiantes ACTIVOS en una sección,
 * respetando la prioridad del estudiante recién insertado/modificado.
 * Primero desplaza números si hay conflicto, luego asigna correlativos.
 *
 * @param int $seccionId
 * @param int|null $estudianteId ID del estudiante que se acaba de guardar (opcional)
 * @param int|null $nuevoNumero Número que se intentó asignar (opcional)
 */
private function renumberActiveStudents($seccionId, $estudianteId = null, $nuevoNumero = null)
{
    if (!$seccionId) return;

    // 1. Resolución de conflictos (si se crea/edita/reactiva un estudiante con un número ya existente)
    if ($estudianteId && $nuevoNumero !== null) {
        $conflicto = Estudiante::where('id_seccion', $seccionId)
            ->where('estado', 'Activo')
            ->where('numero_lista', $nuevoNumero)
            ->where('id', '!=', $estudianteId)
            ->exists();

        if ($conflicto) {
            // Incrementar en 1 los números >= $nuevoNumero (excluyendo al estudiante actual)
            Estudiante::where('id_seccion', $seccionId)
                ->where('estado', 'Activo')
                ->where('numero_lista', '>=', $nuevoNumero)
                ->where('id', '!=', $estudianteId)
                ->increment('numero_lista');
        }
    }

    // 2. Reasignación correlativa final (1,2,3...)
    $estudiantes = Estudiante::where('id_seccion', $seccionId)
        ->where('estado', 'Activo')
        ->orderBy('numero_lista', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    $numero = 1;
    foreach ($estudiantes as $est) {
        if ($est->numero_lista != $numero) {
            $est->numero_lista = $numero;
            $est->save();
        }
        $numero++;
    }
}
}
