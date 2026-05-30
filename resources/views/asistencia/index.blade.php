@extends('layouts.app')

@section('title', 'Asistencia')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <h1 class="h3">Asistencia</h1>
        <div>
            <a href="{{ route('asistencia.maestros.create') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-person-badge"></i> Nueva asistencia / Maestros
            </a>
            <a href="{{ route('asistencia.estudiantes.create') }}" class="btn btn-outline-success">
                <i class="bi bi-people"></i> Nueva asistencia / Estudiantes
            </a>
        </div>
    </div>

    <!-- Filtros con botón -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-white">
            <i class="bi bi-funnel-fill me-1"></i> Filtros
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('asistencia.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Sección</label>
                    <select name="seccion_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($secciones as $seccion)
                            <option value="{{ $seccion->id }}" {{ $filtros['seccion_id'] == $seccion->id ? 'selected' : '' }}>
                                {{ $seccion->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Corte</label>
                    <select name="corte_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($cortes as $corte)
                            <option value="{{ $corte->id }}" {{ $filtros['corte_id'] == $corte->id ? 'selected' : '' }}>
                                {{ $corte->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" name="desde" class="form-control" value="{{ $filtros['desde'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="{{ $filtros['hasta'] }}">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('asistencia.index') }}" class="btn btn-secondary">
                        <i class="bi bi-eraser"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de asistencias por fecha -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <i class="bi bi-table me-1"></i> Registros de asistencia por fecha
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Asistencia Real</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $reg)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($reg->fecha)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-danger">F {{ $reg->F }}</span>
                                <span class="badge bg-success ms-1">V {{ $reg->V }}</span>
                            </td>
                            <td><strong>{{ $reg->Total }}</strong></td>
                            <td>
                                <a href="{{ route('asistencia.reporte', $reg->fecha) }}" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-fecha"
                                    data-fecha="{{ $reg->fecha }}"
                                    data-corte="{{ $filtros['corte_id'] }}"
                                    data-seccion="{{ $filtros['seccion_id'] }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">No hay asistencias registradas en el período.@endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="deleteModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de mensajes (éxito/error) -->
<div class="modal fade" id="mensajeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <h5 class="modal-title" id="modalTitulo"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalMensaje"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mensajes de éxito/error
        @if(session('success_modal'))
            const modalSuccess = new bootstrap.Modal(document.getElementById('mensajeModal'));
            document.getElementById('modalTitulo').innerText = '✅ Éxito';
            document.getElementById('modalMensaje').innerText = '{{ session('success_modal') }}';
            document.getElementById('modalHeader').classList.add('bg-success', 'text-white');
            modalSuccess.show();
        @endif
        @if(session('error_modal'))
            const modalError = new bootstrap.Modal(document.getElementById('mensajeModal'));
            document.getElementById('modalTitulo').innerText = '❌ Error';
            document.getElementById('modalMensaje').innerText = '{{ session('error_modal') }}';
            document.getElementById('modalHeader').classList.add('bg-danger', 'text-white');
            modalError.show();
        @endif

        // Configurar botones de eliminar por fecha
        const deleteModal = document.getElementById('confirmDeleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteBody = document.getElementById('deleteModalBody');

        document.querySelectorAll('.btn-eliminar-fecha').forEach(btn => {
            btn.addEventListener('click', function() {
                const fecha = this.dataset.fecha;
                const corte = this.dataset.corte;
                const seccion = this.dataset.seccion;
                let url = `/asistencia/eliminar/${fecha}`;
                const params = new URLSearchParams();
                if (corte) params.append('corte_id', corte);
                if (seccion) params.append('seccion_id', seccion);
                if (params.toString()) url += '?' + params.toString();
                deleteForm.action = url;
                deleteBody.innerText = `¿Eliminar todos los registros de asistencia para la fecha ${fecha}? Esta acción no se puede deshacer.`;
                new bootstrap.Modal(deleteModal).show();
            });
        });
    });
</script>
@endsection
