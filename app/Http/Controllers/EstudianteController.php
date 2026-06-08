<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Seccion;
use App\Models\Estado;
use App\Models\HistorialEstudiante;
use App\Imports\EstudiantesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EstudianteController extends Controller
{
public function index(Request $request)
{
    // Subconsulta para obtener el ID del último historial de cada estudiante
    $subquery = HistorialEstudiante::select('estudiante_id', DB::raw('MAX(id) as ultimo_id'))
        ->groupBy('estudiante_id');

    if ($request->filled('nombre')) {
        $subquery->whereHas('estudiante', fn($q) => $q->where('name', 'like', '%' . $request->nombre . '%'));
    }
    if ($request->filled('seccion_id')) {
        $subquery->where('seccion_id', $request->seccion_id);
    }

    $subquerySql = $subquery->toSql();
    $bindings = $subquery->getBindings();

    $query = HistorialEstudiante::with(['estudiante', 'seccion', 'estado'])
        ->join(DB::raw("({$subquerySql}) as ultimos"), function($join) {
            $join->on('historial_estudiantes.id', '=', 'ultimos.ultimo_id');
        })
        ->setBindings($bindings, 'join');

    if ($request->filled('estado')) {
        $query->where('historial_estudiantes.estado_id', $request->estado);
    } else {
        // Sin filtro: solo activos (fecha_fin NULL y estado permite asistencia)
        $query->whereNull('historial_estudiantes.fecha_fin')
              ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true));
    }

    // Obtener el número de registros por página (por defecto 15)
    $perPage = (int) $request->input('per_page', 15);
    $perPage = in_array($perPage, [15, 25, 50, 100]) ? $perPage : 15;

    $historiales = $query
        ->join('secciones', 'historial_estudiantes.seccion_id', '=', 'secciones.id')
        ->select('historial_estudiantes.*')
        ->orderBy('secciones.nombre', 'asc')
        ->orderBy('historial_estudiantes.numero_lista', 'asc')
        ->orderBy('historial_estudiantes.id', 'asc')
        ->paginate($perPage);

    $secciones = Seccion::orderBy('nombre')->get();
    $estados = Estado::all();

    return view('estudiantes.index', compact('historiales', 'secciones', 'estados'));
}

/**
 * Actualización masiva de estudiantes (cambio de sección o estado)
 */
