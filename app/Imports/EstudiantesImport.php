<?php

namespace App\Imports;

use App\Models\Estudiante;
use App\Models\Seccion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EstudiantesImport implements ToModel, WithHeadingRow, WithValidation
{
protected $seccionesAfectadas = [];

    public function model(array $row)
    {
        $seccion = Seccion::where('nombre', $row['seccion'])->first();
        if (!$seccion) {
            throw new \Exception("La sección '{$row['seccion']}' no existe.");
        }

        // Registrar sección afectada
        if (!in_array($seccion->id, $this->seccionesAfectadas)) {
            $this->seccionesAfectadas[] = $seccion->id;
        }

        return new Estudiante([
            'name'          => $row['nombre'],
            'numero_lista'  => $row['numero_lista'],
            'genero'        => $row['genero'],
            'año'           => $row['año'] ?? null,
            'id_seccion'    => $seccion->id,
            'estado'        => 'Activo',
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nombre'       => 'required|string',
            '*.seccion'      => 'required|string|exists:secciones,nombre',
            '*.numero_lista' => 'required|integer',
            '*.genero'       => 'required|in:M,F',
            '*.año'          => 'nullable|integer',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nombre.required'       => 'El nombre es obligatorio en la fila :row.',
            '*.seccion.required'      => 'La sección es obligatoria en la fila :row.',
            '*.seccion.exists'        => 'La sección :input no existe en la fila :row.',
            '*.numero_lista.required' => 'El número de lista es obligatorio en la fila :row.',
            '*.numero_lista.integer'  => 'El número de lista debe ser un número entero en la fila :row.',
            '*.genero.required'       => 'El género es obligatorio en la fila :row.',
            '*.genero.in'             => 'El género debe ser M o F en la fila :row.',
        ];
    }

    public function getSeccionesAfectadas()
    {
        return $this->seccionesAfectadas;
    }
}
