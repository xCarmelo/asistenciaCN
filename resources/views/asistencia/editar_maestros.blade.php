@extends('layouts.app')

@section('title', 'Editar Asistencia - Maestros')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Asistencia - Maestros</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('maestros.asistencias.index') }}">Asistencias Maestros</a></li>
        <li class="breadcrumb-item active">Editar 
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-white">
            <i class="bi bi-person-badge me-1"></i> Asistencia del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('asistencia.maestros.actualizar') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="fecha" value="{{ $fecha }}">
                <input type="hidden" name="id_corte" value="{{ $corteId }}">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nombre completo</th>
                                <th>Tutelado (Sección a cargo)</th>
                                <th>Asistencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($maestros as $m)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $m->name }}</td>
                                    <td>{{ $m->seccionesGuiadas->first()?->nombre ?? 'Sin tutelado' }}</td>
                                    <td>
                                        <select name="asistencia[{{ $m->id }}]" class="form-select" style="width: auto; min-width: 130px;">
                                            <option value="P" {{ $m->asistencia_actual == 'P' ? 'selected' : '' }}>Presente</option>
                                            <option value="A" {{ $m->asistencia_actual == 'A' ? 'selected' : '' }}>Ausente</option>
                                            <option value="J" {{ $m->asistencia_actual == 'J' ? 'selected' : '' }}>Justificado</option>
                                            <option value="T" {{ $m->asistencia_actual == 'T' ? 'selected' : '' }}>Llegada tarde</option>
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
                    
                </div>
            </form>
        </div>
    </div>
</div>
@endsection