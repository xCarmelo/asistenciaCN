<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use App\Models\Corte;
use App\Models\Asistencia;
use App\Models\TipoAsistencia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AusenciasExport;

class ReporteAusenciasController extends Controller
{
    public function index(Request $request)
    {
        $secciones = Seccion::where('estado', 1)->orderBy('nombre')->get();
        $cortes = Corte::orderBy('id')->get();
        
        // Obtener años dinámicos desde asistencias de estudiantes
        $anios = Asistencia::selectRaw('DISTINCT YEAR(fecha) as año')
            ->orderBy('año', 'desc')
            ->pluck('año')
            ->toArray();
        
        // Asegurar que haya al menos el año actual si no hay datos
        if (empty($anios)) {
            $anios = [date('Y')];
        }

        $filtros = [
            'seccion_id' => $request->get('seccion_id'),
            'corte_id'   => $request->get('corte_id'),
            'anio'       => $request->get('anio'),
            'desde'      => $request->get('desde'),
            'hasta'      => $request->get('hasta'),
        ];

        // Validación: si hay corte, debe haber año (a menos que haya rango)
        if ($filtros['corte_id'] && empty($filtros['anio']) && (empty($filtros['desde']) || empty($filtros['hasta']))) {
            return redirect()->back()->with('error', 'Al seleccionar un corte, debe seleccionar también un año (a menos que use rango de fechas).');
        }

        $resultados = null;
        if ($filtros['seccion_id'] && ($filtros['corte_id'] || ($filtros['desde'] && $filtros['hasta']))) {
            $resultados = $this->consultarAsistencias($filtros);
        }

        return view('reporte-ausencias', compact('secciones', 'cortes', 'anios', 'filtros', 'resultados'));
    }

    private function consultarAsistencias($filtros)
    {
        $seccionId = $filtros['seccion_id'];

        $query = Asistencia::with(['estudiante', 'tipoAsistencia'])
            ->whereHas('estudiante', function ($q) use ($seccionId) {
                $q->where('id_seccion', $seccionId);
            });

        if (!empty($filtros['corte_id'])) {
            $query->where('id_corte', $filtros['corte_id']);
        }

        if (!empty($filtros['anio']) && empty($filtros['desde']) && empty($filtros['hasta'])) {
            $query->whereYear('fecha', $filtros['anio']);
        }

        if (!empty($filtros['desde']) && !empty($filtros['hasta'])) {
            $query->whereBetween('fecha', [$filtros['desde'], $filtros['hasta']]);
        }

        $asistencias = $query->get();

        if ($asistencias->isEmpty()) {
            return [
                'estudiantes'    => [],
                'llegadasTarde'  => [],
                'meses'          => [],
                'seccion'        => Seccion::find($seccionId),
                'corte'          => $filtros['corte_id'] ? Corte::find($filtros['corte_id']) : null,
            ];
        }

        // Obtener meses únicos ordenados cronológicamente (en español)
        $meses = $asistencias->map(fn($a) => Carbon::parse($a->fecha)->locale('es')->translatedFormat('F'))
            ->unique()
            ->values()
            ->sortBy(function ($mes) use ($asistencias) {
                $fechaPrimera = $asistencias->first(fn($a) => Carbon::parse($a->fecha)->locale('es')->translatedFormat('F') === $mes);
                return $fechaPrimera ? Carbon::parse($fechaPrimera->fecha) : now();
            })
            ->values()
            ->toArray();

        $estudiantesData = [];
        $llegadasTardeData = [];

        foreach ($asistencias as $asis) {
            $est = $asis->estudiante;
            if (!$est) continue;
            $nombre = $est->name;
            $tipo = $asis->tipoAsistencia;
            if (!$tipo) continue;

            $mes = Carbon::parse($asis->fecha)->locale('es')->translatedFormat('F');
            $codigo = $tipo->codigo;

            if ($codigo === 'A' || $codigo === 'J') {
                if (!isset($estudiantesData[$nombre])) {
                    $estudiantesData[$nombre] = [
                        'nombre'                   => $nombre,
                        'ausencias_justificadas'   => 0,
                        'ausencias_injustificadas' => 0,
                        'detalle_mensual'          => [],
                    ];
                }
                if ($codigo === 'A') {
                    $estudiantesData[$nombre]['ausencias_injustificadas']++;
                } else {
                    $estudiantesData[$nombre]['ausencias_justificadas']++;
                }
                $estudiantesData[$nombre]['detalle_mensual'][$mes] = ($estudiantesData[$nombre]['detalle_mensual'][$mes] ?? 0) + 1;
            } elseif ($codigo === 'T') {
                if (!isset($llegadasTardeData[$nombre])) {
                    $llegadasTardeData[$nombre] = [
                        'nombre'  => $nombre,
                        'detalle' => [],
                        'total'   => 0,
                    ];
                }
                $llegadasTardeData[$nombre]['detalle'][$mes] = ($llegadasTardeData[$nombre]['detalle'][$mes] ?? 0) + 1;
                $llegadasTardeData[$nombre]['total']++;
            }
        }

        $estudiantes = [];
        foreach ($estudiantesData as $data) {
            $total = $data['ausencias_justificadas'] + $data['ausencias_injustificadas'];
            if ($total > 0) {
                $data['total_ausencias'] = $total;
                $estudiantes[] = $data;
            }
        }

        usort($estudiantes, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
        $llegadasTarde = array_values($llegadasTardeData);
        usort($llegadasTarde, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));

        return [
            'estudiantes'    => $estudiantes,
            'llegadasTarde'  => $llegadasTarde,
            'meses'          => $meses,
            'seccion'        => Seccion::find($seccionId),
            'corte'          => $filtros['corte_id'] ? Corte::find($filtros['corte_id']) : null,
        ];
    }

