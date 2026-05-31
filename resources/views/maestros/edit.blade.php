@extends('layouts.app')

@section('title', 'Editar Asistencia de Maestro')

@section('content')
<div class="container">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-header bg-white">
            <h4 class="mb-0">Editar Asistencia de Maestro</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('maestros.asistencias.update', $asistencia->id) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label>Maestro</label>
                    <input type="text" class="form-control" value="{{ $asistencia->maestro->name }}" disabled>
                </div>
                <div class="mb-3">
                    <label>Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ $asistencia->fecha }}" required>
                </div>
                <div class="mb-3">
                    <label>Corte</label>
                    <select name="id_corte" class="form-select" required>
                        @foreach($cortes as $c)
                            <option value="{{ $c->id }}" {{ $asistencia->id_corte == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Estado de Asistencia</label>
                    <select name="id_tipo_asistencia" class="form-select" required>
                        @foreach($tipos as $t)
                            <option value="{{ $t->id }}" {{ $asistencia->id_tipo_asistencia == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('maestros.asistencias.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection