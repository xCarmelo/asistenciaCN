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
                    <label class="form-label fw-semibold">Fecha (específica)</label>
                    <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Maestro</label>
                    <select name="maestro_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($maestros as $m)
                            <option value="{{ $m->id }}" {{ request('maestro_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Corte</label>
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
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Total Asistencias</th>
                            <th>Presentes</th>
                            <th>Ausentes</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultados as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $item->total }}</td>
                            <td>{{ $item->presentes }}</td>
                            <td>{{ $item->ausentes }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('asistencia.maestros.editar', $item->fecha) }}" class="btn btn-sm btn-outline-primary" title="Editar todo">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDateModal"
                                        data-fecha="{{ $item->fecha }}" data-corte="{{ $item->corte->id ?? '' }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <td><td colspan="5" class="text-center py-4">No hay asistencias registradas.@endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar por fecha -->
<div class="modal fade" id="deleteDateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar eliminación masiva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Eliminar todos los registros de asistencia para la fecha <strong id="deleteFecha"></strong>?</p>
                <p class="text-warning">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteDateForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="corte_id" id="deleteCorteId" value="">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const deleteDateModal = document.getElementById('deleteDateModal');
    deleteDateModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const fecha = button.dataset.fecha;
        const corte = button.dataset.corte;
        document.getElementById('deleteFecha').innerText = fecha;
        document.getElementById('deleteDateForm').action = `/maestros/asistencias/eliminar/${fecha}`;
        document.getElementById('deleteCorteId').value = corte;
    });
</script>
@endsection