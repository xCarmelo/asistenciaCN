<?php

namespace App\Imports;

use App\Models\Maestro;
use App\Models\Seccion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class MaestrosImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[strtolower(trim($key))] = $value;
        }

        $nombre = $normalized['nombre'] ?? null;
        $genero = $normalized['genero'] ?? null;
        $tuteladoNombre = $normalized['tutelado'] ?? null;

        // Verificar que el maestro no exista ya (case-insensitive)
        if (Maestro::whereRaw('LOWER(name) = ?', [strtolower($nombre)])->exists()) {
            throw new \Exception("El maestro '{$nombre}' ya existe.");
        }

        // Buscar sección por nombre (solo si está libre)
        $seccion = null;
        if ($tuteladoNombre) {
            $seccion = Seccion::where('nombre', $tuteladoNombre)
                ->where('estado', 1)
                ->whereNull('id_maestro_guia')
                ->first();
            if (!$seccion) {
                throw new \Exception("La sección '{$tuteladoNombre}' no está disponible (no existe o ya tiene maestro guía).");
            }
        }

        $maestro = Maestro::create([
            'name'   => $nombre,
            'genero' => $genero,
            'estado' => 1,
        ]);

        if ($seccion) {
            $maestro->seccionesGuiadas()->save($seccion);
        }

        return $maestro;
    }

    public function rules(): array
    {
        return [
            '*.nombre'   => 'required|string',
            '*.genero'   => 'required|in:M,F',
            '*.tutelado' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nombre.required' => 'El nombre es obligatorio en la fila :row.',
            '*.genero.required' => 'El género es obligatorio en la fila :row.',
            '*.genero.in'       => 'El género debe ser M o F en la fila :row.',
        ];
    }
}