public function bulkUpdate(Request $request)
{
    $estudiantes = $request->estudiantes;
    if (is_string($estudiantes)) {
        $estudiantes = json_decode($estudiantes, true);
        $request->merge(['estudiantes' => $estudiantes]);
    }

    $request->validate([
        'action'       => 'required|in:seccion,estado',
        'estudiantes'  => 'required|array|min:1',
        'estudiantes.*' => 'exists:estudiantes,id',
    ]);

    if ($request->action == 'seccion') {
        $request->validate(['seccion_id' => 'required|exists:secciones,id']);
        $nuevaSeccionGlobal = $request->seccion_id;
        $nuevoEstadoGlobal = null;
    } else {
        $request->validate(['estado_id' => 'required|exists:estados,id']);
        $nuevoEstadoGlobal = $request->estado_id;
        $nuevaSeccionGlobal = null;
    }

    $estudianteIds = $request->estudiantes;
    $action = $request->action;
    $procesados = 0;
    $seccionesReconstruir = collect();

    DB::beginTransaction();
    try {
        // Clasificar estudiantes: reactivaciones (inactivo -> activo) vs otros
        $reactivacionesPorSeccion = []; // [seccion_id => [['estudiante_id', 'numero_deseado']]]
        $otrosProcesos = [];

        foreach ($estudianteIds as $estudianteId) {
            $estudiante = Estudiante::findOrFail($estudianteId);

            $historialActivo = HistorialEstudiante::where('estudiante_id', $estudianteId)
                ->whereNull('fecha_fin')
                ->first();

            if (!$historialActivo) {
                $ultimo = HistorialEstudiante::where('estudiante_id', $estudianteId)
                    ->orderBy('id', 'desc')
                    ->first();
                if (!$ultimo) continue;

                $seccionActual = $ultimo->seccion_id;
                $estadoActual  = $ultimo->estado_id;
                $numeroActual  = $ultimo->numero_lista;
                $cerrar = false;
            } else {
                $seccionActual = $historialActivo->seccion_id;
                $estadoActual  = $historialActivo->estado_id;
                $numeroActual  = $historialActivo->numero_lista;
                $cerrar = true;
            }

            $nuevaSeccion = ($action == 'seccion') ? $nuevaSeccionGlobal : $seccionActual;
            $nuevoEstado  = ($action == 'estado') ? $nuevoEstadoGlobal : $estadoActual;

            if ($seccionActual == $nuevaSeccion && $estadoActual == $nuevoEstado) continue;
            if ($action == 'estado' && $estadoActual == $nuevoEstado) continue;
            if ($action == 'seccion' && $seccionActual == $nuevaSeccion) continue;

            $nuevoEstadoObj = Estado::find($nuevoEstado);
            $esReactivacion = (!$cerrar && $nuevoEstadoObj && $nuevoEstadoObj->permite_asistencia);

            if ($esReactivacion) {
                $reactivacionesPorSeccion[$nuevaSeccion][] = [
                    'estudiante_id' => $estudiante->id,
                    'numero_deseado' => $numeroActual,
                ];
            } else {
                $otrosProcesos[] = [
                    'estudiante_id' => $estudiante->id,
                    'historial_activo' => $historialActivo,
                    'seccion_actual' => $seccionActual,
                    'estado_actual' => $estadoActual,
                    'numero_actual' => $numeroActual,
                    'cerrar' => $cerrar,
                    'nueva_seccion' => $nuevaSeccion,
                    'nuevo_estado' => $nuevoEstado,
                ];
            }
        }

        // Procesar cambios de sección o cambios a estado no activo
        foreach ($otrosProcesos as $proc) {
            if ($proc['cerrar']) {
                $proc['historial_activo']->fecha_fin = Carbon::now()->toDateString();
                $proc['historial_activo']->save();
                if (Estado::find($proc['estado_actual'])->permite_asistencia) {
                    $seccionesReconstruir->push($proc['seccion_actual']);
                }
            }

            $nuevoEstadoObj = Estado::find($proc['nuevo_estado']);
            if ($nuevoEstadoObj && $nuevoEstadoObj->permite_asistencia) {
                $this->shiftNumbersForInsert($proc['nueva_seccion'], $proc['numero_actual'], null);
                $seccionesReconstruir->push($proc['nueva_seccion']);
            } elseif ($action == 'seccion') {
                $seccionesReconstruir->push($proc['nueva_seccion']);
            }

            HistorialEstudiante::create([
                'estudiante_id' => $proc['estudiante_id'],
                'seccion_id'    => $proc['nueva_seccion'],
                'estado_id'     => $proc['nuevo_estado'],
                'numero_lista'  => $proc['numero_actual'],
                'fecha_inicio'  => Carbon::now()->toDateString(),
                'fecha_fin'     => null,
            ]);
            $procesados++;
        }

        // Procesar reactivaciones: orden descendente por número deseado
        foreach ($reactivacionesPorSeccion as $seccionId => $reactivos) {
            // Ordenar de mayor a menor
            usort($reactivos, fn($a, $b) => $b['numero_deseado'] <=> $a['numero_deseado']);

            foreach ($reactivos as $r) {
                $num = $r['numero_deseado'];
                // Desplazar números existentes >= num
                HistorialEstudiante::where('seccion_id', $seccionId)
                    ->whereNull('fecha_fin')
                    ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
                    ->where('numero_lista', '>=', $num)
                    ->increment('numero_lista');

                HistorialEstudiante::create([
                    'estudiante_id' => $r['estudiante_id'],
                    'seccion_id'    => $seccionId,
                    'estado_id'     => $nuevoEstadoGlobal,
                    'numero_lista'  => $num,
                    'fecha_inicio'  => Carbon::now()->toDateString(),
                    'fecha_fin'     => null,
                ]);
                $procesados++;
            }
            // No reconstruir la sección destino para mantener los números originales
        }

        // Reconstruir secciones afectadas por otros procesos
        foreach ($seccionesReconstruir->unique() as $seccionId) {
            $this->rebuildListNumbers($seccionId);
        }

        DB::commit();

        if ($procesados == 0) {
            return redirect()->route('estudiantes.index')
                ->with('info', 'No se realizaron cambios porque los estudiantes ya estaban en el estado/sección seleccionado.');
        }

        $message = $action == 'seccion'
            ? "Se cambiaron $procesados estudiante(s) de sección correctamente."
            : "Se cambiaron $procesados estudiante(s) de estado correctamente.";

        return redirect()->route('estudiantes.index', ['clear_selection' => 1])
            ->with('success', $message);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error en actualización masiva: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error al procesar la operación masiva: ' . $e->getMessage());
    }
}
    public function create()
    {
        $secciones = Seccion::orderBy('nombre')->get();
        return view('estudiantes.create', compact('secciones'));
    }

        public function show($id)
        {
            $historial = HistorialEstudiante::with(['estudiante', 'seccion', 'estado'])
                ->where('estudiante_id', $id)
                ->whereNull('fecha_fin')
                ->firstOrFail();
            return view('estudiantes.show', compact('historial'));
        }

        public function edit($id)
        {
            $estudiante = Estudiante::findOrFail($id);
            $historialActual = HistorialEstudiante::where('estudiante_id', $id)
                ->latest('id')
                ->firstOrFail();
            $secciones = Seccion::orderBy('nombre')->get();
            $estados = Estado::all();
            return view('estudiantes.edit', compact('estudiante', 'historialActual', 'secciones', 'estados'));
        }

    /**
     * Guardar un nuevo estudiante (primer historial).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'genero'       => 'required|in:M,F',
            'id_seccion'   => 'required|exists:secciones,id',
            'numero_lista' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $estudiante = Estudiante::create([
                'name'   => $request->name,
                'genero' => $request->genero,
            ]);

            $estadoActivo = Estado::where('nombre', 'Activo')->first();
            if (!$estadoActivo) {
                throw new \Exception('No se encuentra el estado Activo');
            }

            // Hacer espacio para el número deseado en la sección
            $this->shiftNumbersForInsert($request->id_seccion, $request->numero_lista, null);

            // Crear historial inicial
            HistorialEstudiante::create([
                'estudiante_id' => $estudiante->id,
                'seccion_id'    => $request->id_seccion,
                'estado_id'     => $estadoActivo->id,
                'numero_lista'  => $request->numero_lista,
                'fecha_inicio'  => Carbon::now()->toDateString(),
                'fecha_fin'     => null,
            ]);
            $this->rebuildListNumbers($request->id_seccion);

            DB::commit();

            
            return redirect()->route('estudiantes.index')->with('success', 'Estudiante creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear estudiante: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Actualizar estudiante (nombre, género, número o sección).
     */
