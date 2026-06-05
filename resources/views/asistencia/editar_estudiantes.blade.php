@extends('layouts.app')

@section('title', 'Editar Asistencia - ' . $seccion->nombre)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Asistencia - {{ $seccion->nombre }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('asistencia.index') }}">Asistencia</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-white">
            <i class="bi bi-pencil-square me-1"></i> Asistencia del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('asistencia.estudiantes.actualizar') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="fecha" value="{{ $fecha }}">
                <input type="hidden" name="id_seccion" value="{{ $seccion->id }}">
                <input type="hidden" name="id_corte" value="{{ $corteId }}">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Número de lista</th>
                                <th>Nombre completo</th>
                                <th>Asistencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historiales as $historial)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $historial->numero_lista ?? '-' }}</td>
                                <td>{{ $historial->estudiante->name }}</td>
                                <td>
                                    <select name="asistencia[{{ $historial->estudiante_id }}]" class="form-select" style="width: auto; min-width: 130px;">
                                        <option value="P" {{ $historial->asistencia_actual == 'P' ? 'selected' : '' }}>Presente</option>
                                        <option value="A" {{ $historial->asistencia_actual == 'A' ? 'selected' : '' }}>Ausente</option>
                                        <option value="J" {{ $historial->asistencia_actual == 'J' ? 'selected' : '' }}>Justificado</option>
                                        <option value="T" {{ $historial->asistencia_actual == 'T' ? 'selected' : '' }}>Llegada tarde</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar cambios
                    </button>
                    <a href="{{ route('asistencia.reporte', $fecha) }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection