@extends('layouts.app')

@section('title', 'Asistencias de Maestros')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Asistencias de Maestros</h1>
        <a href="{{ route('asistencia.maestros.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Asistencia
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <i class="bi bi-funnel-fill me-1"></i> Filtros
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('maestros.asistencias.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label>Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
                </div>
                <div class="col-md-3">
                    <label>Maestro</label>
                    <select name="maestro_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($maestros as $m)
                            <option value="{{ $m->id }}" {{ request('maestro_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Corte</label>
                    <select name="corte_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($cortes as $c)
                            <option value="{{ $c->id }}" {{ request('corte_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    <a href="{{ route('maestros.asistencias.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Maestro</th>
                            <th>Fecha</th>
                            <th>Corte</th>
                            <th>Asistencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asistencias as $a)
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>{{ $a->maestro->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($a->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $a->corte->nombre ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $estado = $a->tipoAsistencia->nombre ?? ($a->justificado ? 'Justificado' : ($a->asis == 'P' ? 'Presente' : 'Ausente'));
                                @endphp
                                <span class="badge bg-secondary">{{ $estado }}</span>
                            </td>
                            <td>
                                <a href="{{ route('maestros.asistencias.edit', $a->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('maestros.asistencias.destroy', $a->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta asistencia?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No hay asistencias registradas.@endforelse
                    </tbody>
                </table>
            </div>
            {{ $asistencias->links() }}
        </div>
    </div>
</div>
@endsection