public function update(Request $request, $id)
{
    $request->validate([
        'name'         => 'required|string|max:100',
        'genero'       => 'required|in:M,F',
        'id_seccion'   => 'required|exists:secciones,id',
        'numero_lista' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        $estudiante = Estudiante::findOrFail($id);
        $estudiante->update([
            'name'   => $request->name,
            'genero' => $request->genero,
        ]);

        $historialActivo = HistorialEstudiante::where('estudiante_id', $id)
            ->whereNull('fecha_fin')
            ->first();

        if (!$historialActivo) {
            DB::commit();
            return redirect()
                ->route('estudiantes.index')
                ->with('warning', 'Solo se actualizó nombre y género. El estudiante no está activo.');
        }

        $seccionActual = $historialActivo->seccion_id;
        $numeroActual  = $historialActivo->numero_lista;
        $nuevaSeccion  = (int) $request->id_seccion;
        $nuevoNumero   = (int) $request->numero_lista;

        // Si cambia de sección o cambia el número, se cierra el historial actual y se crea uno nuevo
        if ($seccionActual != $nuevaSeccion || $numeroActual != $nuevoNumero) {
            // Cerrar historial actual
            $historialActivo->fecha_fin = now()->toDateString();
            $historialActivo->save();

            // Reordenar la sección antigua (solo si el estado actual permitía asistencia)
            $estadoActualObj = Estado::find($historialActivo->estado_id);
            if ($estadoActualObj && $estadoActualObj->permite_asistencia) {
                $this->rebuildListNumbers($seccionActual);
            }

            // Determinar la nueva sección y nuevo número
            $nuevaSeccionDestino = ($seccionActual != $nuevaSeccion) ? $nuevaSeccion : $seccionActual;
            $nuevoNumeroDestino = $nuevoNumero;

            // Si se cambia de sección, hacer espacio en la nueva sección
            if ($seccionActual != $nuevaSeccion) {
                $this->shiftNumbersForInsert($nuevaSeccionDestino, $nuevoNumeroDestino, null);
            } else {
                // Misma sección: hacer espacio excluyendo al estudiante actual (que ya no tiene historial activo)
                $this->shiftNumbersForInsert($seccionActual, $nuevoNumeroDestino, null);
            }

            // Crear nuevo historial
            HistorialEstudiante::create([
                'estudiante_id' => $estudiante->id,
                'seccion_id'    => $nuevaSeccionDestino,
                'estado_id'     => $historialActivo->estado_id,
                'numero_lista'  => $nuevoNumeroDestino,
                'fecha_inicio'  => now()->toDateString(),
                'fecha_fin'     => null,
            ]);

            // Reordenar la nueva sección (para asegurar secuencia 1,2,3...)
            $this->rebuildListNumbers($nuevaSeccionDestino);
        }

        DB::commit();
        return redirect()
            ->route('estudiantes.index')
            ->with('success', 'Estudiante actualizado correctamente.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error($e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Error al actualizar: ' . $e->getMessage());
    }
}

    /**
     * Desactivar estudiante (cambio de estado). Solo cierra historial y reordena.
     */
    public function destroy($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $historialActivo = HistorialEstudiante::where('estudiante_id', $id)
                ->whereNull('fecha_fin')
                ->first();

            if (!$historialActivo) {
                return redirect()->back()->with('error', 'El estudiante no tiene un historial activo.');
            }

            $estadoId = $request->input('estado_id');
            if (!$estadoId) {
                return redirect()->back()->with('error', 'Debe seleccionar un motivo de desactivación.');
            }

            $estado = Estado::where('id', $estadoId)->where('permite_asistencia', false)->first();
            if (!$estado) {
                return redirect()->back()->with('error', 'El estado seleccionado no es válido para desactivación.');
            }

            $seccionId = $historialActivo->seccion_id;
            $historialActivo->update([
                'estado_id' => $estado->id,
                'fecha_fin' => Carbon::now()->toDateString(),
            ]);

            $this->rebuildListNumbers($seccionId); // reordenar para eliminar hueco

            DB::commit();
            return redirect()->route('estudiantes.index')->with('success', "Estudiante marcado como {$estado->nombre} correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al desactivar estudiante: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al desactivar: ' . $e->getMessage());
        }
    }

    /**
     * Reactivar / cambiar estado (a uno que permita o no asistencia).
     * Al reactivar a un estado que permite asistencia, se debe asignar un número de lista.
     */
/**
 * Reactivar / cambiar estado (a uno que permita o no asistencia).
 * Al reactivar a un estado que permite asistencia, se reutiliza el número de lista
 * del último historial activo que tuvo el estudiante.
 */
public function reactivar($id, Request $request)
{
    $request->validate([
        'estado_id' => 'required|exists:estados,id',
    ]);

    DB::beginTransaction();
    try {
        $estudiante = Estudiante::findOrFail($id);
        $nuevoEstado = Estado::findOrFail($request->estado_id);

        if ($nuevoEstado->nombre == 'Inactivo') {
            return redirect()->back()->with('error', 'No se puede cambiar al estado Inactivo desde este modal.');
        }

        // Obtener el último historial del estudiante (el más reciente, activo o inactivo)
        $ultimoHistorial = HistorialEstudiante::where('estudiante_id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if (!$ultimoHistorial) {
            return redirect()->back()->with('error', 'No se encontró un historial previo.');
        }

        // Cerrar el historial actual si existe (por si el estudiante estaba en un estado inactivo pero con fecha_fin NULL)
        $historialActual = HistorialEstudiante::where('estudiante_id', $id)
            ->whereNull('fecha_fin')
            ->first();
        if ($historialActual) {
            $historialActual->update(['fecha_fin' => Carbon::now()->toDateString()]);
            // Si el estado actual permitía asistencia, reordenar su sección (eliminar su hueco)
            if ($historialActual->estado->permite_asistencia) {
                $this->rebuildListNumbers($historialActual->seccion_id);
            }
        }

        // Preparar los datos para el nuevo historial
        $nuevaSeccion = $ultimoHistorial->seccion_id;
        $nuevoNumero = $ultimoHistorial->numero_lista;

        // Si el nuevo estado permite asistencia, hacer espacio para el número en la sección
        if ($nuevoEstado->permite_asistencia) {
            // Desplazar los números >= $nuevoNumero para hacer espacio
            // No excluimos ningún historial porque el estudiante aún no tiene historial activo en esta sección
            $this->shiftNumbersForInsert($nuevaSeccion, $nuevoNumero, null);
        }

        // Crear el nuevo historial
        HistorialEstudiante::create([
            'estudiante_id' => $estudiante->id,
            'seccion_id'    => $nuevaSeccion,
            'estado_id'     => $nuevoEstado->id,
            'numero_lista'  => $nuevoNumero,
            'fecha_inicio'  => Carbon::now()->toDateString(),
            'fecha_fin'     => null,
        ]);

        DB::commit();
        return redirect()->route('estudiantes.index')->with('success', "Estudiante cambiado a estado {$nuevoEstado->nombre} correctamente.");
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al cambiar estado: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
    }
}
    /**
     * Importar estudiantes desde Excel.
     * Se espera que el importador devuelva las secciones afectadas y, por cada fila,
     * se debe hacer desplazamiento antes de insertar el historial.
     * Para simplificar, se puede modificar el Import para que haga el desplazamiento,
     * o hacerlo aquí tras recoger todos los datos. Como el importador actual no tiene esa lógica,
     * sugiero reescribir la importación en el controlador mismo para controlar el desplazamiento.
     * Por simplicidad, mantendré la llamada al import y luego reordenaré todo, pero eso no respeta
     * los números deseados. El usuario pide que se respete, así que reemplazaré el método import.
     */
    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv'
        ]);

        DB::beginTransaction();
        try {
            $data = Excel::toArray([], $request->file('archivo'));
            $rows = $data[0] ?? [];
            if (empty($rows)) {
                throw new \Exception('El archivo está vacío.');
            }

            $headers = array_shift($rows);
            $indices = [];
            foreach ($headers as $i => $header) {
                $normalized = strtolower(trim($header));
                if ($normalized === 'nombre') $indices['nombre'] = $i;
                elseif ($normalized === 'seccion') $indices['seccion'] = $i;
                elseif ($normalized === 'numero_lista') $indices['numero_lista'] = $i;
                elseif ($normalized === 'genero') $indices['genero'] = $i;
            }
            $required = ['nombre', 'seccion', 'numero_lista', 'genero'];
            foreach ($required as $req) {
                if (!isset($indices[$req])) {
                    throw new \Exception("La columna '{$req}' es obligatoria.");
                }
            }

            $estadoActivo = Estado::where('nombre', 'Activo')->first();
            if (!$estadoActivo) {
                throw new \Exception('Estado Activo no encontrado');
            }

            foreach ($rows as $rowIndex => $row) {
                $nombre = trim($row[$indices['nombre']] ?? '');
                $seccionNombre = trim($row[$indices['seccion']] ?? '');
                $numeroLista = (int)($row[$indices['numero_lista']] ?? 0);
                $genero = strtoupper(trim($row[$indices['genero']] ?? ''));

                if (empty($nombre) || empty($seccionNombre) || $numeroLista <= 0 || empty($genero)) {
                    throw new \Exception("Fila " . ($rowIndex + 2) . ": Datos incompletos.");
                }
                if (!in_array($genero, ['M', 'F'])) {
                    throw new \Exception("Fila " . ($rowIndex + 2) . ": Género inválido.");
                }

                $seccion = Seccion::where('nombre', $seccionNombre)->first();
                if (!$seccion) {
                    throw new \Exception("Fila " . ($rowIndex + 2) . ": Sección '{$seccionNombre}' no existe.");
                }

                // Crear o recuperar estudiante (por nombre, evitar duplicados exactos)
                $estudiante = Estudiante::firstOrCreate(
                    ['name' => $nombre],
                    ['genero' => $genero]
                );
                if (empty($estudiante->genero)) {
                    $estudiante->genero = $genero;
                    $estudiante->save();
                }

                // Cerrar historial activo actual si existe y está en diferente sección/estado? Para importación,
                // asumimos que cada fila es un nuevo estudiante o una reactivación. Para simplificar,
                // cerramos cualquier historial activo del estudiante (si existe) y creamos uno nuevo.
                $historialActivo = HistorialEstudiante::where('estudiante_id', $estudiante->id)
                    ->whereNull('fecha_fin')
                    ->first();
                if ($historialActivo) {
                    $historialActivo->update(['fecha_fin' => Carbon::now()->toDateString()]);
                    $this->rebuildListNumbers($historialActivo->seccion_id);
                }

                // Hacer espacio para el número deseado en la sección
                $this->shiftNumbersForInsert($seccion->id, $numeroLista, null);

                // Crear nuevo historial activo
                HistorialEstudiante::create([
                    'estudiante_id' => $estudiante->id,
                    'seccion_id'    => $seccion->id,
                    'estado_id'     => $estadoActivo->id,
                    'numero_lista'  => $numeroLista,
                    'fecha_inicio'  => Carbon::now()->toDateString(),
                    'fecha_fin'     => null,
                ]);
            }

            DB::commit();
            return redirect()->route('estudiantes.index')->with('success', 'Estudiantes importados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al importar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    /**
     * Desplaza hacia arriba los números de lista en una sección para hacer espacio
     * para un nuevo número deseado, excluyendo opcionalmente un historial (para ediciones).
     * Incrementa en 1 todos los números >= $numeroDeseado que no sean el excluido.
     */
    private function shiftNumbersForInsert($seccionId, $numeroDeseado, $excluirHistorialId = null)
    {
        if (!$seccionId || $numeroDeseado <= 0) return;

        $query = HistorialEstudiante::where('seccion_id', $seccionId)
            ->whereNull('fecha_fin')
            ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
            ->where('numero_lista', '>=', $numeroDeseado);

        if ($excluirHistorialId) {
            $query->where('id', '!=', $excluirHistorialId);
        }

        $query->increment('numero_lista');
    }

    /**
     * Reconstruye números de lista de estudiantes ACTIVOS en una sección (1,2,3...).
     * Solo usado cuando se elimina un estudiante o se cambia de sección (para llenar huecos).
     */
    private function rebuildListNumbers($seccionId)
    {
        if (!$seccionId) return;

        $historiales = HistorialEstudiante::where('seccion_id', $seccionId)
            ->whereNull('fecha_fin')
            ->whereHas('estado', fn($q) => $q->where('permite_asistencia', true))
            ->orderBy('numero_lista')
            ->orderBy('id')
            ->get();

        $numero = 1;
        foreach ($historiales as $historial) {
            if ($historial->numero_lista != $numero) {
                $historial->update(['numero_lista' => $numero]);
            }
            $numero++;
        }
    }
}