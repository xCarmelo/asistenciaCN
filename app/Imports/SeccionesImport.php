<?php

namespace App\Imports;

use App\Models\Seccion;
use App\Models\Maestro;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SeccionesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[strtolower(trim($key))] = $value;
        }

        $nombre = $normalized['nombre'] ?? null;
        $maestroNombre = $normalized['maestro'] ?? $normalized['id_maestro_guia'] ?? null;

        $maestro = null;
        if ($maestroNombre) {
            $maestro = Maestro::where('name', $maestroNombre)->first();
            if (!$maestro) {
                throw new \Exception("El maestro '{$maestroNombre}' no existe en la fila.");
            }
            // Verificar si el maestro ya está ocupado en otra sección activa
            $ocupado = Seccion::where('estado', 1)
                ->where('id_maestro_guia', $maestro->id)
                ->exists();
            if ($ocupado) {
                throw new \Exception("El maestro '{$maestro->name}' ya está asignado a otra sección activa.");
            }
        }

        $existente = Seccion::where('nombre', $nombre)->first();
        if ($existente) {
            if ($existente->estado == 0) {
                $existente->estado = 1;
                $existente->id_maestro_guia = $maestro ? $maestro->id : null;
                $existente->save();
                return null;
            } else {
                throw new \Exception("La sección '{$nombre}' ya existe y está activa.");
            }
        }

        return new Seccion([
            'nombre'          => $nombre,
            'id_maestro_guia' => $maestro ? $maestro->id : null,
            'estado'          => 1,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nombre' => 'required|string',
            '*.maestro' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nombre.required' => 'El nombre de la sección es obligatorio en la fila :row.',
        ];
    }
}