    public function generarPDF(Request $request)
    {
        try {
            $filtros = $request->all();
            $filtros = array_merge([
                'seccion_id' => null,
                'corte_id'   => null,
                'anio'       => null,
                'desde'      => null,
                'hasta'      => null,
            ], $filtros);
            if (empty($filtros['seccion_id']) || (empty($filtros['corte_id']) && (empty($filtros['desde']) || empty($filtros['hasta'])))) {
                return redirect()->back()->with('error', 'Seleccione filtros válidos (sección y corte o rango de fechas).');
            }
            $resultados = $this->consultarAsistencias($filtros);
            if (empty($resultados['estudiantes']) && empty($resultados['llegadasTarde'])) {
                return redirect()->back()->with('error', 'No hay datos para exportar con los filtros seleccionados.');
            }
            $pdf = Pdf::loadView('pdf.ausencias', [
                'resultados'        => $resultados,
                'filtros'           => $filtros,
                'fecha_generacion'  => now(),
            ]);
            $pdf->setPaper('a4', 'portrait');
            return $pdf->download('reporte_ausencias.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    public function exportarExcel(Request $request)
    {
        try {
            $filtros = $request->all();
            $filtros = array_merge([
                'seccion_id' => null,
                'corte_id'   => null,
                'anio'       => null,
                'desde'      => null,
                'hasta'      => null,
            ], $filtros);
            if (empty($filtros['seccion_id']) || (empty($filtros['corte_id']) && (empty($filtros['desde']) || empty($filtros['hasta'])))) {
                return redirect()->back()->with('error', 'Seleccione filtros válidos (sección y corte o rango de fechas).');
            }
            $resultados = $this->consultarAsistencias($filtros);
            if (empty($resultados['estudiantes']) && empty($resultados['llegadasTarde'])) {
                return redirect()->back()->with('error', 'No hay datos para exportar con los filtros seleccionados.');
            }
            return Excel::download(new AusenciasExport($resultados, $filtros), 'reporte_ausencias.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al exportar Excel: ' . $e->getMessage());
        }
    }

    public function exportarWord(Request $request)
    {
        try {
            $filtros = $request->all();
            $filtros = array_merge([
                'seccion_id' => null,
                'corte_id'   => null,
                'anio'       => null,
                'desde'      => null,
                'hasta'      => null,
            ], $filtros);
            if (empty($filtros['seccion_id']) || (empty($filtros['corte_id']) && (empty($filtros['desde']) || empty($filtros['hasta'])))) {
                return redirect()->back()->with('error', 'Seleccione filtros válidos (sección y corte o rango de fechas).');
            }
            $resultados = $this->consultarAsistencias($filtros);
            if (empty($resultados['estudiantes']) && empty($resultados['llegadasTarde'])) {
                return redirect()->back()->with('error', 'No hay datos para exportar con los filtros seleccionados.');
            }

            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            $section->addTitle('Reporte de Ausencias y Llegadas Tarde', 1);
            $section->addText("Sección: {$resultados['seccion']->nombre}");
            if ($resultados['corte']) $section->addText("Corte: {$resultados['corte']->nombre}");
            if ($filtros['anio']) $section->addText("Año: {$filtros['anio']}");
            if ($filtros['desde'] && $filtros['hasta']) $section->addText("Rango: {$filtros['desde']} al {$filtros['hasta']}");
            $section->addText("Fecha generación: " . now()->format('d/m/Y H:i'));
            $section->addTextBreak(1);

            // Tabla de ausencias
            $section->addTitle('Ausencias', 2);
            $tableStyle = ['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80];
            $phpWord->addTableStyle('myTable', $tableStyle);
            $table = $section->addTable('myTable');
            $headerRow = $table->addRow();
            $headerRow->addCell(2000)->addText('Estudiante', ['bold' => true]);
            foreach ($resultados['meses'] as $mes) {
                $headerRow->addCell(1000)->addText($mes, ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            }
            $headerRow->addCell(1500)->addText('Justificadas', ['bold' => true]);
            $headerRow->addCell(1500)->addText('Injustificadas', ['bold' => true]);
            $headerRow->addCell(1000)->addText('Total', ['bold' => true]);

            foreach ($resultados['estudiantes'] as $est) {
                $row = $table->addRow();
                $row->addCell(2000)->addText($est['nombre']);
                foreach ($resultados['meses'] as $mes) {
                    $row->addCell(1000)->addText($est['detalle_mensual'][$mes] ?? '-', [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                }
                $row->addCell(1500)->addText($est['ausencias_justificadas']);
                $row->addCell(1500)->addText($est['ausencias_injustificadas']);
                $row->addCell(1000)->addText($est['total_ausencias'], ['bold' => true, 'color' => 'dc3545']);
            }

            // Tabla de llegadas tarde
            if (count($resultados['llegadasTarde']) > 0) {
                $section->addTitle('Llegadas Tarde', 2);
                $table2 = $section->addTable('myTable');
                $headerRow2 = $table2->addRow();
                $headerRow2->addCell(2000)->addText('Estudiante', ['bold' => true]);
                foreach ($resultados['meses'] as $mes) {
                    $headerRow2->addCell(1000)->addText($mes, ['bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                }
                $headerRow2->addCell(1000)->addText('Total', ['bold' => true]);
                foreach ($resultados['llegadasTarde'] as $lt) {
                    $row = $table2->addRow();
                    $row->addCell(2000)->addText($lt['nombre']);
                    foreach ($resultados['meses'] as $mes) {
                        $row->addCell(1000)->addText($lt['detalle'][$mes] ?? '-', [], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                    }
                    $row->addCell(1000)->addText($lt['total'], ['bold' => true, 'color' => 'dc3545']);
                }
            }

            $filename = 'reporte_ausencias.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'word');
            $phpWord->save($tempFile, 'Word2007');
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al exportar Word: ' . $e->getMessage());
        }
    }

    public function vistaPrevia(Request $request)
    {
        try {
            $filtros = $request->all();
            $filtros = array_merge([
                'seccion_id' => null,
                'corte_id'   => null,
                'anio'       => null,
                'desde'      => null,
                'hasta'      => null,
            ], $filtros);
            if (empty($filtros['seccion_id']) || (empty($filtros['corte_id']) && (empty($filtros['desde']) || empty($filtros['hasta'])))) {
                return response()->json(['html' => '<div class="alert alert-warning">Seleccione filtros válidos (sección y corte o rango de fechas).</div>']);
            }
            $resultados = $this->consultarAsistencias($filtros);
            $html = view('pdf.ausencias', [
                'resultados'        => $resultados,
                'filtros'           => $filtros,
                'fecha_generacion'  => now(),
            ])->render();
            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['html' => '<div class="alert alert-danger">Error al generar vista previa: ' . $e->getMessage() . '</div>']);
        }
    }
}
