<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ausencias</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-danger {
            color: #dc3545;
        }
        .fw-bold {
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ausencias y Llegadas Tarde</h1>
        <p>
            @if($filtros['seccion_id']) Sección: {{ $resultados['seccion']->nombre ?? 'N/A' }} | @endif
            @if($filtros['corte_id']) Corte: {{ $resultados['corte']->nombre ?? 'N/A' }} | @endif
            @if($filtros['anio']) Año: {{ $filtros['anio'] }} | @endif
            @if($filtros['desde'] && $filtros['hasta']) Rango: {{ $filtros['desde'] }} al {{ $filtros['hasta'] }} | @endif
            Fecha generación: {{ $fecha_generacion->format('d/m/Y H:i') }}
        </p>
    </div>

    <h3>Ausencias</h3>
    <table>
        <thead>
            <tr>
                <th rowspan="2">Estudiante</th>
                <th colspan="{{ count($resultados['meses']) }}" class="text-center">Ausencias por mes</th>
                <th rowspan="2">Justificadas</th>
                <th rowspan="2">Injustificadas</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                @foreach($resultados['meses'] as $mes)
                    <th class="text-center">{{ $mes }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($resultados['estudiantes'] as $est)
            <tr>
                <td class="fw-bold">{{ $est['nombre'] }}</td>
                @foreach($resultados['meses'] as $mes)
                    <td class="text-center">{{ $est['detalle_mensual'][$mes] ?? '-' }}</td>
                @endforeach
                <td>{{ $est['ausencias_justificadas'] }}</td>
                <td>{{ $est['ausencias_injustificadas'] }}</td>
                <td class="text-center fw-bold text-danger">{{ $est['total_ausencias'] }}</td>
            </tr>
            @empty
                <td><td colspan="{{ count($resultados['meses']) + 3 }}" class="text-center">No hay ausencias registradas.@endforelse
        </tbody>
    </table>

    @if(count($resultados['llegadasTarde']) > 0)
    <h3>Llegadas Tarde</h3>
    <table>
        <thead>
            <tr>
                <th>Estudiante</th>
                @foreach($resultados['meses'] as $mes)
                    <th class="text-center">{{ $mes }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultados['llegadasTarde'] as $lt)
            <tr>
                <td class="fw-bold">{{ $lt['nombre'] }}</td>
                @foreach($resultados['meses'] as $mes)
                    <td class="text-center">{{ $lt['detalle'][$mes] ?? '-' }}</td>
                @endforeach
                <td class="text-center fw-bold text-danger">{{ $lt['total'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Reporte generado automáticamente - Sistema de Control de Asistencia
    </div>
</body>
</html>
