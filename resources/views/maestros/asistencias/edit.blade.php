@extends('layouts.app')

@section('title', 'Editar Asistencia de Maestro')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Editar Asistencia de Maestro</h1>
        <a href="{{ route('maestros.asistencias.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('maestros.asistencias.update', $asistencia->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-semibold">Maestro</label>
                    <input type="text" class="form-control" value="{{ $asistencia->maestro->name ?? 'N/A' }}" disabled>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="{{ $asistencia->fecha }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Corte</label>
                        <select name="id_corte" class="form-select" required>
                            @foreach($cortes as $c)
                                <option value="{{ $c->id }}" {{ $asistencia->id_corte == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Estado de Asistencia</label>
                        <select name="id_tipo_asistencia" class="form-select" required>
                            @foreach($tipos as $t)
                                <option value="{{ $t->id }}" {{ $asistencia->id_tipo_asistencia == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('maestros.asistencias.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
