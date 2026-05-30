@extends('layouts.app')

@section('title', 'Registrar Asistencia - Estudiantes')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Registrar Asistencia - Estudiantes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Nueva Asistencia</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-white">
            <i class="bi bi-funnel-fill me-1"></i> Filtros y Datos
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('asistencia.estudiantes.create') }}" class="row g-3 mb-4" id="filterForm">
                <div class="col-md-3">
                    <label for="seccion_id" class="form-label">Sección</label>
                    <select name="seccion_id" id="seccion_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Seleccionar sección --</option>
                        @foreach($secciones as $seccion)
                            <option value="{{ $seccion->id }}" {{ $seccionId == $seccion->id ? 'selected' : '' }}>
                                {{ $seccion->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Corte</label>
                    <select name="corte_id" id="corte_id" class="form-select" onchange="this.form.submit()">
                        @foreach($cortes as $corte)
                            <option value="{{ $corte->id }}" {{ $corteId == $corte->id ? 'selected' : '' }}>{{ $corte->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ $fecha }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('asistencia.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Volver a Asistencia
                    </a>
                </div>
            </form>

            @if($seccionId && $estudiantes->count())
                <form method="POST" action="{{ route('asistencia.estudiantes.store') }}">
                    @csrf
                    <input type="hidden" name="fecha" value="{{ $fecha }}">
                    <input type="hidden" name="id_corte" value="{{ $corteId }}">
                    <input type="hidden" name="id_seccion" value="{{ $seccionId }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="filtro_nombre" class="form-label">Buscar por nombre</label>
                            <input type="text" id="filtro_nombre" class="form-control" placeholder="Nombre del estudiante">
                        </div>
                        <div class="col-md-4">
                            <label for="filtro_numero" class="form-label">Buscar por número de lista</label>
                            <input type="text" id="filtro_numero" class="form-control" placeholder="Ej: 5">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tablaAsistencia">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Número de lista</th>
                                    <th>Nombre completo</th>
                                    <th>Asistencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estudiantes as $est)
                                    <tr data-numero="{{ $est->numero_lista }}" data-nombre="{{ strtolower($est->name) }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $est->numero_lista ?? '-' }}</td>
                                        <td>{{ $est->name }}</td>
                                        <td>
                                            <select name="asistencia[{{ $est->id }}]" class="form-select" style="width: auto; min-width: 130px;">
                                                <option value="P" selected>Presente</option>
                                                <option value="A">Ausente</option>
                                                <option value="J">Justificado</option>
                                                <option value="T">Llegada tarde</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Asistencia
                        </button>
                        <a href="{{ route('asistencia.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            @elseif($seccionId)
                <div class="alert alert-info">No hay estudiantes activos en esta sección.</div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de mensajes (igual que antes) -->
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

        const inputNombre = document.getElementById('filtro_nombre');
        const inputNumero = document.getElementById('filtro_numero');
        const tabla = document.getElementById('tablaAsistencia');
        if (tabla && inputNombre && inputNumero) {
            const filas = tabla.querySelectorAll('tbody tr');
            function filtrar() {
                const textoNombre = inputNombre.value.toLowerCase();
                const textoNumero = inputNumero.value.toLowerCase();
                filas.forEach(fila => {
                    const nombre = fila.dataset.nombre || '';
                    const numero = fila.dataset.numero ? fila.dataset.numero.toString() : '';
                    const coincideNombre = textoNombre === '' || nombre.includes(textoNombre);
                    const coincideNumero = textoNumero === '' || numero.includes(textoNumero);
                    fila.style.display = (coincideNombre && coincideNumero) ? '' : 'none';
                });
            }
            inputNombre.addEventListener('input', filtrar);
            inputNumero.addEventListener('input', filtrar);
        }
    });
</script>
@endsection
