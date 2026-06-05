{{-- resources/views/estudiantes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Estudiantes')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-people-fill text-primary"></i>
                Lista de Estudiantes
            </h1>
            <p class="text-muted mb-0">Gestión general de estudiantes registrados.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel"></i> Importar Excel
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle"></i> Nuevo Estudiante
            </button>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('estudiantes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Buscar estudiante..." value="{{ request('nombre') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sección</label>
                    <select name="seccion_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($secciones as $seccion)
                            <option value="{{ $seccion->id }}" {{ request('seccion_id') == $seccion->id ? 'selected' : '' }}>
                                {{ $seccion->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" {{ request('estado') == $estado->id ? 'selected' : '' }}>
                                {{ $estado->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('estudiantes.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-eraser"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre completo</th>
                            <th>N° Lista</th>
                            <th>Sección</th>
                            <th>Género</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historiales as $historial)
                            @php
                                $estudiante = $historial->estudiante;
                                $seccion = $historial->seccion;
                                $estado = $historial->estado;
                            @endphp
                            <tr>
                                <td>{{ ($historiales->currentPage() - 1) * $historiales->perPage() + $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $estudiante->name ?? 'N/A' }}</td>
                                <td>{{ $historial->numero_lista ?? '-' }}</td>
                                <td>{{ $seccion->nombre ?? 'Sin asignar' }}</td>
                                <td>
                                    @if(isset($estudiante->genero))
                                        {{ $estudiante->genero == 'M' ? 'Masculino' : ($estudiante->genero == 'F' ? 'Femenino' : '-') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($estado)
                                        <span class="badge bg-{{ $estado->nombre == 'Activo' ? 'success' : 'secondary' }}">
                                            {{ $estado->nombre }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sin estado</span>
                                    @endif
                                </td>
                               <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Botón Editar: solo si el estado permite edición (ej. Activo) --}}
                                        @if($estado && $estado->nombre == 'Activo')
                                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    data-id="{{ $estudiante->id ?? '' }}"
                                                    data-name="{{ $estudiante->name ?? '' }}"
                                                    data-numero_lista="{{ $historial->numero_lista ?? '' }}"
                                                    data-seccion="{{ $historial->seccion_id ?? '' }}"
                                                    data-genero="{{ $estudiante->genero ?? '' }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endif

                                        {{-- Botón Eliminar (desactivar): solo si el estado es Activo --}}
                                        @if($estado && $estado->nombre == 'Activo')
                                            <button class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal"
                                                    data-id="{{ $estudiante->id ?? '' }}"
                                                    data-nombre="{{ $estudiante->name ?? '' }}"
                                                    data-tipo="eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @else
                                            {{-- Botón Reactivar (cambiar estado): solo si NO es Activo --}}
                                            <button class="btn btn-sm btn-outline-success reactivar-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reactivarModal"
                                                    data-id="{{ $estudiante->id ?? '' }}"
                                                    data-nombre="{{ $estudiante->name ?? '' }}"
                                                    data-tipo="reactivar">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-database-x fs-1 d-block mb-3"></i>
                                        No se encontraron registros.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $historiales->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('estudiantes.store', request()->query()) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre completo *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Número de lista *</label>
                        <input type="number" name="numero_lista" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Sección *</label>
                        <select name="id_seccion" class="form-select" required>
                            <option value="">Seleccionar</option>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Género *</label>
                        <select name="genero" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- MODAL EDITAR --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nombre completo *</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Número de lista *</label>
                        <input type="number" name="numero_lista" id="edit_numero_lista" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label>Sección *</label>
                        <select name="id_seccion" id="edit_id_seccion" class="form-select" required>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Género *</label>
                        <select name="genero" id="edit_genero" class="form-select" required>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- MODAL IMPORTAR --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('estudiantes.import', request()->query()) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Importar estudiantes desde Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Archivo Excel (xlsx, xls, csv)</label>
                        <input type="file" name="archivo" class="form-control" required>
                    </div>
                    <div class="form-text">
                        Columnas requeridas: <strong>nombre, seccion, numero_lista, genero</strong> (M o F).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL CONFIRMAR ELIMINACIÓN (con opción de estado) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="deleteMethod" value="DELETE">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalTitle">Desactivar estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteModalBody"></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Motivo de desactivación</label>
                        <select name="estado_id" id="deleteEstadoId" class="form-select" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($estados as $estado)
                                @if(!$estado->permite_asistencia) {{-- Solo estados que NO permiten asistencia --}}
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" id="deleteModalBtn">Desactivar</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- MODAL REACTIVAR (solo estado) --}}
<div class="modal fade" id="reactivarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reactivarForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar estado del estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="reactivarModalBody"></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nuevo estado</label>
                        <select name="estado_id" id="reactivarEstadoId" class="form-select" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($estados as $estado)
                                @if($estado->nombre != 'Inactivo')
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Cambiar estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Esperar a que Bootstrap esté disponible
        function getBootstrap() {
            if (typeof window.bootstrap !== 'undefined') return window.bootstrap;
            if (typeof bootstrap !== 'undefined') return bootstrap;
            return null;
        }

        let bs = getBootstrap();
        if (!bs) {
            console.error('Bootstrap no encontrado. Asegúrate de incluir Bootstrap JS.');
            return;
        }

        // Inicializar modales manualmente si es necesario (por si los data-bs-toggle no funcionan)
        document.querySelectorAll('.modal').forEach(modalEl => {
            try {
                new bs.Modal(modalEl);
            } catch(e) {}
        });

      // --- Modal Editar: cargar datos (sin cambios) ---
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const form = document.getElementById('editForm');
        if (!form) return;
        const estudianteId = this.dataset.id;
        if (estudianteId) {
            form.action = `/estudiantes/${estudianteId}`;
        }
        document.getElementById('edit_name').value = this.dataset.name || '';
        document.getElementById('edit_numero_lista').value = this.dataset.numero_lista || '';
        document.getElementById('edit_id_seccion').value = this.dataset.seccion || '';
        document.getElementById('edit_genero').value = this.dataset.genero || '';
        // YA NO hay campo "año" ni "estado"
    });
});

// --- Modal Eliminar (desactivar) ---
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const nombre = this.dataset.nombre;
        
        document.getElementById('deleteModalTitle').innerText = 'Desactivar estudiante';
        document.getElementById('deleteModalBody').innerHTML = `¿Desea desactivar al estudiante "<strong>${nombre}</strong>"? Seleccione el motivo:`;
        
        // Limpiar select
        const estadoSelect = document.getElementById('deleteEstadoId');
        if (estadoSelect) {
            estadoSelect.value = '';
            estadoSelect.disabled = false;
            estadoSelect.required = true;
        }
        
        // Configurar el formulario
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/estudiantes/${id}`;
        document.getElementById('deleteMethod').value = 'DELETE';
    });
});

// --- Modal Reactivar (solo estado) ---
document.querySelectorAll('.reactivar-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const nombre = this.dataset.nombre;
        
        document.getElementById('reactivarModalBody').innerHTML = `Seleccione el nuevo estado para el estudiante "<strong>${nombre}</strong>". Los datos de sección y número de lista se mantendrán igual que antes de su desactivación.`;
        
        const reactivarForm = document.getElementById('reactivarForm');
        reactivarForm.action = `/estudiantes/${id}/reactivar`;
        
        const estadoSelect = document.getElementById('reactivarEstadoId');
        if (estadoSelect) estadoSelect.value = '';
    });
});


    });
</script>
@endsection