<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AusenciasExport implements FromArray, WithHeadings, WithStyles
{
    protected $resultados;
    protected $filtros;
    
    public function __construct($resultados, $filtros)
    {
        $this->resultados = $resultados;
        $this->filtros = $filtros;
    }
    
    public function array(): array
    {
        $data = [];
        
        // Cabecera de sección y filtros
        $data[] = ['REPORTE DE AUSENCIAS Y LLEGADAS TARDE'];
        $data[] = ['Sección', $this->resultados['seccion']->nombre ?? 'N/A'];
        if ($this->filtros['corte_id']) {
            $data[] = ['Corte', $this->resultados['corte']->nombre ?? 'N/A'];
        }
        if ($this->filtros['desde'] && $this->filtros['hasta']) {
            $data[] = ['Rango', $this->filtros['desde'] . ' al ' . $this->filtros['hasta']];
        }
        $data[] = []; // línea en blanco
        
        // Encabezados de la tabla de ausencias
        $headers = ['Nombre del Estudiante'];
        foreach ($this->resultados['meses'] as $mes) {
            $headers[] = $mes;
        }
        $headers[] = 'Ausencias Justificadas';
        $headers[] = 'Ausencias Injustificadas';
        $headers[] = 'Total de Ausencias';
        $data[] = $headers;
        
        // Datos de estudiantes
        foreach ($this->resultados['estudiantes'] as $est) {
            $row = [$est['nombre']];
            foreach ($this->resultados['meses'] as $mes) {
                $row[] = $est['detalle_mensual'][$mes] ?? '-';
            }
            $row[] = $est['ausencias_justificadas'];
            $row[] = $est['ausencias_injustificadas'];
            $row[] = $est['total_ausencias'];
            $data[] = $row;
        }
        
        $data[] = []; // línea en blanco
        $data[] = ['LLEGADAS TARDE'];
        $headersLT = ['Nombre'];
        foreach ($this->resultados['meses'] as $mes) {
            $headersLT[] = $mes;
        }
        $data[] = $headersLT;
        
        foreach ($this->resultados['llegadasTarde'] as $lt) {
            $row = [$lt['nombre']];
            foreach ($this->resultados['meses'] as $mes) {
                $row[] = $lt['detalle'][$mes] ?? '-';
            }
            $data[] = $row;
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        return [];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
