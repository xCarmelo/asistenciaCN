<?php

namespace App\Imports;

use App\Models\Estudiante;
use App\Models\HistorialEstudiante;
use App\Models\Seccion;
use App\Models\Estado;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class EstudiantesImport implements ToCollection, WithHeadingRow
{
    private $seccionesAfectadas = [];

    public function collection(Collection $rows)
    {
        $estadoActivo = Estado::where('nombre', 'Activo')->first();
        if (!$estadoActivo) {
            throw new \Exception('No se encontró el estado "Activo" en la tabla estados.');
        }

        foreach ($rows as $row) {
            if (empty($row['nombre']) || empty($row['seccion']) || empty($row['numero_lista']) || empty($row['genero'])) {
                continue;
            }

            $seccion = Seccion::where('nombre', trim($row['seccion']))->first();
            if (!$seccion) {
                throw new \Exception('Sección "' . $row['seccion'] . '" no existe.');
            }

            // Crear NUEVO estudiante (permitir homónimos)
            $estudiante = Estudiante::create([
                'name'   => trim($row['nombre']),
                'genero' => strtoupper(trim($row['genero'])),
            ]);

            // Crear historial activo con número inicial (será reordenado después)
            HistorialEstudiante::create([
                'estudiante_id' => $estudiante->id,
                'seccion_id'    => $seccion->id,
                'estado_id'     => $estadoActivo->id,
                'numero_lista'  => (int)$row['numero_lista'],
                'fecha_inicio'  => Carbon::now()->toDateString(),
                'fecha_fin'     => null,
            ]);

            // Registrar sección afectada para reordenar después
            if (!in_array($seccion->id, $this->seccionesAfectadas)) {
                $this->seccionesAfectadas[] = $seccion->id;
            }
        }
    }

    public function getSeccionesAfectadas()
    {
        return $this->seccionesAfectadas;
    }
}